<?php

return [
    // Your Supabase project URL (e.g. https://xxxx.supabase.co)
    'url' => env('SUPABASE_URL'),

    // Public anon key (safe to use in the browser)
    'anon_key' => env('SUPABASE_ANON_KEY'),

    // Expected issuer for tokens (defaults to "{SUPABASE_URL}/auth/v1")
    'issuer' => env('SUPABASE_JWT_ISSUER'),

    // JWKS endpoint (defaults to "{SUPABASE_URL}/auth/v1/.well-known/jwks.json")
    'jwks_url' => env('SUPABASE_JWKS_URL'),

    // Optional: enforce audience claim (Supabase commonly uses "authenticated")
    'audience' => env('SUPABASE_JWT_AUD', 'authenticated'),

    // Cache time for JWKS (seconds)
    'jwks_cache_seconds' => (int) env('SUPABASE_JWKS_CACHE_SECONDS', 3600),
];
