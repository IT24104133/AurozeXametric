@extends('layouts.dashboard')

@section('title', 'Question Bank Health')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Question Bank Health</h1>
                <p class="text-slate-600 mt-1">Monitor question distribution and coverage across all subjects</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- A) Global Question Distribution -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Global Question Distribution</h2>
                    <p class="text-sm text-slate-600">Total: {{ $globalStats['total'] }} questions</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Easy -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">Easy</span>
                        <span class="text-2xl font-extrabold text-emerald-600">{{ $globalStats['easy_percent'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $globalStats['easy_percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $globalStats['easy'] }} questions</p>
                </div>

                <!-- Medium -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">Medium</span>
                        <span class="text-2xl font-extrabold text-amber-600">{{ $globalStats['medium_percent'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $globalStats['medium_percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $globalStats['medium'] }} questions</p>
                </div>

                <!-- Hard -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">Hard</span>
                        <span class="text-2xl font-extrabold text-red-600">{{ $globalStats['hard_percent'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $globalStats['hard_percent'] }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $globalStats['hard'] }} questions</p>
                </div>
            </div>
        </div>

        <!-- B) By Stream + Subject -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-teal-50 to-sky-50 px-6 py-4 border-b border-slate-200">
                <h2 class="text-xl font-bold text-slate-800">Questions by Stream & Subject</h2>
            </div>

            <div class="p-6 space-y-8">
                @forelse($streamSubjectStats as $stream => $subjects)
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $stream }}
                        </h3>

                        <div class="space-y-4">
                            @foreach($subjects as $subjectData)
                                <div class="border border-slate-200 rounded-2xl p-4 hover:shadow-md transition">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-bold text-slate-800">{{ $subjectData['subject'] }}</h4>
                                            @if($subjectData['warning'])
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                                                    ⚠️ LOW
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">
                                                    ✅ OK
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-sm font-semibold text-slate-600">Total: {{ $subjectData['total'] }}</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Easy -->
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-600">Easy</span>
                                                <span class="text-sm font-bold text-emerald-600">{{ $subjectData['easy_percent'] }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $subjectData['easy_percent'] }}%"></div>
                                            </div>
                                            <p class="text-xs text-slate-500">{{ $subjectData['easy'] }} questions</p>
                                        </div>

                                        <!-- Medium -->
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-600">Medium</span>
                                                <span class="text-sm font-bold text-amber-600">{{ $subjectData['medium_percent'] }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $subjectData['medium_percent'] }}%"></div>
                                            </div>
                                            <p class="text-xs text-slate-500">{{ $subjectData['medium'] }} questions</p>
                                        </div>

                                        <!-- Hard -->
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-600">Hard</span>
                                                <span class="text-sm font-bold text-red-600">{{ $subjectData['hard_percent'] }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $subjectData['hard_percent'] }}%"></div>
                                            </div>
                                            <p class="text-xs text-slate-500">{{ $subjectData['hard'] }} questions</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No subjects found</p>
                @endforelse
            </div>
        </div>

        <!-- C) Coverage vs Paper Requirements -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-teal-50 to-sky-50 px-6 py-4 border-b border-slate-200">
                <h2 class="text-xl font-bold text-slate-800">Coverage Analysis</h2>
                <p class="text-sm text-slate-600 mt-1">Question availability vs. paper requirements</p>
            </div>

            <div class="p-6 space-y-8">
                @forelse($coverageAnalysis as $stream => $papers)
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-4">{{ $stream }}</h3>

                        <div class="space-y-4">
                            @foreach($papers as $paper)
                                <div class="border border-slate-200 rounded-2xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h4 class="font-bold text-slate-800">{{ $paper['paper_title'] }}</h4>
                                            <p class="text-sm text-slate-600">{{ $paper['subject'] }} • {{ ucfirst(str_replace('_', ' ', $paper['category'])) }}</p>
                                        </div>
                                        @if($paper['status'] === 'ok')
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                                                ✅ OK
                                            </span>
                                        @elseif($paper['status'] === 'low')
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold">
                                                ⚠️ LOW
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                                ❌ FAIL
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                        <!-- Easy -->
                                        <div class="bg-slate-50 rounded-xl p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-semibold text-slate-600">Easy</span>
                                                @if($paper['easy_status'] === 'ok')
                                                    <span class="text-xs font-semibold text-emerald-600">✅</span>
                                                @elseif($paper['easy_status'] === 'low')
                                                    <span class="text-xs font-semibold text-amber-600">⚠️</span>
                                                @else
                                                    <span class="text-xs font-semibold text-red-600">❌</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-slate-700">
                                                <span class="font-bold">{{ $paper['available_easy'] }}</span> available / 
                                                <span class="font-bold">{{ $paper['required_easy'] }}</span> required
                                            </p>
                                        </div>

                                        <!-- Medium -->
                                        <div class="bg-slate-50 rounded-xl p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-semibold text-slate-600">Medium</span>
                                                @if($paper['medium_status'] === 'ok')
                                                    <span class="text-xs font-semibold text-emerald-600">✅</span>
                                                @elseif($paper['medium_status'] === 'low')
                                                    <span class="text-xs font-semibold text-amber-600">⚠️</span>
                                                @else
                                                    <span class="text-xs font-semibold text-red-600">❌</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-slate-700">
                                                <span class="font-bold">{{ $paper['available_medium'] }}</span> available / 
                                                <span class="font-bold">{{ $paper['required_medium'] }}</span> required
                                            </p>
                                        </div>

                                        <!-- Hard -->
                                        <div class="bg-slate-50 rounded-xl p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-semibold text-slate-600">Hard</span>
                                                @if($paper['hard_status'] === 'ok')
                                                    <span class="text-xs font-semibold text-emerald-600">✅</span>
                                                @elseif($paper['hard_status'] === 'low')
                                                    <span class="text-xs font-semibold text-amber-600">⚠️</span>
                                                @else
                                                    <span class="text-xs font-semibold text-red-600">❌</span>
                                                @endif
                                            </div>
                                            <p class="text="sm text-slate-700">
                                                <span class="font-bold">{{ $paper['available_hard'] }}</span> available / 
                                                <span class="font-bold">{{ $paper['required_hard'] }}</span> required
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No published papers found</p>
                @endforelse
            </div>
        </div>

        <!-- D) Fix Suggestions -->
        @if(count($suggestions) > 0)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl border border-amber-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center border border-amber-300">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Fix Suggestions</h2>
                        <p class="text-sm text-slate-600">Action items to improve question bank health</p>
                    </div>
                </div>

                <ul class="space-y-2">
                    @foreach($suggestions as $suggestion)
                        <li class="flex items-start gap-2 text-slate-700">
                            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">{{ $suggestion }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl border border-emerald-200 shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center border border-emerald-300">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">All Good! 🎉</h2>
                        <p class="text-sm text-slate-600">Your question bank is healthy with sufficient coverage.</p>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
