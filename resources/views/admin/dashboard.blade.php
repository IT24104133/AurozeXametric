@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('breadcrumbs')
    <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6">
    <!-- Welcome Banner -->
    <div class="mb-8 bg-gradient-to-br from-teal-50 to-sky-50 border border-slate-200 rounded-3xl shadow-sm px-6 py-6">
        <h2 class="text-2xl font-extrabold text-slate-900">Welcome back, Admin!</h2>
        <p class="text-sm text-slate-600 mt-1">Manage exams, students, and monitor system activity</p>
    </div>

    <!-- Analytics & Growth Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Analytics & Growth Preview</h3>
                <p class="text-sm text-slate-600">Preview before enabling on homepage</p>
            </div>
            <div class="flex items-center gap-3">
                @if($homepageSettings['enable_analytics_section'] ?? false)
                    <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Live on Homepage
                    </span>
                @else
                    <span class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Preview Only
                    </span>
                @endif
                <a href="{{ route('admin.homepage.settings') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-semibold hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configure
                </a>
            </div>
        </div>
        
        <!-- Analytics Cards Grid (12-column system) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-4 mb-8">
            <!-- Students (3 cols) -->
            <div class="xl:col-span-3 bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Students</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalStudents }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center border border-teal-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Teachers (3 cols) -->
            <div class="xl:col-span-3 bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Teachers</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalTeachers }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center border border-purple-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Exams (2 cols) -->
            <div class="xl:col-span-2 bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Exams</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalExamsCount }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Past Papers (2 cols) -->
            <div class="xl:col-span-2 bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Past Papers</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalPastPapers }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center border border-sky-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>

            <!-- Free Style (2 cols) -->
            <div class="xl:col-span-2 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-amber-700 uppercase">Free Style</p>
                    <p class="mt-2 text-4xl font-extrabold text-amber-900">∞</p>
                    <p class="text-xs text-amber-600 mt-1">Unlimited</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Activity Section -->
    <div class="mb-8">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Exam Activity</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <!-- Total Exams -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Total Exams</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalExams }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center border border-teal-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Published Exams -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Published Exams</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $publishedExams }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Draft Exams -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Draft Exams</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $draftExams }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <!-- Total Attempts -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Total Attempts</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $totalAttempts }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center border border-sky-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Submitted -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Submitted</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $submittedAttempts }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center border border-teal-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>

            <!-- Auto-Submitted -->
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 flex items-center justify-between hover:shadow-md transition h-full">
                <div>
                    <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Auto-Submitted</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900">{{ $autoSubmittedAttempts }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center border border-rose-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create'))" 
               class="flex items-center justify-between px-4 py-3 rounded-2xl bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold shadow-sm hover:shadow-md transition h-full">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create New Exam
                </span>
                <span class="text-white/80">→</span>
            </button>
            
            <a href="{{ route('admin.exams.index') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-2xl border border-slate-200 text-slate-700 font-bold bg-white hover:bg-slate-50 transition h-full">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View All Exams
                </span>
                <span class="text-slate-400">→</span>
            </a>
            
            <a href="{{ route('admin.students.bulk.create') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-2xl border border-slate-200 text-slate-700 font-bold bg-white hover:bg-slate-50 transition h-full">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Manage Students
                </span>
                <span class="text-slate-400">→</span>
            </a>
        </div>
    </div>
</div>
@include('components.admin.create-exam-modal')
@endsection
