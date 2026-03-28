@extends('layouts.dashboard')

@section('title', 'Manage Past Papers')

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
                    <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Subjects</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Papers</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Past Papers</h1>
                <p class="text-slate-600 mt-1">{{ strtoupper($stream) }} Stream</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.past_papers.subjects.index', ['stream' => $stream]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Subjects
                </a>
                <a href="{{ route('admin.past_papers.papers.create', ['stream' => $stream]) }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white px-4 py-2 rounded-2xl hover:shadow-md transition font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Paper
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

        <div class="mb-6">
            <form method="GET" class="flex gap-4">
                <select name="subject_id" class="px-4 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Title</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Subject</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Year</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Duration</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Questions</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($papers as $paper)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $paper->title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->subject->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->year ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $paper->duration_minutes }}min</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($paper->category === 'edu_department')
                                    <a href="{{ route('admin.past_papers.questions.index', ['stream' => $stream, 'paper' => $paper]) }}" class="text-blue-600 hover:underline">
                                        {{ $paper->questions_count }}
                                    </a>
                                @else
                                    <span class="text-gray-700">{{ $paper->total_questions ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 text-xs font-semibold rounded {{ $paper->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($paper->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                @if($paper->category === 'edu_department')
                                    <a href="{{ route('admin.past_papers.questions.index', ['stream' => $stream, 'paper' => $paper]) }}" class="text-blue-600 hover:underline">Questions</a>
                                    <a href="{{ route('admin.past_papers.papers.edit', ['stream' => $stream, 'paper' => $paper]) }}" class="text-blue-600 hover:underline">Edit</a>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">Config Only</span>
                                    <a href="{{ route('admin.past_papers.papers.edit', ['stream' => $stream, 'paper' => $paper]) }}" class="text-blue-600 hover:underline">Configure</a>
                                @endif
                                <form method="POST" action="{{ route('admin.past_papers.papers.toggle_publish', ['stream' => $stream, 'paper' => $paper]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="text-{{ $paper->status === 'published' ? 'orange' : 'green' }}-600 hover:underline">
                                        {{ $paper->status === 'published' ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.past_papers.papers.destroy', ['stream' => $stream, 'paper' => $paper]) }}" style="display:inline;" onsubmit="return confirm('Delete this paper?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-600">No papers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
