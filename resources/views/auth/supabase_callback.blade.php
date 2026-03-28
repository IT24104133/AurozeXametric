<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Signing in...</title>
  @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-950 text-gray-100 flex items-center justify-center p-6">
  <div class="w-full max-w-md bg-gray-900/60 border border-gray-800 rounded-2xl p-6 shadow-xl">
    <h1 class="text-xl font-semibold mb-2">Finishing sign in…</h1>
    <p class="text-gray-400" id="status">Please wait.</p>
  </div>

  <script type="module">
    import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm'

    const supabaseUrl = @json($supabaseUrl);
    const supabaseAnonKey = @json($supabaseAnonKey);
    const next = @json($next ?? '/');

    const status = document.getElementById('status');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const supabase = createClient(supabaseUrl, supabaseAnonKey);

    async function postToken(access_token) {
      const res = await fetch(@json(route('supabase.exchange')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ access_token, next })
      });

      if (res.redirected) {
        location.href = res.url;
        return;
      }

      if (res.ok) {
        location.href = next || '/';
        return;
      }

      const txt = await res.text();
      throw new Error(txt || 'Token exchange failed');
    }

    // Supabase OAuth returns tokens in the URL (sometimes as a hash fragment)
    // Supabase JS can parse it for us.
    (async () => {
      try {
        status.textContent = 'Reading session…';
        const { data, error } = await supabase.auth.getSessionFromUrl({ storeSession: false });
        if (error) throw error;

        const access_token = data?.session?.access_token;
        if (!access_token) throw new Error('No access token returned.');

        status.textContent = 'Creating app session…';
        await postToken(access_token);
      } catch (e) {
        console.error(e);
        status.textContent = 'Login failed. Please go back and try again.';
      }
    })();
  </script>
</body>
</html>
