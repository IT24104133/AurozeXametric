@extends('layouts.dashboard')

@section('title', 'Manage Questions')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @yield('sidebar-nav')
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
            <a href="{{ route('admin.past_papers.home') }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Past Papers</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">{{ $paper->title }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Questions for {{ $paper->title }}</h1>
                <p class="text-slate-600 mt-1">{{ $paper->subject->name }} • {{ $paper->year ?? 'Free Style' }} • {{ $paper->duration_minutes }} minutes</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Papers
                </a>
                <a href="{{ route('admin.past_papers.questions.create', [$stream, $paper->id]) }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white px-4 py-2 rounded-2xl hover:shadow-md transition font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Question
                </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $message }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">#</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Question Text</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Correct</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($questions as $index => $question)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-md truncate" title="{{ $question->question_text }}">
                                    {{ $question->question_text }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @php
                                    $correctOption = $question->options->where('is_correct', true)->first();
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 font-semibold rounded">
                                    {{ $correctOption?->option_key ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.past_papers.questions.edit', [$stream, $question->id]) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.past_papers.questions.destroy', [$stream, $question->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-600">
                                No questions yet. <a href="{{ route('admin.past_papers.questions.create', [$stream, $paper->id]) }}" class="text-blue-600 hover:underline">Add one now.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.past_papers.papers.index', [$stream, $paper->subject_id]) }}" class="text-blue-600 hover:underline">← Back to Papers</a>
        </div>
    </div>
@endsection
