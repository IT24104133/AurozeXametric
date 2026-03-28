<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ REQUIRED for fetch() / Alpine submit -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <!-- ✅ x-cloak support -->
    <style>[x-cloak]{ display:none !important; }</style>

    <!-- ✅ FIXED ORDER: Alpine FIRST, then plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

<div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50 flex">

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:block w-72 shrink-0">
        <div class="h-full sticky top-0 p-6">
            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3 px-2 pb-4 border-b border-slate-100">
                    <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-sm">
                        <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
                    </div>
                    <div>
                        <div class="text-xs font-bold tracking-widest text-teal-700">EXAMPORTAL</div>
                        <div class="text-sm font-extrabold text-slate-900">Student Panel</div>
                    </div>
                </div>

                <nav class="mt-4 space-y-2">
                    @yield('sidebar-nav')
                </nav>

                <div class="mt-6 pt-4 border-t border-slate-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition"
                        >
                            Logout
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Column -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Bar -->
        <header class="shrink-0 sticky top-0 z-20 bg-slate-50/80 backdrop-blur border-b border-slate-200">
            <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between">

                <!-- Left: Hamburger (mobile) + Page title -->
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-100 transition"
                        @click="sidebarOpen = true"
                        aria-label="Open menu"
                    >
                        <svg class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div>
                        @yield('breadcrumbs')
                    </div>
                </div>

                <!-- Right: Profile -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:block text-right">
                        <div class="text-sm font-extrabold text-slate-900">
                            {{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ auth()->user()->email ?? auth()->user()->student_id ?? '' }}
                        </div>
                    </div>

                    <div class="h-10 w-10 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-extrabold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>

            </div>
        </header>

        <!-- Mobile Sidebar Drawer -->
        <div class="lg:hidden">
            <!-- Overlay -->
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-black/40"
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            <!-- Drawer -->
            <aside
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white border-r border-slate-200 p-5"
                x-cloak
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-sm">
                            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
                        </div>
                        <div>
                            <div class="text-xs font-bold tracking-widest text-teal-700">EXAMPORTAL</div>
                            <div class="text-sm font-extrabold text-slate-900">Student Panel</div>
                        </div>
                    </div>

                    <button
                        class="h-10 w-10 rounded-xl hover:bg-slate-100 transition flex items-center justify-center"
                        @click="sidebarOpen = false"
                        aria-label="Close menu"
                    >
                        ✕
                    </button>
                </div>

                <nav class="mt-6 space-y-2">
                    @yield('mobile-nav')
                </nav>

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition"
                        >
                            Logout
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

</div>
</body>
</html>
