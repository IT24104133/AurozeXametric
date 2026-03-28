@extends('layouts.student')

@section('title', 'Past Paper Results')

@section('page_title', 'Past Paper Results')
@section('page_subtitle', 'Review your past paper attempt')

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
                    <span class="ml-1 text-gray-700 font-medium">Results</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
@php
    $returnUrl = request()->query('return');
    $safeReturnUrl = ($returnUrl && str_starts_with($returnUrl, '/'))
        ? $returnUrl
        : route('student.dashboard');
@endphp
        
        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-8 mb-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    @if($paper->title)
                        {{ $paper->title }}
                    @elseif($paper->year)
                        {{ $paper->subject->name }} - {{ $paper->year }}
                    @else
                        {{ $paper->subject->name }}
                    @endif
                </h1>
                <p class="text-gray-600">Attempt #{{ $attempt->attempt_no }} - Results</p>
            </div>

            {{-- Score Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Percentage --}}
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 text-center border border-indigo-200">
                    <div class="text-sm font-semibold text-indigo-600 uppercase mb-2">Score</div>
                    <div class="text-4xl font-bold text-indigo-700">
                        {{ number_format($attempt->percentage, 1) }}%
                    </div>
                </div>

                {{-- Correct Answers --}}
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center border border-green-200">
                    <div class="text-sm font-semibold text-green-600 uppercase mb-2">Correct</div>
                    <div class="text-4xl font-bold text-green-700">
                        {{ $attempt->correct_count }}
                    </div>
                </div>

                {{-- Total Questions --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-sm font-semibold text-gray-600 uppercase mb-2">Total</div>
                    <div class="text-4xl font-bold text-gray-700">
                        {{ $attempt->total_questions }}
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            @php
                $subjectStream = $paper->stream
                    ?? optional($paper->subject)->stream
                    ?? optional($attempt->paper)->stream;
                $subjectSource = ($paper->category ?? null) === 'free_style' ? 'free' : 'education';
            @endphp
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $safeReturnUrl }}"
                   class="flex-1 px-6 py-3 bg-slate-100 text-slate-700 text-center font-semibold rounded-lg hover:bg-slate-200 transition">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <a href="{{ route('student.past_papers.start', $paper->id) }}"
                   class="flex-1 px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Retake
                </a>
                     <a href="{{ route('student.past_papers.subject.papers', ['stream' => $subjectStream, 'subject' => $paper->subject_id, 'source' => $subjectSource]) }}"
                   class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-300 transition">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Subject
                </a>
            </div>
        </div>

        {{-- Questions Review --}}
        <div class="space-y-6">
            @foreach($questions as $questionIndex => $question)
                @php
                    $studentAnswer = $answersMap->get($question->id);
                    $selectedOptionId = $studentAnswer ? $studentAnswer->selected_option_id : null;
                    $isCorrect = $studentAnswer ? $studentAnswer->is_correct : false;
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border-2 @if($isCorrect) border-green-200 @elseif($selectedOptionId) border-red-200 @else border-gray-200 @endif overflow-hidden">
                    {{-- Question Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between @if($isCorrect) bg-green-50 @elseif($selectedOptionId) bg-red-50 @else bg-gray-50 @endif">
                        <h3 class="text-lg font-bold text-gray-900">
                            Question {{ $questionIndex + 1 }} of {{ $questions->count() }}
                        </h3>
                        <div>
                            @if($isCorrect)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Correct
                                </span>
                            @elseif($selectedOptionId)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-100 text-red-700 text-sm font-semibold">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Incorrect
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">
                                    Not Answered
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Question Body --}}
                    <div class="px-6 py-6">
                        {{-- Question Text --}}
                        <div class="mb-6">
                            <p class="text-gray-900 text-lg leading-relaxed">
                                {{ $question->question_text }}
                            </p>

                            {{-- Question Image --}}
                            @if($question->question_image)
                                <div class="mt-4">
                                    <img src="{{ asset('storage/' . $question->question_image) }}" 
                                         alt="Question image" 
                                         class="max-w-full h-auto rounded-lg border border-gray-200">
                                </div>
                            @endif
                        </div>

                        {{-- Options --}}
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                @php
                                    $isStudentAnswer = $option->id === $selectedOptionId;
                                    $isCorrectOption = $option->is_correct;
                                @endphp

                                <div class="flex items-start p-4 rounded-lg border-2 transition-all
                                    @if($isCorrectOption) 
                                        border-green-400 bg-green-50 shadow-sm
                                    @elseif($isStudentAnswer && !$isCorrectOption) 
                                        border-red-400 bg-red-50 shadow-sm
                                    @else 
                                        border-gray-200 bg-white
                                    @endif
                                ">
                                    {{-- Option Icon --}}
                                    <div class="flex-shrink-0 mt-1">
                                        @if($isCorrectOption)
                                            <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @elseif($isStudentAnswer && !$isCorrectOption)
                                            <div class="w-7 h-7 rounded-full bg-red-500 flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-7 h-7 rounded-full border-2 border-gray-300 bg-white"></div>
                                        @endif
                                    </div>

                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center flex-wrap gap-2 mb-1.5">
                                            <span class="text-base font-bold @if($isCorrectOption) text-green-700 @elseif($isStudentAnswer) text-red-700 @else text-gray-700 @endif">
                                                {{ $option->option_key }}.
                                            </span>
                                            
                                            {{-- Badges --}}
                                            @if($isCorrectOption && $isStudentAnswer)
                                                {{-- Student got it right --}}
                                                <span class="inline-flex items-center text-xs font-semibold text-green-700 px-2.5 py-1 bg-green-200 rounded-full">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Your Answer - Correct!
                                                </span>
                                            @elseif($isCorrectOption)
                                                {{-- Show correct answer --}}
                                                <span class="inline-flex items-center text-xs font-semibold text-green-700 px-2.5 py-1 bg-green-200 rounded-full">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Correct Answer
                                                </span>
                                            @elseif($isStudentAnswer)
                                                {{-- Show wrong student answer --}}
                                                <span class="inline-flex items-center text-xs font-semibold text-red-700 px-2.5 py-1 bg-red-200 rounded-full">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Your Answer
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-base leading-relaxed @if($isCorrectOption) text-green-900 font-medium @elseif($isStudentAnswer) text-red-900 font-medium @else text-gray-700 @endif">
                                            {{ $option->option_text }}
                                        </p>

                                        {{-- Option Image --}}
                                        @if($option->option_image)
                                            <img src="{{ asset('storage/' . $option->option_image) }}" 
                                                 alt="Option image" 
                                                 class="mt-3 max-w-xs h-auto rounded-lg border border-gray-300 shadow-sm">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Show "Not Answered" message if no option selected --}}
                        @if(!$selectedOptionId)
                            <div class="mt-4 p-4 bg-amber-50 border-l-4 border-amber-400 rounded">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-amber-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm font-semibold text-amber-800">Not Answered - You did not select any option for this question.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Action Buttons --}}
        @php
            $subjectStream = $paper->stream
                ?? optional($paper->subject)->stream
                ?? optional($attempt->paper)->stream;
            $subjectSource = ($paper->category ?? null) === 'free_style' ? 'free' : 'education';
        @endphp
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ $safeReturnUrl }}"
               class="flex-1 px-6 py-3 bg-slate-100 text-slate-700 text-center font-semibold rounded-lg hover:bg-slate-200 transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <a href="{{ route('student.past_papers.start', $paper->id) }}"
               class="flex-1 px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Retake
            </a>
                <a href="{{ route('student.past_papers.subject.papers', ['stream' => $subjectStream, 'subject' => $paper->subject_id, 'source' => $subjectSource]) }}"
               class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-300 transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Subject
            </a>
        </div>

@endsection
