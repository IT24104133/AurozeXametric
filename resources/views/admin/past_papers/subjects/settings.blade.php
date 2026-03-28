@extends('layouts.dashboard')

@section('title', 'Subject Free Style Settings')

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
                    <span class="ml-1 text-gray-500">Settings</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $subject->name }} - Free Style Settings</h1>
        <p class="text-gray-600 mb-6">Configure the question distribution for Free Style papers in this subject.</p>

        <form method="POST" action="{{ route('admin.past_papers.subjects.settings.update', ['stream' => $stream, 'subject' => $subject]) }}" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Total Questions --}}
            <div class="border-b pb-6">
                <label class="block text-lg font-semibold text-gray-900 mb-4">Total Questions</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Questions per Paper</label>
                        <input type="number" name="fs_total_questions" value="{{ old('fs_total_questions', $subject->fs_total_questions ?? 40) }}" min="10" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_total_questions') border-red-500 @enderror" required>
                        @error('fs_total_questions')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">Applies to all difficulty modes below</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timer (minutes)</label>
                        <input type="number" name="fs_timer_minutes" value="{{ old('fs_timer_minutes', $subject->fs_timer_minutes ?? 60) }}" min="5" max="300" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_timer_minutes') border-red-500 @enderror" required>
                        @error('fs_timer_minutes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Normal Mode --}}
            <div class="border-b pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Normal Mode</h2>
                <p class="text-sm text-gray-600 mb-4">Distribution of Easy, Medium, and Hard questions</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Easy (E)</label>
                        <input type="number" name="fs_count_e" value="{{ old('fs_count_e', $subject->fs_count_e ?? 12) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_count_e') border-red-500 @enderror" required>
                        @error('fs_count_e')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medium (M)</label>
                        <input type="number" name="fs_count_m" value="{{ old('fs_count_m', $subject->fs_count_m ?? 18) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_count_m') border-red-500 @enderror" required>
                        @error('fs_count_m')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hard (H)</label>
                        <input type="number" name="fs_count_h" value="{{ old('fs_count_h', $subject->fs_count_h ?? 10) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_count_h') border-red-500 @enderror" required>
                        @error('fs_count_h')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Total must equal {{ old('fs_total_questions', $subject->fs_total_questions ?? 40) }} questions</p>
                @error('normal')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ultra Easy Mode --}}
            <div class="border-b pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ultra Easy Mode (20E / 15M / 5H default)</h2>
                <p class="text-sm text-gray-600 mb-4">More Easy questions, fewer Hard questions</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Easy (E)</label>
                        <input type="number" name="fs_ultra_easy_e" value="{{ old('fs_ultra_easy_e', $subject->fs_ultra_easy_e ?? 20) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_easy_e') border-red-500 @enderror" required>
                        @error('fs_ultra_easy_e')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medium (M)</label>
                        <input type="number" name="fs_ultra_easy_m" value="{{ old('fs_ultra_easy_m', $subject->fs_ultra_easy_m ?? 15) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_easy_m') border-red-500 @enderror" required>
                        @error('fs_ultra_easy_m')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hard (H)</label>
                        <input type="number" name="fs_ultra_easy_h" value="{{ old('fs_ultra_easy_h', $subject->fs_ultra_easy_h ?? 5) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_easy_h') border-red-500 @enderror" required>
                        @error('fs_ultra_easy_h')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Total must equal {{ old('fs_total_questions', $subject->fs_total_questions ?? 40) }} questions</p>
                @error('ultra_easy')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ultra Medium Mode --}}
            <div class="border-b pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ultra Medium Mode (12E / 18M / 10H default)</h2>
                <p class="text-sm text-gray-600 mb-4">Balanced distribution across difficulties</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Easy (E)</label>
                        <input type="number" name="fs_ultra_medium_e" value="{{ old('fs_ultra_medium_e', $subject->fs_ultra_medium_e ?? 12) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_medium_e') border-red-500 @enderror" required>
                        @error('fs_ultra_medium_e')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medium (M)</label>
                        <input type="number" name="fs_ultra_medium_m" value="{{ old('fs_ultra_medium_m', $subject->fs_ultra_medium_m ?? 18) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_medium_m') border-red-500 @enderror" required>
                        @error('fs_ultra_medium_m')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hard (H)</label>
                        <input type="number" name="fs_ultra_medium_h" value="{{ old('fs_ultra_medium_h', $subject->fs_ultra_medium_h ?? 10) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_medium_h') border-red-500 @enderror" required>
                        @error('fs_ultra_medium_h')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Total must equal {{ old('fs_total_questions', $subject->fs_total_questions ?? 40) }} questions</p>
                @error('ultra_medium')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ultra Hard Mode --}}
            <div class="pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ultra Hard Mode (5E / 20M / 15H default)</h2>
                <p class="text-sm text-gray-600 mb-4">More Hard questions, fewer Easy questions</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Easy (E)</label>
                        <input type="number" name="fs_ultra_hard_e" value="{{ old('fs_ultra_hard_e', $subject->fs_ultra_hard_e ?? 5) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_hard_e') border-red-500 @enderror" required>
                        @error('fs_ultra_hard_e')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medium (M)</label>
                        <input type="number" name="fs_ultra_hard_m" value="{{ old('fs_ultra_hard_m', $subject->fs_ultra_hard_m ?? 20) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_hard_m') border-red-500 @enderror" required>
                        @error('fs_ultra_hard_m')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hard (H)</label>
                        <input type="number" name="fs_ultra_hard_h" value="{{ old('fs_ultra_hard_h', $subject->fs_ultra_hard_h ?? 15) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('fs_ultra_hard_h') border-red-500 @enderror" required>
                        @error('fs_ultra_hard_h')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Total must equal {{ old('fs_total_questions', $subject->fs_total_questions ?? 40) }} questions</p>
                @error('ultra_hard')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                    Save Settings
                </button>
                <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
