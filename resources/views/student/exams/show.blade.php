@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>
  <p class="text-gray-600 mt-1">Duration: {{ $exam->duration_minutes }} minutes</p>

  <div class="mt-4 p-4 border rounded bg-white">
    <h2 class="font-semibold mb-2">Exam Details & Conditions</h2>
    <div class="text-sm text-gray-700 whitespace-pre-line">
      {{ $exam->instructions ?? 'No special instructions.' }}
    </div>
  </div>

  <form method="POST" action="{{ route('student.exams.start', $exam) }}" class="mt-6">
    @csrf
    <button class="px-5 py-2 rounded bg-black text-white">Start Exam</button>
  </form>
</div>
@endsection
