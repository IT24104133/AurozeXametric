@extends('layouts.student')

@section('title', 'My Results')

@section('page_title', 'My Results')
@section('page_subtitle', 'View your exam and past paper results')

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
                    <span class="ml-1 text-gray-700 font-medium">Results</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="mb-6">
  <h1 class="text-3xl font-extrabold text-slate-900">My Results</h1>
  <p class="text-sm text-slate-600 mt-1">
    Completed exams appear here. Scores show once results are published.
  </p>
</div>

@if($attempts->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 text-center">
      <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      <h3 class="mt-2 text-base font-bold text-slate-900">No completed exams yet</h3>
      <p class="mt-1 text-sm text-slate-600">Complete exams to see your results here.</p>
      <div class="mt-6">
        <a href="{{ route('student.exams.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold rounded-2xl hover:from-teal-700 hover:to-teal-800 transition shadow-sm">
          Browse Exams →
        </a>
      </div>
    </div>
  @else
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-gradient-to-r from-teal-50 to-sky-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Exam</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Score</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Submitted At</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-100">
            @foreach($attempts as $a)
              <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-slate-900">{{ $a->exam->title ?? 'Exam' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $isPublished = (bool) ($a->exam->results_published ?? false);
                  @endphp
                  <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-bold rounded-full border {{ $isPublished ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                    {{ $isPublished ? 'Published' : 'Pending Results' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                  @if($isPublished)
                    @php
                      $total = (int) ($a->total_questions ?? 0);
                      $score = (int) ($a->score ?? 0);
                      $percent = $total > 0 ? round(($score / $total) * 100) : 0;
                    @endphp
                    <span class="font-bold text-teal-600">{{ $score }}</span> / {{ $total }}
                    <span class="text-xs text-slate-500 ml-2">({{ $percent }}%)</span>
                  @else
                    <span class="text-sm text-slate-500">Pending</span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  {{ $a->submitted_at ? $a->submitted_at->format('M d, Y H:i') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  @if($isPublished)
                    <a class="inline-flex items-center gap-1 px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-xl hover:bg-teal-700 transition"
                       href="{{ route('student.exams.result', ['exam' => $a->exam_id, 'attempt' => $a->id, 'return' => request()->getRequestUri()]) }}">
                      View Result →
                    </a>
                  @else
                    <span class="text-slate-500 text-xs font-semibold">Pending</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
@endsection
