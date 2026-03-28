@extends('layouts.dashboard')

@section('title', 'Past Papers')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Past Papers</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900">Past Papers Management</h1>
            <p class="text-slate-600 mt-2">Select a stream to manage subjects, papers, and questions</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(['ol' => 'O/L', 'al' => 'A/L', 'grade5' => 'Grade 5 Scholarship'] as $stream => $label)
                @php
                    $data = $streamData[$stream] ?? ['subjects' => 0, 'papers' => 0];
                @endphp
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
                    <div class="bg-gradient-to-r from-teal-50 to-sky-50 p-6">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $label }}</h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl p-4 border border-teal-100">
                                <p class="text-slate-600 text-sm font-bold">Subjects</p>
                                <p class="text-3xl font-extrabold text-slate-900">{{ $data['subjects'] }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl p-4 border border-teal-100">
                                <p class="text-slate-600 text-sm font-bold">Papers</p>
                                <p class="text-3xl font-extrabold text-slate-900">{{ $data['papers'] }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('admin.past_papers.subjects.index', $stream) }}"
                               class="block w-full bg-gradient-to-r from-teal-600 to-teal-700 hover:shadow-md text-white font-bold py-3 px-4 rounded-2xl text-center transition text-sm">
                                Manage Subjects
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
