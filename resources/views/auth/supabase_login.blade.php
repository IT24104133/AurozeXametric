<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Login</title>
  @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-950 text-gray-100 flex items-center justify-center p-6">
  <div class="w-full max-w-md bg-gray-900/60 border border-gray-800 rounded-2xl p-6 shadow-xl">
    <h1 class="text-2xl font-semibold mb-2">Sign in</h1>
    <p class="text-gray-400 mb-6">Use your email/password or Google.</p>

    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-red-800 bg-red-950/40 p-3 text-red-200 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form id="emailForm" class="space-y-3">
      <div>
        <label class="block text-sm text-gray-300 mb-1">Email</label>
        <input id="email" type="email" class="w-full rounded-lg bg-gray-950 border border-gray-800 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500" required />
      </div>
      <div>
        <label class="block text-sm text-gray-300 mb-1">Password</label>
        <input id="password" type="password" class="w-full rounded-lg bg-gray-950 border border-gray-800 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500" required />
      </div>
      <button type="submit" class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 transition px-4 py-2 font-medium">Sign in</button>
    </form>

    <div class="my-5 flex items-center gap-3">
      <div class="h-px bg-gray-800 flex-1"></div>
      <div class="text-xs text-gray-500">OR</div>
      <div class="h-px bg-gray-800 flex-1"></div>
    </div>

    <button id="googleBtn" class="w-full rounded-lg bg-white text-gray-900 hover:bg-gray-100 transition px-4 py-2 font-semibold">
      Continue with Google
    </button>

    <p id="status" class="mt-4 text-sm text-gray-400"></p>
  </div>

  <script type="module">
    import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm'

    const supabaseUrl = @json($supabaseUrl);
    const supabaseAnonKey = @json($supabaseAnonKey);

    if (!supabaseUrl || !supabaseAnonKey) {
      document.getElementById('status').textContent = 'Supabase is not configured (SUPABASE_URL / SUPABASE_ANON_KEY).';
      throw new Error('Supabase not configured');
    }

    const supabase = createClient(supabaseUrl, supabaseAnonKey);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    async function postToken(access_token) {
      const res = await fetch(@json(route('supabase.exchange')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          access_token,
          next: new URLSearchParams(location.search).get('next') || '/'
        })
      });

      // Laravel may respond with redirect HTML if not expecting JSON
      // So we just navigate to intended location on success.
      if (res.redirected) {
        location.href = res.url;
        return;
      }

      if (res.ok) {
        location.href = new URLSearchParams(location.search).get('next') || '/';
        return;
      }

      const txt = await res.text();
      throw new Error(txt || 'Token exchange failed');
    }

    document.getElementById('emailForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const status = document.getElementById('status');
      status.textContent = 'Signing in...';

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      const { data, error } = await supabase.auth.signInWithPassword({ email, password });
      if (error) {
        status.textContent = error.message;
        return;
      }

      const access_token = data?.session?.access_token;
      if (!access_token) {
        status.textContent = 'No access token returned.';
        return;
      }

      await postToken(access_token);
    });

    document.getElementById('googleBtn').addEventListener('click', async () => {
      const next = new URLSearchParams(location.search).get('next') || '/';
      await supabase.auth.signInWithOAuth({
        provider: 'google',
        options: {
          redirectTo: `${location.origin}${@json(route('supabase.callback'))}?next=${encodeURIComponent(next)}`
        }
      });
    });
  </script>
</body>
</html>
