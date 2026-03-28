<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SupabaseJwtVerifier
{
    /**
     * Verify a Supabase access token (JWT) using the project's JWKS.
     * Returns claims array on success, or null on failure.
     */
    public function verify(string $jwt): ?array
    {
        $jwt = trim($jwt);
        if ($jwt === '') return null;

        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;

        [$h64, $p64, $s64] = $parts;
        $header = $this->jsonDecode($this->b64UrlDecode($h64));
        $payload = $this->jsonDecode($this->b64UrlDecode($p64));
        $signature = $this->b64UrlDecode($s64);

        if (!is_array($header) || !is_array($payload) || !is_string($signature)) {
            return null;
        }

        if (($header['alg'] ?? null) !== 'RS256') {
            return null;
        }

        $kid = $header['kid'] ?? null;
        if (!is_string($kid) || $kid === '') {
            return null;
        }

        $jwk = $this->getJwkByKid($kid);
        if (!$jwk) {
            return null;
        }

        $pem = $this->jwkToPem($jwk);
        if (!$pem) {
            return null;
        }

        $data = $h64 . '.' . $p64;
        $ok = openssl_verify($data, $signature, $pem, OPENSSL_ALGO_SHA256);
        if ($ok !== 1) {
            return null;
        }

        // Basic claim checks (minimal, but sane defaults)
        $now = time();
        if (isset($payload['exp']) && is_numeric($payload['exp']) && (int)$payload['exp'] < $now - 30) {
            return null;
        }

        $issExpected = config('supabase.issuer')
            ?: rtrim((string) config('supabase.url'), '/') . '/auth/v1';

        if (isset($payload['iss']) && $payload['iss'] !== $issExpected) {
            return null;
        }

        $audExpected = config('supabase.audience');
        if ($audExpected) {
            $aud = $payload['aud'] ?? null;
            $audOk = false;
            if (is_string($aud) && $aud === $audExpected) $audOk = true;
            if (is_array($aud) && in_array($audExpected, $aud, true)) $audOk = true;
            if (!$audOk) return null;
        }

        return $payload;
    }

    private function getJwkByKid(string $kid): ?array
    {
        $jwks = $this->getJwks();
        $keys = $jwks['keys'] ?? [];
        if (!is_array($keys)) return null;

        foreach ($keys as $k) {
            if (!is_array($k)) continue;
            if (($k['kid'] ?? null) === $kid) {
                return $k;
            }
        }
        return null;
    }

    private function getJwks(): array
    {
        $cacheSeconds = (int) config('supabase.jwks_cache_seconds', 3600);

        return Cache::remember('supabase_jwks', max(60, $cacheSeconds), function () {
            $url = config('supabase.jwks_url');
            if (!$url) {
                $base = rtrim((string) config('supabase.url'), '/');
                $url = $base . '/auth/v1/.well-known/jwks.json';
            }

            $res = Http::timeout(10)->get($url);
            if (!$res->ok()) {
                return ['keys' => []];
            }

            $json = $res->json();
            return is_array($json) ? $json : ['keys' => []];
        });
    }

    /**
     * Convert RSA JWK to PEM public key.
     */
    private function jwkToPem(array $jwk): ?string
    {
        if (($jwk['kty'] ?? null) !== 'RSA') return null;
        $n = $jwk['n'] ?? null;
        $e = $jwk['e'] ?? null;
        if (!is_string($n) || !is_string($e)) return null;

        $modulus = $this->b64UrlDecode($n);
        $exponent = $this->b64UrlDecode($e);
        if (!is_string($modulus) || !is_string($exponent)) return null;

        // ASN.1 DER encoding for RSA public key: SEQUENCE { modulus INTEGER, exponent INTEGER }
        $modulusEnc = $this->asn1Integer($modulus);
        $exponentEnc = $this->asn1Integer($exponent);
        $rsaPubKey = $this->asn1Sequence($modulusEnc . $exponentEnc);

        // Wrap in SubjectPublicKeyInfo
        $algoId = $this->asn1Sequence(
            "\x06\x09\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01" . "\x05\x00" // rsaEncryption + NULL
        );
        $bitString = "\x03" . $this->asn1Length(strlen($rsaPubKey) + 1) . "\x00" . $rsaPubKey;
        $spki = $this->asn1Sequence($algoId . $bitString);

        $pem = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($spki), 64, "\n") .
            "-----END PUBLIC KEY-----\n";

        return $pem;
    }

    private function asn1Integer(string $bytes): string
    {
        // Ensure positive INTEGER: if highest bit set, prefix 0x00
        if ($bytes !== '' && (ord($bytes[0]) & 0x80)) {
            $bytes = "\x00" . $bytes;
        }
        return "\x02" . $this->asn1Length(strlen($bytes)) . $bytes;
    }

    private function asn1Sequence(string $bytes): string
    {
        return "\x30" . $this->asn1Length(strlen($bytes)) . $bytes;
    }

    private function asn1Length(int $len): string
    {
        if ($len < 0x80) {
            return chr($len);
        }

        $tmp = '';
        while ($len > 0) {
            $tmp = chr($len & 0xFF) . $tmp;
            $len >>= 8;
        }
        return chr(0x80 | strlen($tmp)) . $tmp;
    }

    private function b64UrlDecode(string $data): ?string
    {
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $pad = strlen($data) % 4;
        if ($pad) {
            $data .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($data, true);
        return $decoded === false ? null : $decoded;
    }

    private function jsonDecode(?string $json): ?array
    {
        if (!is_string($json)) return null;
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : null;
    }
}
