<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Exam Portal') }}</title>

    {{-- ✅ REQUIRED: x-cloak support (hides Alpine sections until Alpine loads) --}}
    <style>[x-cloak]{ display:none !important; }</style>

    {{-- ✅ OPTIONAL but recommended: Alpine Focus plugin (removes your x-trap warning) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    {{-- ✅ REQUIRED: AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50">
    @yield('content')
</body>
</html>
