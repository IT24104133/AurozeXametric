@extends('layouts.dashboard')

@section('title', 'Students Created')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Students Created</h1>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <p class="mb-6 text-sm text-slate-600">
            Give each student their Student ID and temporary password.
        </p>

        <div class="overflow-auto rounded-3xl border border-slate-200">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-teal-50 to-sky-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Student ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Temporary Password</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($created as $row)
                        <tr class="border-t border-slate-200 hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono text-slate-900">{{ $row['student_id'] }}</td>
                            <td class="px-4 py-3 font-mono text-slate-900">{{ $row['temp_password'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('admin.students.bulk.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white px-4 py-2 rounded-2xl hover:shadow-md transition font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create more students
            </a>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 border border-slate-300 text-slate-700 px-4 py-2 rounded-2xl hover:bg-slate-50 transition font-bold">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
