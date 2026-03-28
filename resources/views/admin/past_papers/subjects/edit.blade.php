@extends('layouts.dashboard')

@section('title', 'Edit Subject')

@section('sidebar-nav')
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
                    <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="ml-1 text-gray-700 hover:text-gray-900">Subjects</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500">Edit</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Edit Subject</h1>
            <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Subjects
            </a>
        </div>
        
        <div class="bg-white shadow-sm rounded-3xl border border-slate-200 p-6">

            <form method="POST" action="{{ route('admin.past_papers.subjects.update', ['stream' => $stream, 'subject' => $subject]) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $subject->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white px-6 py-3 rounded-2xl hover:shadow-md transition font-bold">
                        Update
                    </button>
                    <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="inline-flex items-center gap-2 bg-slate-200 text-slate-800 px-6 py-3 rounded-2xl hover:bg-slate-300 transition font-bold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
