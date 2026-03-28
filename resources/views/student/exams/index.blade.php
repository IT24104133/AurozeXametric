@extends('layouts.student')

@section('title', 'Available Exams')

@section('page_title', 'Available Exams')
@section('page_subtitle', 'Browse and start published exams')

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
                    <span class="ml-1 text-gray-700 font-medium">Exams</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="mb-8">
  <h1 class="text-3xl font-extrabold text-slate-900">Available Exams</h1>
  <p class="text-sm text-slate-600 mt-2">Browse and start published exams</p>
  
  {{-- Search by Exam Code --}}
  <div class="mt-6">
    <form method="GET" action="{{ route('student.exams.index') }}" class="flex gap-3">
      <input type="text" name="code" value="{{ request('code') }}" 
        class="flex-1 max-w-sm rounded-2xl border border-slate-300 px-5 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm"
        placeholder="Search by exam code (e.g., NGIE01)">
      <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-2xl hover:bg-teal-700 font-semibold shadow-sm transition">
        Search
      </button>
      @if(request('code'))
        <a href="{{ route('student.exams.index') }}" class="px-6 py-3 bg-slate-200 text-slate-700 rounded-2xl hover:bg-slate-300 font-semibold transition">
          Clear
        </a>
      @endif
    </form>
  </div>
</div>

@if($exams->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 text-center">
      <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h3 class="mt-4 text-lg font-bold text-slate-900">No published exams</h3>
      <p class="mt-2 text-sm text-slate-600">Check back later for new exams.</p>
    </div>
  @else
    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
      @foreach($exams as $exam)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
          
          {{-- Card Header with Status Badge --}}
          <div class="relative bg-gradient-to-br from-teal-50 to-sky-50 px-4 pt-4 pb-3">
            {{-- Watermark Logo --}}
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
              <img src="{{ asset('logo.png') }}" alt="" class="w-24 h-24 object-contain">
            </div>
            {{-- Status Badge - Top Right --}}
            <div class="absolute top-3 right-3">
              @php
                $status = $exam->attempt_status ?? null;
              @endphp
              @if(!$status)
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-teal-100 text-teal-700 border border-teal-200">
                  Available
                </span>
              @elseif($status === 'in_progress')
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                  In Progress
                </span>
              @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Completed
                </span>
              @endif
            </div>

            {{-- Exam UID Badge --}}
            @if($exam->exam_uid)
              <div class="mb-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-teal-100 text-teal-700 border border-teal-200">
                  {{ $exam->exam_uid }}
                </span>
              </div>
            @endif

            {{-- Title --}}
            <h3 class="text-base font-extrabold text-slate-900 mb-2 pr-16 leading-tight">
              {{ $exam->title }}
            </h3>

            {{-- Teacher Profile --}}
            <div class="flex items-center gap-2 mb-2">
              <img src="{{ asset('teacher.png') }}" alt="Teacher" class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm">
              <div>
                <p class="text-xs text-slate-700">
                  <span class="text-slate-500">By</span> 
                  <span class="font-bold">{{ $exam->teacher_name ?: 'Instructor' }}</span>
                </p>
              </div>
            </div>

            {{-- Description --}}
            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
              {{ $exam->description ?: 'No description' }}
            </p>
          </div>

          {{-- Card Footer with CTA Button --}}
          <div class="p-4">
            @php
              $hasCode = !empty($exam->exam_code);
              $codeOk = session()->get("exam_code_ok.{$exam->id}") === true;
              $openOnError = session('exam_code_for') == $exam->id;
            @endphp

            <div x-data="{ showCode: @json($openOnError) }">
              @if(!$status)
                @if($hasCode && !$codeOk)
                  <button type="button"
                     @click="showCode = true"
                     class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $exam->duration_minutes }} min</span>
                    <span class="ml-auto">Start Exam →</span>
                  </button>
                @else
                  <a href="{{ route('student.exams.start', $exam->id) }}"
                     class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-sm font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $exam->duration_minutes }} min</span>
                    <span class="ml-auto">Start Exam →</span>
                  </a>
                @endif
              @elseif($status === 'in_progress')
                @if($hasCode && !$codeOk)
                  <button type="button"
                     @click="showCode = true"
                     class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-2xl hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $exam->duration_minutes }} min</span>
                    <span class="ml-auto">Continue →</span>
                  </button>
                @else
                  <a href="{{ route('student.exams.start', $exam->id) }}"
                     class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-2xl hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $exam->duration_minutes }} min</span>
                    <span class="ml-auto">Continue →</span>
                  </a>
                @endif
              @else
                <button disabled
                   class="w-full px-4 py-2.5 bg-slate-100 text-slate-500 text-sm font-bold rounded-2xl cursor-not-allowed border border-slate-200">
                  Completed
                </button>
              @endif

              @if($hasCode)
                <div x-show="showCode" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
                  <div class="bg-white rounded-3xl shadow-xl w-full max-w-sm p-6" @click.away="showCode = false">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Enter Exam Code</h3>
                    <p class="text-sm text-slate-600 mb-5">This exam requires a code to start.</p>

                    <form method="POST" action="{{ route('student.exams.verifyCode', $exam) }}">
                      @csrf
                      <input type="text" name="exam_code" value="{{ old('exam_code') }}"
                        class="w-full border border-slate-300 rounded-2xl px-4 py-3 mb-3 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Exam code" required>

                      @if($openOnError && $errors->has('exam_code'))
                        <div class="text-sm text-red-600 mb-3">{{ $errors->first('exam_code') }}</div>
                      @endif

                      <div class="flex gap-3 justify-end mt-5">
                        <button type="button" @click="showCode = false"
                          class="px-5 py-2.5 rounded-2xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">
                          Cancel
                        </button>
                        <button type="submit"
                          class="px-5 py-2.5 rounded-2xl bg-teal-600 text-white font-semibold hover:bg-teal-700 transition">
                          Verify
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>


@endsection
