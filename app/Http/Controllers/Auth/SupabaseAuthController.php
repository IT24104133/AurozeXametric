<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SupabaseJwtVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SupabaseAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.supabase_login', [
            'supabaseUrl' => config('supabase.url'),
            'supabaseAnonKey' => config('supabase.anon_key'),
        ]);
    }

    /**
     * Browser posts a Supabase access token here.
     * We verify the token and then create a normal Laravel session.
     */
    public function exchangeToken(Request $request, SupabaseJwtVerifier $verifier): RedirectResponse
    {
        $data = $request->validate([
            'access_token' => ['required', 'string'],
            'next' => ['nullable', 'string'],
        ]);

        $claims = $verifier->verify($data['access_token']);
        if (!$claims) {
            return redirect()->route('login')->withErrors(['email' => 'Login failed. Invalid token.']);
        }

        $supabaseId = $claims['sub'] ?? null;
        $email = $claims['email'] ?? null;

        if (!is_string($supabaseId) || $supabaseId === '') {
            return redirect()->route('login')->withErrors(['email' => 'Login failed. Missing user id.']);
        }

        $user = User::where('supabase_id', $supabaseId)->first();

        // If first time: try matching by email (if your app has existing email users)
        if (!$user && is_string($email) && $email !== '') {
            $user = User::where('email', $email)->first();
        }

        // Create local user if none found
        if (!$user) {
            $name = (string) ($claims['user_metadata']['full_name'] ?? $claims['user_metadata']['name'] ?? $email ?? 'Student');
            $studentId = $this->generateNextStudentId('NGI', 4);

            $user = User::create([
                'supabase_id' => $supabaseId,
                'name' => $name,
                'full_name' => $name,
                'email' => is_string($email) && $email !== '' ? $email : null,
                'role' => 'student',
                'student_id' => $studentId,
                // Local password is not used when Supabase is identity provider
                'password' => Hash::make(str()->random(32)),
            ]);
        } else {
            // Ensure supabase_id is stored
            if ($user->supabase_id !== $supabaseId) {
                $user->supabase_id = $supabaseId;
            }

            // Sync email/name if present
            if (is_string($email) && $email !== '' && $user->email !== $email) {
                $user->email = $email;
            }

            $name = (string) ($claims['user_metadata']['full_name'] ?? $claims['user_metadata']['name'] ?? $user->name);
            if ($name && $user->name !== $name) {
                $user->name = $name;
                $user->full_name = $user->full_name ?: $name;
            }

            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $next = $data['next'] ?? '/';
        if (!is_string($next) || $next === '') $next = '/';

        return redirect()->intended($next);
    }

    /**
     * OAuth returns to this page.
     * JS on this page extracts the session and posts the access_token to exchangeToken.
     */
    public function callback(Request $request): View
    {
        return view('auth.supabase_callback', [
            'supabaseUrl' => config('supabase.url'),
            'supabaseAnonKey' => config('supabase.anon_key'),
            'next' => $request->query('next', '/'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function generateNextStudentId(string $prefix = 'NGI', int $pad = 4): string
    {
        $latest = User::where('student_id', 'like', $prefix . '%')
            ->orderBy('student_id', 'desc')
            ->value('student_id');

        if (!$latest) {
            return $prefix . str_pad('1', $pad, '0', STR_PAD_LEFT);
        }

        $numPart = preg_replace('/\D+/', '', $latest);
        $num = (int) ($numPart ?: 0);
        $next = $num + 1;

        $digits = max($pad, strlen((string) $next));
        return $prefix . str_pad((string) $next, $digits, '0', STR_PAD_LEFT);
    }
}
