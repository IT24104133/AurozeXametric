@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('sidebar-nav')
    @php
        $isDashboard = request()->routeIs('student.dashboard');
        $isExams = request()->routeIs('student.exams.*');
        $isPastPapers = request()->routeIs('student.past_papers.*');
        $isResults = request()->routeIs('student.results.*');

        $navBase = "flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-full transition";
        $navActive = "bg-teal-50 text-teal-700 border border-teal-200";
        $navIdle = "text-slate-700 hover:bg-slate-50";
        $iconActive = "text-teal-700";
        $iconIdle = "text-slate-500";
    @endphp

    <a href="{{ route('student.dashboard') }}"
       class="{{ $navBase }} {{ $isDashboard ? $navActive : $navIdle }}">
        <svg class="w-5 h-5 {{ $isDashboard ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('student.exams.index') }}"
       class="{{ $navBase }} {{ $isExams ? $navActive : $navIdle }}">
        <svg class="w-5 h-5 {{ $isExams ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Exams
    </a>

    <a href="{{ route('student.past_papers.home') }}"
       class="{{ $navBase }} {{ $isPastPapers ? $navActive : $navIdle }}">
        <svg class="w-5 h-5 {{ $isPastPapers ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Past Papers
    </a>

    <a href="{{ route('student.results.index') }}"
       class="{{ $navBase }} {{ $isResults ? $navActive : $navIdle }}">
        <svg class="w-5 h-5 {{ $isResults ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Results
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="{{ $navBase }} {{ $navIdle }} w-full text-left hover:bg-red-50 hover:text-red-600">
            <svg class="w-5 h-5 {{ $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/>
            </svg>
            Logout
        </button>
    </form>
@endsection

@section('mobile-nav')
    @yield('sidebar-nav')
@endsection

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview')

@section('content')

@include('components.student-onboarding-modal')

@php
    $studentName = auth()->user()->full_name ?? auth()->user()->name ?? auth()->user()->student_id;

    $minutesRemaining = null;
    if(isset($continueAttempt) && $continueAttempt && $continueAttempt->ends_at) {
        $minutesRemaining = now()->diffInMinutes($continueAttempt->ends_at, false);
        if ($minutesRemaining < 0) $minutesRemaining = 0;
    }
@endphp

<!-- Hero Strip -->
<div class="mb-8 rounded-3xl bg-gradient-to-r from-teal-600 to-sky-500 text-white shadow-sm p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <h2 class="text-2xl sm:text-3xl font-extrabold">Welcome back, {{ $studentName }}!</h2>
            <p class="mt-2 text-white/90">Your progress is right here. Keep building momentum every day.</p>
            <a href="{{ route('student.exams.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white text-teal-700 font-extrabold shadow hover:bg-white/90">
                Go to Exams
                <span aria-hidden="true">→</span>
            </a>
        </div>
        <div class="rounded-2xl bg-white/15 border border-white/30 p-4">
            <div class="text-xs uppercase tracking-widest text-white/80">Tip</div>
            <div class="mt-2 font-extrabold">Practice daily to increase ranking</div>
            <div class="mt-2 text-sm text-white/90">Short sessions, steady progress.</div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-6 mb-10 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Available Exams</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $availableCount ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Exams Attempted</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $attemptedCount ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Published Results</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $publishedResultsCount ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Total Coins</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $totalCoins ?? 0 }}</p>
            <p class="mt-1 text-xs text-slate-500">Today +{{ $todayCoins ?? 0 }} • Remaining {{ max($remainingCoinsToday ?? 0, 0) }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2m6-6a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-900">Quick Actions</h3>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('student.exams.index') }}"
           class="flex items-center justify-between px-4 py-3 rounded-2xl bg-gradient-to-r from-sky-600 to-teal-600 text-white font-semibold shadow hover:opacity-95">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Start New Exam
            </span>
            <span class="text-white/80">→</span>
        </a>

        <a href="{{ route('student.past_papers.home') }}"
              class="flex items-center justify-between px-4 py-3 rounded-2xl border border-slate-200 text-slate-800 font-semibold bg-white hover:bg-slate-50">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Past Papers
            </span>
            <span class="text-slate-400">→</span>
        </a>

        <a href="{{ route('student.results.index') }}"
              class="flex items-center justify-between px-4 py-3 rounded-2xl border border-slate-200 text-slate-800 font-semibold bg-white hover:bg-slate-50">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                View Results
            </span>
            <span class="text-slate-400">→</span>
        </a>
    </div>
</div>

<!-- Lists -->
<div class="grid grid-cols-1 gap-6 mt-10 lg:grid-cols-2">
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Available Exams</h3>
            <a href="{{ route('student.exams.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800">View All →</a>
        </div>
        @if(isset($availableExamsList) && $availableExamsList->count())
            <div class="space-y-3">
                @foreach($availableExamsList as $exam)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3 hover:bg-slate-50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-900 truncate">{{ $exam->title }}</div>
                            <div class="text-xs text-slate-500">{{ $exam->duration_minutes }} min</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-slate-500">Available</span>
                            <a href="{{ route('student.exams.start', $exam->id) }}" class="text-xs font-bold text-teal-700 hover:text-teal-800">Start →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-500">No exams available right now.</p>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Latest Results</h3>
            <a href="{{ route('student.results.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800">View All →</a>
        </div>
        @if($latestResults->isEmpty())
            <p class="text-sm text-slate-500">No published results yet.</p>
        @else
            <div class="space-y-3">
                @foreach($latestResults as $attempt)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3 hover:bg-slate-50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-900 truncate">{{ $attempt->exam->title }}</div>
                            <div class="text-xs text-slate-500">Score: {{ $attempt->score ?? 0 }}/{{ $attempt->total_questions }}</div>
                        </div>
                        <a href="{{ route('student.exams.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id, 'return' => request()->getRequestUri()]) }}"
                           class="text-xs font-bold text-teal-700 hover:text-teal-800">View →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection


