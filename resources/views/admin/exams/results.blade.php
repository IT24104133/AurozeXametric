@extends('layouts.dashboard')

@section('title', 'Exam Results')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-bold text-slate-900">Exam Results</h1>
      <p class="text-slate-600 mt-1">{{ $exam->title }}</p>
    </div>
    <a href="{{ route('admin.exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      Back
    </a>
  </div>

  @if($attempts->isEmpty())
    <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50 text-center">
      <p class="text-slate-600">No attempts yet.</p>
    </div>
  @else
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-teal-50 to-sky-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Student</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Email</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Status</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Score</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Submitted</th>
            </tr>
          </thead>
          <tbody>
            @foreach($attempts as $a)
              <tr class="border-t border-slate-200 hover:bg-slate-50 transition">
                <td class="px-4 py-3 text-slate-900">{{ $a->user->name ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-700">{{ $a->user->email ?? '-' }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $a->status === 'completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                    {{ ucfirst($a->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-slate-900 font-bold">{{ $a->score ?? 0 }} / {{ $a->total_questions }}</td>
                <td class="px-4 py-3 text-slate-700">{{ $a->submitted_at ? $a->submitted_at->format('M d, Y H:i') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>
@endsection
