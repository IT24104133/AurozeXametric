@extends('layouts.student')

@section('title', 'Past Papers')

@section('page_title', 'Past Papers')
@section('page_subtitle', 'Select stream and subject to practice')

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
<div class="mb-6">
  <h1 class="text-3xl font-extrabold text-slate-900">Past Papers</h1>
  <p class="text-sm text-slate-600 mt-1">Select a stream to browse and practice past examination papers</p>
</div>

<div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
  @foreach(['ol' => 'O/L', 'al' => 'A/L', 'grade5' => 'Grade 5 Scholarship'] as $stream => $label)
    <div class="bg-white rounded-3xl shadow-sm hover:shadow-md transition-all duration-200 border border-slate-200 overflow-hidden">
      
      {{-- Card Header with Stream Badge --}}
      <div class="relative bg-gradient-to-br from-teal-50 to-sky-50 px-4 pt-4 pb-3">
        {{-- Stream Badge - Top Right --}}
        <div class="absolute top-3 right-3">
          @if(($streamData[$stream] ?? 0) > 0)
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-teal-100 text-teal-700 border border-teal-200">
              {{ $streamData[$stream] }} Papers
            </span>
          @else
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
              Coming Soon
            </span>
          @endif
        </div>

        {{-- Stream Name --}}
        <h3 class="text-base font-extrabold text-slate-900 mb-1 pr-20">{{ $label }}</h3>
        <p class="text-xs text-slate-600">Practice papers from this examination stream</p>
      </div>

      {{-- Card Body --}}
      <div class="relative p-4">
        {{-- Watermark Logo - Bottom Center --}}
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center justify-center opacity-5 pointer-events-none select-none z-0">
          <img src="{{ asset('logo.png') }}" alt="" class="w-32 h-32 object-contain">
        </div>

        {{-- Card Content (Higher z-index) --}}
        <div class="relative z-10">
          @if(($streamData[$stream] ?? 0) > 0)
            {{-- Available Message --}}
            <div class="mb-4">
              <p class="text-sm font-medium text-slate-700">
                <span class="text-lg font-bold text-teal-600">{{ $streamData[$stream] }}</span>
                <span class="text-slate-600">{{ Str::plural('subject', $streamData[$stream]) }} available</span>
              </p>
            </div>

            {{-- View Button --}}
            <a href="{{ route('student.past_papers.streams', $stream) }}"
               class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
              Browse Papers
            </a>
          @else
            {{-- Coming Soon State --}}
            <div class="text-center py-6">
              <p class="text-sm font-medium text-slate-600">No papers available yet</p>
              <p class="text-xs text-slate-500 mt-1">Check back soon for new papers</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endforeach
</div>

@endsection
