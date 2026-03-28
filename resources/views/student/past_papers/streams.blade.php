@extends('layouts.student')

@section('title', streamLabel($stream) . ' - Subjects')

@section('page_title', streamLabel($stream))
@section('page_subtitle', 'Select a subject to practice')

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
                    <span class="ml-1 text-gray-700 font-medium">{{ streamLabel($stream) }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="mb-6 flex items-center gap-4">
  <a href="{{ route('student.past_papers.home') }}"
     class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    <span>Back to Past Papers</span>
  </a>
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">{{ streamLabel($stream) }} Subjects</h1>
    <p class="text-sm text-slate-600 mt-1">Select a subject to view available past papers</p>
  </div>
</div>

{{-- Education Department Section --}}
<div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  {{-- Section Header --}}
  <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
    <h3 class="text-lg font-extrabold text-slate-900">Education Department</h3>
    @if($eduSubjects->count() > 0)
      <span class="text-xs font-bold px-3 py-1 rounded-full bg-teal-100 text-teal-700 border border-teal-200">{{ $eduSubjects->count() }} {{ Str::plural('Subject', $eduSubjects->count()) }}</span>
    @else
      <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Coming Soon</span>
    @endif
  </div>

  {{-- Section Content --}}
  <div class="p-6">
    @if($eduSubjects->isEmpty())
      {{-- Empty State --}}
      <div class="bg-slate-50 rounded-xl border border-slate-200 p-8 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-sm font-medium text-slate-600">No subjects available yet</p>
        <p class="text-xs text-slate-500 mt-1">Education Department subjects coming soon</p>
      </div>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($eduSubjects as $subject)
          @php
            $publishedPapersCount = $subject->pastPapers->count();
          @endphp
           <a href="{{ route('student.past_papers.subject.papers', [$stream, $subject->id, 'education']) }}"
             class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 overflow-hidden relative group">
            
            {{-- Watermark Logo - Bottom Right --}}
            <div class="absolute bottom-4 right-4 flex items-center justify-center opacity-5 pointer-events-none select-none z-0">
              <img src="{{ asset('logo.png') }}" alt="" class="w-32 h-32 object-contain">
            </div>

            {{-- Card Content - Higher z-index --}}
            <div class="relative z-10">
              {{-- Card Header Strip --}}
              <div class="relative bg-gradient-to-br from-teal-50 to-sky-50 px-4 pt-4 pb-3">
                {{-- Badge - Top Right --}}
                <div class="absolute top-3 right-3">
                  <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-700 border border-teal-200">
                    Education
                  </span>
                </div>

                {{-- Subject Name --}}
                <h4 class="text-sm font-bold text-slate-900 mb-0.5 pr-20">{{ $subject->name }}</h4>
                <p class="text-xs text-slate-600">{{ streamLabel($stream) }} subject papers</p>
              </div>

              {{-- Card Body --}}
              <div class="p-4">
                {{-- Paper Count --}}
                <div class="mb-4">
                  <p class="text-xs text-slate-600 font-medium">Available Papers</p>
                  <p class="text-3xl font-bold text-slate-900">{{ $publishedPapersCount }}</p>
                </div>

                {{-- Performance Stats --}}
                <div class="mb-4 space-y-2 text-xs text-slate-600">
                  @if($subject->attempts_count > 0)
                    <div class="flex justify-between">
                      <span>Attempts:</span>
                      <span class="font-semibold text-slate-900">{{ $subject->attempts_count }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Last Score:</span>
                      <span class="font-semibold text-teal-600">{{ (int)$subject->last_percent }}%</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Average:</span>
                      <span class="font-semibold text-teal-600">{{ (int)$subject->avg_percent }}%</span>
                    </div>
                  @else
                    <div class="text-slate-500">No attempts yet</div>
                  @endif
                </div>

                {{-- CTA Button --}}
                <button class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                  <span>View Papers</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- Free Style Section --}}
<div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  {{-- Section Header --}}
  <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
    <h3 class="text-lg font-extrabold text-slate-900">Free Style</h3>
    @if($freeSubjects->count() > 0)
      <span class="text-xs font-bold px-3 py-1 rounded-full bg-teal-100 text-teal-700 border border-teal-200">{{ $freeSubjects->count() }} {{ Str::plural('Subject', $freeSubjects->count()) }}</span>
    @else
      <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Coming Soon</span>
    @endif
  </div>

  {{-- Section Content --}}
  <div class="p-6">
    @if($freeSubjects->isEmpty())
      {{-- Empty State --}}
      <div class="bg-slate-50 rounded-xl border border-slate-200 p-8 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-sm font-medium text-slate-600">No subjects available yet</p>
        <p class="text-xs text-slate-500 mt-1">Free Style subjects coming soon</p>
      </div>
    @else
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($freeSubjects as $subject)
          @php
            $publishedPapersCount = $subject->pastPapers->count();
          @endphp
           <a href="{{ route('student.past_papers.subject.papers', [$stream, $subject->id, 'free']) }}"
             class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 overflow-hidden relative group">
            
            {{-- Watermark Logo - Bottom Right --}}
            <div class="absolute bottom-4 right-4 flex items-center justify-center opacity-5 pointer-events-none select-none z-0">
              <img src="{{ asset('logo.png') }}" alt="" class="w-32 h-32 object-contain">
            </div>

            {{-- Card Content - Higher z-index --}}
            <div class="relative z-10">
              {{-- Card Header Strip --}}
              <div class="relative bg-gradient-to-br from-emerald-50 to-teal-50 px-4 pt-4 pb-3">
                {{-- Badge - Top Right --}}
                <div class="absolute top-3 right-3">
                  <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                    Free Style
                  </span>
                </div>

                {{-- Subject Name --}}
                <h4 class="text-sm font-bold text-slate-900 mb-0.5 pr-20">{{ $subject->name }}</h4>
                <p class="text-xs text-slate-600">{{ streamLabel($stream) }} subject papers</p>
              </div>

              {{-- Card Body --}}
              <div class="p-4">
                {{-- Paper Count --}}
                <div class="mb-4">
                  <p class="text-xs text-slate-600 font-medium">Available Papers</p>
                  <p class="text-3xl font-bold text-slate-900">∞</p>
                  <p class="text-xs text-emerald-600 font-semibold">Unlimited</p>
                </div>

                {{-- Performance Stats --}}
                <div class="mb-4 space-y-2 text-xs text-slate-600">
                  @if($subject->attempts_count > 0)
                    <div class="flex justify-between">
                      <span>Attempts:</span>
                      <span class="font-semibold text-slate-900">{{ $subject->attempts_count }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Last Score:</span>
                      <span class="font-semibold text-emerald-600">{{ (int)$subject->last_percent }}%</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Average:</span>
                      <span class="font-semibold text-emerald-600">{{ (int)$subject->avg_percent }}%</span>
                    </div>
                  @else
                    <div class="text-slate-500">No attempts yet</div>
                  @endif
                </div>

                {{-- CTA Button --}}
                <button class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                  <span>View Papers</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
