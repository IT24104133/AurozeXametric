@php
    $isDashboard = request()->routeIs('admin.dashboard');
    $isExams = request()->routeIs('admin.exams.*');
    $isPastPapers = request()->routeIs('admin.past_papers.*');
    $isStudents = request()->routeIs('admin.students.*');
    $isHealth = request()->routeIs('admin.system.health');
    $isQuestionHealth = request()->routeIs('admin.question_health');
    
    $navBase = "flex items-center gap-3 px-4 py-2.5 text-sm font-semibold rounded-full transition";
    $navActive = "bg-teal-50 text-teal-700 border border-teal-200";
    $navIdle = "text-slate-700 hover:bg-slate-50";
    $iconActive = "text-teal-700";
    $iconIdle = "text-slate-500";
@endphp

<a href="{{ route('admin.dashboard') }}"
   class="{{ $navBase }} {{ $isDashboard ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isDashboard ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

<a href="{{ route('admin.exams.index') }}"
   class="{{ $navBase }} {{ $isExams ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isExams ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    Exams
</a>

<a href="{{ route('admin.past_papers.home') }}"
   class="{{ $navBase }} {{ $isPastPapers ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isPastPapers ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
    Past Papers
</a>

<a href="{{ route('admin.students.bulk.create') }}"
   class="{{ $navBase }} {{ $isStudents ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isStudents ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    Students
</a>

<a href="{{ route('admin.question_health') }}"
   class="{{ $navBase }} {{ $isQuestionHealth ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isQuestionHealth ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Question Health
</a>

<a href="{{ route('admin.system.health') }}"
   class="{{ $navBase }} {{ $isHealth ? $navActive : $navIdle }}">
    <svg class="w-5 h-5 {{ $isHealth ? $iconActive : $iconIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    System Status
</a>

<hr class="my-3 border-slate-200">
