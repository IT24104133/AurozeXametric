@extends('layouts.dashboard')

@section('title', 'System Health')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">System Health</h1>
                <p class="text-slate-600 mt-1">Monitor application and infrastructure status</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- System Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-600">PHP Version</p>
                        <p class="text-xl font-bold text-slate-800">{{ $systemInfo['php_version'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Laravel Version</p>
                        <p class="text-xl font-bold text-slate-800">{{ $systemInfo['laravel_version'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-600">Environment</p>
                        <p class="text-xl font-bold text-slate-800 capitalize">{{ $systemInfo['environment'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Checks -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-50 to-sky-50 px-6 py-4 border-b border-slate-200">
                <h2 class="text-xl font-bold text-slate-800">Health Checks</h2>
            </div>

            <!-- Checks List -->
            <div class="divide-y divide-slate-200">
                <!-- App Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Application</p>
                                <p class="text-sm text-slate-600">{{ $checks['app']['message'] }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['app']['status']) }}
                        </span>
                    </div>
                </div>

                <!-- Database Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Database</p>
                                <p class="text-sm text-slate-600">{{ $checks['database']['message'] }}</p>
                                @if(isset($checks['database']['driver']))
                                    <p class="text-xs text-slate-500 mt-0.5">Driver: {{ $checks['database']['driver'] }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 {{ $checks['database']['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['database']['status']) }}
                        </span>
                    </div>
                </div>

                <!-- Cache Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Cache</p>
                                <p class="text-sm text-slate-600">{{ $checks['cache']['message'] }}</p>
                                @if(isset($checks['cache']['driver']))
                                    <p class="text-xs text-slate-500 mt-0.5">Driver: {{ $checks['cache']['driver'] }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 {{ $checks['cache']['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['cache']['status']) }}
                        </span>
                    </div>
                </div>

                <!-- Session Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Session</p>
                                <p class="text-sm text-slate-600">{{ $checks['session']['message'] }}</p>
                                @if(isset($checks['session']['driver']))
                                    <p class="text-xs text-slate-500 mt-0.5">Driver: {{ $checks['session']['driver'] }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['session']['status']) }}
                        </span>
                    </div>
                </div>

                <!-- Queue Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Queue</p>
                                <p class="text-sm text-slate-600">{{ $checks['queue']['message'] }}</p>
                                @if(isset($checks['queue']['driver']))
                                    <p class="text-xs text-slate-500 mt-0.5">Driver: {{ $checks['queue']['driver'] }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['queue']['status']) }}
                        </span>
                    </div>
                </div>

                <!-- Storage Check -->
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-50 to-sky-50 rounded-xl flex items-center justify-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Storage</p>
                                <p class="text-sm text-slate-600">{{ $checks['storage']['message'] }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 {{ $checks['storage']['status'] === 'ok' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} rounded-full text-sm font-semibold">
                            {{ strtoupper($checks['storage']['status']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
