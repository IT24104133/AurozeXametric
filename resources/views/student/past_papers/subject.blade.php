@extends('layouts.student')

@section('title', 'Subject - Past Papers')

@section('page_title', 'Past Papers')
@section('page_subtitle', 'Browse and start past papers')

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

    <hr class="my-3 border-slate-200">

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-full text-slate-700 hover:bg-red-50 hover:text-red-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </button>
    </form>
@endsection

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('student.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('student.past_papers.home') }}" class="ml-1 text-gray-700 hover:text-gray-900">Past Papers</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-700 font-medium">{{ $subject->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
        
        {{-- Back Button --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('student.past_papers.streams', ['stream' => $stream]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back to {{ streamLabel($stream) }} Subjects</span>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">{{ $subject->name }}</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Browse and practice papers from this subject</p>
            </div>
        </div>

        {{-- No Questions SweetAlert --}}
        @if(session('no_questions'))
            <div id="noQuestionsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-100 mb-4">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 mb-2">
                        This paper has no questions yet
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">
                        {{ session('no_questions') }}
                    </p>

                    <button id="noQuestionsOk"
                            class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        OK
                    </button>
                </div>
            </div>

            <script>
                (function () {
                    var baseUrl = "{{ route('student.past_papers.streams', ['stream' => $stream]) }}";
                    var okBtn = document.getElementById('noQuestionsOk');
                    var modal = document.getElementById('noQuestionsModal');

                    var closeModal = function () {
                        if (modal) {
                            modal.remove();
                        }
                    };

                    var redirect = function () {
                        window.location.href = baseUrl;
                    };

                    if (okBtn) {
                        okBtn.addEventListener('click', redirect);
                    }

                    setTimeout(function () {
                        closeModal();
                    }, 5000);
                })();
            </script>
        @endif

        {{-- Source Section --}}
        <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            {{-- Section Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-900">{{ $sourceLabel }}</h3>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">{{ $badgeLabel }}</span>
            </div>

            {{-- Section Content --}}
            <div class="p-6">
                @if($papers->isEmpty())
                    {{-- Empty State --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-8 text-center">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-sm font-medium text-slate-600">No papers available yet</p>
                        <p class="text-xs text-slate-500 mt-1">Coming soon</p>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($papers as $paper)
                            @if($paper->category === 'free_style')
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden relative group">
                                    
                                    {{-- Watermark Logo - Bottom Right --}}
                                    <div class="absolute bottom-4 right-4 flex items-center justify-center opacity-10 pointer-events-none select-none z-0">
                                        <img src="{{ asset('logo.png') }}" alt="" class="w-40 h-40 object-contain">
                                    </div>

                                    {{-- Card Content - Higher z-index --}}
                                    <div class="relative z-10 flex flex-col h-full">
                                        {{-- Card Header Strip --}}
                                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 px-4 pt-4 pb-3">
                                            {{-- Title --}}
                                            <h4 class="text-sm font-bold text-slate-900 mb-0.5">
                                                @if($paper->title)
                                                    {{ $paper->title }}
                                                @else
                                                    {{ $subject->name }} - Free Style
                                                @endif
                                            </h4>
                                            <p class="text-xs text-slate-600">Duration: {{ $paper->duration_minutes }} mins</p>
                                            <p class="text-xs text-slate-600">
                                                Questions: {{ $paper->total_questions ?? 40 }}
                                            </p>
                                        </div>

                                        {{-- Card Body --}}
                                        <div class="p-4 flex-1 flex flex-col">
                                            {{-- Attempts Count Badge --}}
                                            <div class="mb-3">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                                    Attempts: {{ $paper->attempts_count }}
                                                </span>
                                            </div>

                                            {{-- Last Score --}}
                                            <div class="mb-4 px-3 py-3 rounded-lg bg-slate-50">
                                                <p class="text-xs text-slate-600 mb-1">Last Score</p>
                                                @if($paper->attempts_count > 0)
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-2xl font-bold text-indigo-600">{{ (int)($paper->lastAttempt->percentage ?? 0) }}%</span>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-slate-500 font-medium">Not attempted</p>
                                                @endif
                                            </div>

                                            {{-- Mode Selection for Free Style --}}
                                            <div class="mb-4 space-y-2">
                                                <p class="text-xs font-semibold text-slate-700">Select Mode:</p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <a href="{{ route('student.past_papers.start', $paper->id) }}?mode=ultra_easy"
                                                       class="px-3 py-2 text-xs font-bold text-center rounded-lg border border-green-300 bg-green-50 text-green-700 hover:bg-green-100 transition">
                                                        Easy
                                                    </a>
                                                    <a href="{{ route('student.past_papers.start', $paper->id) }}?mode=ultra_medium"
                                                       class="px-3 py-2 text-xs font-bold text-center rounded-lg border border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                                                        Normal
                                                    </a>
                                                    <a href="{{ route('student.past_papers.start', $paper->id) }}?mode=ultra_hard"
                                                       class="px-3 py-2 text-xs font-bold text-center rounded-lg border border-red-300 bg-red-50 text-red-700 hover:bg-red-100 transition col-span-2">
                                                        Hard
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('student.past_papers.start', $paper->id) }}"
                                   class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 overflow-hidden relative group flex flex-col">
                                    
                                    {{-- Watermark Logo - Bottom Right --}}
                                    <div class="absolute bottom-4 right-4 flex items-center justify-center opacity-10 pointer-events-none select-none z-0">
                                        <img src="{{ asset('logo.png') }}" alt="" class="w-40 h-40 object-contain">
                                    </div>

                                    {{-- Card Content - Higher z-index --}}
                                    <div class="relative z-10 flex flex-col h-full">
                                        {{-- Card Header Strip --}}
                                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 px-4 pt-4 pb-3">
                                            {{-- Title --}}
                                            <h4 class="text-sm font-bold text-slate-900 mb-0.5">
                                                @if($paper->year)
                                                    {{ $subject->name }} - {{ $paper->year }}
                                                @elseif($paper->title)
                                                    {{ $paper->title }}
                                                @else
                                                    Paper #{{ $paper->id }}
                                                @endif
                                            </h4>
                                            <p class="text-xs text-slate-600">Duration: {{ $paper->duration_minutes }} mins</p>
                                            <p class="text-xs text-slate-600">
                                                Questions: {{ $paper->questions_count }}
                                            </p>
                                        </div>

                                        {{-- Card Body --}}
                                        <div class="p-4 flex-1 flex flex-col">
                                            {{-- Attempts Count Badge --}}
                                            <div class="mb-3">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                                    Attempts: {{ $paper->attempts_count }}
                                                </span>
                                            </div>

                                            {{-- Last Score --}}
                                            <div class="mb-4 px-3 py-3 rounded-lg bg-slate-50">
                                                <p class="text-xs text-slate-600 mb-1">Last Score</p>
                                                @if($paper->attempts_count > 0)
                                                    <div class="flex items-baseline gap-2">
                                                        <span class="text-2xl font-bold text-indigo-600">{{ (int)($paper->lastAttempt->percentage ?? 0) }}%</span>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-slate-500 font-medium">Not attempted</p>
                                                @endif
                                            </div>

                                            {{-- CTA Button --}}
                                            <button class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 group-hover:shadow-md transition mt-auto">
                                                <span>
                                                    @if($paper->attempts_count > 0)
                                                        Retake
                                                    @else
                                                        Start
                                                    @endif
                                                </span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
