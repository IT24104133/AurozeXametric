@extends('layouts.student')

@section('title', 'Past Papers')

@section('page_title', 'Past Papers')
@section('page_subtitle', 'Practice with previous years\' papers')

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
                    <span class="ml-1 text-gray-700 font-medium">Past Papers</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
        
        {{-- Page Title --}}
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900">Past Papers</h2>
            <p class="text-sm text-slate-600 mt-1">Practice with previous years' papers to prepare for exams</p>
        </div>

        {{-- Education Department Section --}}
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-teal-100 text-teal-700 font-semibold mr-3 text-base">
                        📚
                    </span>
                    Education Department
                </h2>
                <p class="text-sm text-slate-600 mt-1">Official exam papers from the education department</p>
            </div>

            @if(count($eduSubjects) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($eduSubjects as $subject)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col">
                            {{-- Card Header --}}
                            <div class="bg-gradient-to-r from-teal-50 to-sky-50 px-4 py-4">
                                <h3 class="text-base font-extrabold text-slate-900 mb-1 line-clamp-2">
                                    {{ $subject['name'] }}
                                </h3>
                                <p class="text-sm text-slate-600">
                                    {{ $subject['total_papers'] }} paper{{ $subject['total_papers'] !== 1 ? 's' : '' }}
                                </p>
                            </div>

                            {{-- Card Body --}}
                            <div class="flex-1 px-4 py-4">
                                {{-- Progress Bar --}}
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-slate-700">Progress</span>
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-teal-100 text-teal-700 font-bold text-sm border border-teal-200">
                                            {{ $subject['average_percentage'] }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ $subject['average_percentage'] }}%">
                                        </div>
                                    </div>
                                </div>

                                {{-- Completion Stats --}}
                                <div class="flex items-center gap-1 px-3 py-2 bg-slate-50 rounded-lg">
                                    <span class="text-sm text-slate-700">
                                        <span class="font-semibold">{{ $subject['completed_papers'] }}</span>
                                        <span class="text-slate-600">/</span>
                                        <span class="font-semibold">{{ $subject['total_papers'] }}</span>
                                    </span>
                                    <span class="text-sm text-slate-600">papers completed</span>
                                </div>
                            </div>

                            {{-- Card Footer --}}
                            <div class="px-4 py-4 border-t border-slate-100">
                                <a href="{{ route('student.past_papers.subject', $subject['id']) }}"
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                                    View Papers →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm px-6 py-8 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">No subjects available</h3>
                    <p class="text-sm text-slate-600">Education department papers will be available soon.</p>
                </div>
            @endif
        </div>

        {{-- Free Style Section --}}
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-semibold mr-3 text-base">
                        ⭐
                    </span>
                    Free Style
                </h2>
                <p class="text-sm text-slate-600 mt-1">Practice papers from various sources</p>
            </div>

            @if(count($freeSubjects) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($freeSubjects as $subject)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col">
                            {{-- Card Header --}}
                            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-4">
                                <h3 class="text-base font-extrabold text-slate-900 mb-1 line-clamp-2">
                                    {{ $subject['name'] }}
                                </h3>
                                <p class="text-sm text-slate-600">
                                    {{ $subject['total_papers'] }} paper{{ $subject['total_papers'] !== 1 ? 's' : '' }}
                                </p>
                            </div>

                            {{-- Card Body --}}
                            <div class="flex-1 px-4 py-4">
                                {{-- Progress Bar --}}
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-slate-700">Progress</span>
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm border border-emerald-200">
                                            {{ $subject['average_percentage'] }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ $subject['average_percentage'] }}%">
                                        </div>
                                    </div>
                                </div>

                                {{-- Completion Stats --}}
                                <div class="flex items-center gap-1 px-3 py-2 bg-slate-50 rounded-lg">
                                    <span class="text-sm text-slate-700">
                                        <span class="font-semibold">{{ $subject['completed_papers'] }}</span>
                                        <span class="text-slate-600">/</span>
                                        <span class="font-semibold">{{ $subject['total_papers'] }}</span>
                                    </span>
                                    <span class="text-sm text-slate-600">papers completed</span>
                                </div>
                            </div>

                            {{-- Card Footer --}}
                            <div class="px-4 py-4 border-t border-slate-100">
                                <a href="{{ route('student.past_papers.subject', $subject['id']) }}"
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                                    View Papers →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm px-6 py-8 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">No subjects available</h3>
                    <p class="text-sm text-slate-600">Free style papers will be available soon.</p>
                </div>
            @endif
        </div>

</div>
@endsection
