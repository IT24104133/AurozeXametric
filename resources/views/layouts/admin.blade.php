<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js for growth charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">

    <nav class="bg-black text-white p-4">
        <div class="max-w-7xl mx-auto flex justify-between">
            <span class="font-bold">Exam Portal – Admin</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="underline">Logout</button>
            </form>
        </div>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
