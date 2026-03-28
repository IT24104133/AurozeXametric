@extends('layouts.dashboard')

@section('title', 'Edit Past Paper')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @yield('sidebar-nav')
@endsection

@php
    $subjectId = $paper->subject_id;
@endphp

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
                    <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Subjects</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $subjectId]) }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Papers</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Edit</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Edit Past Paper</h1>
            <a href="{{ route('admin.past_papers.papers.index', ['stream' => $stream]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Papers
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-3xl border border-slate-200 p-6">
            <form method="POST" action="{{ route('admin.past_papers.papers.update', ['stream' => $stream, 'paper' => $paper]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <select id="subject_id" name="subject_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('subject_id') border-red-500 @enderror">
                        <option value="">Select a subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $paper->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('category') border-red-500 @enderror" onchange="updateFields()">
                        <option value="">Select category</option>
                        <option value="edu_department" {{ old('category', $paper->category) === 'edu_department' ? 'selected' : '' }}>Education Department</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1" value="{{ old('duration_minutes', $paper->duration_minutes) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('duration_minutes') border-red-500 @enderror" />
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="yearField" style="display: none;">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <input type="number" id="year" name="year" min="1900" max="2100" value="{{ old('year', $paper->year) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('year') border-red-500 @enderror" />
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="freeStyleFields" style="display: none;">
                    <div class="border rounded-lg p-4 bg-gray-50 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700">Free Style Configuration</h3>

                        <div>
                            <label for="total_questions" class="block text-sm font-medium text-gray-700 mb-2">Total Questions</label>
                            <input type="number" id="total_questions" name="total_questions" min="1" value="{{ old('total_questions', $paper->total_questions ?? 40) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('total_questions') border-red-500 @enderror" />
                            @error('total_questions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="count_e" class="block text-sm font-medium text-gray-700 mb-2">E Count</label>
                                <input type="number" id="count_e" name="count_e" min="0" value="{{ old('count_e', $paper->count_e ?? $paper->count_s ?? 12) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('count_e') border-red-500 @enderror" />
                                @error('count_e')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="count_m" class="block text-sm font-medium text-gray-700 mb-2">M Count</label>
                                <input type="number" id="count_m" name="count_m" min="0" value="{{ old('count_m', $paper->count_m ?? 18) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('count_m') border-red-500 @enderror" />
                                @error('count_m')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="count_h" class="block text-sm font-medium text-gray-700 mb-2">H Count</label>
                                <input type="number" id="count_h" name="count_h" min="0" value="{{ old('count_h', $paper->count_h ?? 10) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('count_h') border-red-500 @enderror" />
                                @error('count_h')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="draft" {{ old('status', $paper->status) === 'draft' ? 'checked' : '' }} class="w-4 h-4" />
                            <span class="ml-2">Draft</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="published" {{ old('status', $paper->status) === 'published' ? 'checked' : '' }} class="w-4 h-4" />
                            <span class="ml-2">Published</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFields() {
            const select = document.getElementById('subject_id');
            const categorySelect = document.getElementById('category');
            const category = categorySelect.value;

            const yearField = document.getElementById('yearField');
            const freeStyleFields = document.getElementById('freeStyleFields');

            if (category === 'edu_department') {
                yearField.style.display = 'block';
                freeStyleFields.style.display = 'none';
            } else if (category === 'free_style') {
                yearField.style.display = 'none';
                freeStyleFields.style.display = 'block';
            } else {
                yearField.style.display = 'none';
                freeStyleFields.style.display = 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateFields);
    </script>
@endsection
