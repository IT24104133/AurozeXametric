@extends('layouts.dashboard')

@section('title', 'Bulk Create Students')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Bulk Create Students</h1>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        @if ($errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200">
                <ul class="list-disc ml-5 text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.students.bulk.store') }}">
            @csrf

            <label class="block mb-2 font-bold text-slate-700">How many students?</label>
            <input
                type="number"
                name="count"
                min="1"
                max="500"
                value="{{ old('count', 10) }}"
                class="w-full border border-slate-300 rounded-2xl px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            />

            <button class="w-full bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold py-3 rounded-2xl hover:shadow-md transition">
                Generate
            </button>
        </form>
    </div>
</div>
@endsection
