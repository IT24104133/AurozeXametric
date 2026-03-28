@extends('layouts.admin')

@section('title', 'Question Bank Health')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Question Bank Health</h1>
        <p class="mt-2 text-gray-600">Monitor available questions per subject and detect configuration conflicts</p>
    </div>

    {{-- Overview Card --}}
    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $subjects->count() }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Bank Questions</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $subjects->sum('bank_total') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Warnings</p>
                <p class="mt-2 text-2xl font-bold text-red-600">{{ $subjects->where('has_warning', true)->count() }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Healthy Subjects</p>
                <p class="mt-2 text-2xl font-bold text-green-600">{{ $subjects->where('has_warning', false)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Subjects Table --}}
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">Subject Question Bank Status</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Subject</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase">Easy (E)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-orange-700 uppercase">Medium (M)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-red-700 uppercase">Hard (H)</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.past_papers.papers.index', ['stream' => $subject->stream, 'subject' => $subject->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $subject->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 font-bold text-sm">
                                    {{ $subject->bank_total }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-bold text-sm">
                                    {{ $subject->bank_e }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-orange-100 text-orange-800 font-bold text-sm">
                                    {{ $subject->bank_m }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold text-sm">
                                    {{ $subject->bank_h }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($subject->has_warning)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-sm font-medium border border-red-200">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $subject->warning_message }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Healthy
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No subjects found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Legend</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">E</span>
                <div>
                    <p class="font-medium text-gray-900">Easy</p>
                    <p class="text-gray-600">Easy difficulty questions</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">M</span>
                <div>
                    <p class="font-medium text-gray-900">Medium</p>
                    <p class="text-gray-600">Medium difficulty questions</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-700 text-xs font-bold">H</span>
                <div>
                    <p class="font-medium text-gray-900">Hard</p>
                    <p class="text-gray-600">Hard difficulty questions</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
