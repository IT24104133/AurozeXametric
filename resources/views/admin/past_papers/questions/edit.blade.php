@extends('layouts.dashboard')

@section('title', 'Edit Question')

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
                    <a href="{{ route('admin.past_papers.questions.index', ['stream' => $stream, 'paper' => $paper->id]) }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">{{ $paper->title }}</a>
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
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Question</h1>
        <p class="text-gray-600 mb-6">{{ $paper->title }} • {{ $paper->subject->name }}</p>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('admin.past_papers.questions.update', [$stream, $question->id]) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div>
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                    <textarea id="question_text" name="question_text" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('question_text') border-red-500 @enderror" placeholder="Enter the question text here...">{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">Text optional if you provide at least one image.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Images (optional)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="question_image_1" class="block text-xs text-gray-600 mb-1">Question Image 1</label>
                            <input type="file" id="question_image_1" name="question_image_1" accept="image/*" class="w-full text-sm" />
                            @if($question->question_image_1)
                                <img src="{{ Storage::url($question->question_image_1) }}" alt="Question Image 1" class="mt-2 max-h-28 rounded border" />
                            @elseif($question->question_image)
                                <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="mt-2 max-h-28 rounded border" />
                            @endif
                            @error('question_image_1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="question_image_2" class="block text-xs text-gray-600 mb-1">Question Image 2</label>
                            <input type="file" id="question_image_2" name="question_image_2" accept="image/*" class="w-full text-sm" />
                            @if($question->question_image_2)
                                <img src="{{ Storage::url($question->question_image_2) }}" alt="Question Image 2" class="mt-2 max-h-28 rounded border" />
                            @endif
                            @error('question_image_2')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="question_image_3" class="block text-xs text-gray-600 mb-1">Question Image 3</label>
                            <input type="file" id="question_image_3" name="question_image_3" accept="image/*" class="w-full text-sm" />
                            @if($question->question_image_3)
                                <img src="{{ Storage::url($question->question_image_3) }}" alt="Question Image 3" class="mt-2 max-h-28 rounded border" />
                            @endif
                            @error('question_image_3')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                    <select id="difficulty" name="difficulty" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('difficulty') border-red-500 @enderror">
                        <option value="E" {{ old('difficulty', $question->difficulty ?? 'M') === 'E' ? 'selected' : '' }}>Easy (E)</option>
                        <option value="M" {{ old('difficulty', $question->difficulty ?? 'M') === 'M' ? 'selected' : '' }}>Medium (M)</option>
                        <option value="H" {{ old('difficulty', $question->difficulty ?? 'M') === 'H' ? 'selected' : '' }}>Hard (H)</option>
                    </select>
                    @error('difficulty')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Options</label>
                    <p class="text-xs text-gray-500 mb-3">Text optional if image is provided.</p>
                    <div class="space-y-4">
                        @foreach(['A', 'B', 'C', 'D'] as $key)
                            @php
                                $option = $question->options->where('option_key', $key)->first();
                                $isCorrect = $option?->is_correct ?? false;
                            @endphp
                            <div class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center pt-2">
                                    <input type="radio" id="correct_{{ $key }}" name="correct_option" value="{{ $key }}" {{ $isCorrect ? 'checked' : '' }} class="w-4 h-4 text-blue-600" />
                                </div>
                                <div class="flex-1">
                                    <label for="option_{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Option {{ $key }}
                                    </label>
                                    <input type="text" id="option_{{ $key }}" name="options[{{ $key }}][text]" value="{{ old("options.{$key}.text", $option?->option_text) }}" placeholder="Enter option {{ $key }} text..." class="w-full px-3 py-2 border border-gray-300 rounded-lg @error("options.{$key}.text") border-red-500 @enderror" />
                                    @error("options.{$key}.text")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div class="mt-2">
                                        <label for="option_{{ $key }}_image" class="block text-xs text-gray-600 mb-1">Option {{ $key }} Image</label>
                                        <input type="file" id="option_{{ $key }}_image" name="options[{{ $key }}][image]" accept="image/*" class="w-full text-sm" />
                                        @if($option?->option_image)
                                            <img src="{{ Storage::url($option->option_image) }}" alt="Option {{ $key }} Image" class="mt-2 max-h-28 rounded border" />
                                        @endif
                                        @error("options.{$key}.image")
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('correct_option')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('admin.past_papers.questions.index', [$stream, $paper->id]) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
