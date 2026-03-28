@extends('layouts.dashboard')

@section('title', 'Exam Questions')

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
      <h1 class="text-3xl font-bold text-slate-900">Questions – {{ $exam->title }}</h1>

      <p class="text-sm text-slate-600 mt-2">
        Total: {{ $count }} /
        {{ $exam->question_limit ?? 40 }}
        |
        Selection: <span class="font-semibold">{{ $exam->selection_mode ?? 'all' }}</span>
        |
        Order: <span class="font-semibold">{{ $exam->question_mode ?? '-' }}</span>
      </p>

      @if(($exam->selection_mode ?? 'all') === 'manual')
        <p class="text-xs text-purple-700 mt-1 font-semibold">
          Manual mode enabled: Use "Included" checkbox to choose which questions appear in the exam.
        </p>
      @endif
    </div>

    <div class="flex gap-3">
      <a href="{{ route('admin.exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
      </a>

      <a href="{{ route('admin.exams.questions.create', $exam) }}"
         class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold rounded-2xl hover:shadow-md transition">
         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
         </svg>
         Add Question
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  @if($questions->isEmpty())
    <div class="p-4 bg-gray-50 border rounded">
      No questions added yet.
    </div>
  @else
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border p-2 w-20">No</th>
          <th class="border p-2">Question</th>

          <th class="border p-2 w-28">Included</th>

          <th class="border p-2 w-24">Images</th>
          <th class="border p-2 w-24">Options</th>
          <th class="border p-2 w-24">Correct</th>

          <th class="border p-2 w-40">Actions</th>
        </tr>
      </thead>

      <tbody>
        @foreach($questions as $q)
          @php
            $hasImg = !empty($q->image_1) || !empty($q->image_2) || !empty($q->image_3);
            $optCount = $q->options?->count() ?? 0;
            $correctOpt = $q->options?->firstWhere('is_correct', true);
            $correctKey = $correctOpt?->option_key ?? '-';
          @endphp

          <tr>
            <td class="border p-2 text-center">{{ $q->order_index }}</td>

            <td class="border p-2">
              <div class="font-medium">{{ $q->question_text }}</div>
            </td>

            {{-- ✅ Manual include/exclude toggle --}}
            <td class="border p-2 text-center">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox"
                       @checked($q->is_included)
                       onchange="toggleInclude({{ $exam->id }}, {{ $q->id }}, this)">
                <span class="text-xs {{ $q->is_included ? 'text-green-700' : 'text-gray-600' }}">
                  {{ $q->is_included ? 'Yes' : 'No' }}
                </span>
              </label>
              @if(($exam->selection_mode ?? 'all') !== 'manual')
                <div class="text-[11px] text-gray-400 mt-1">(manual only)</div>
              @endif
            </td>

            <td class="border p-2 text-center">
              @if($hasImg)
                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 border border-blue-200">Yes</span>
              @else
                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700 border">No</span>
              @endif
            </td>

            <td class="border p-2 text-center">
              <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 border">
                {{ $optCount }}
              </span>
            </td>

            <td class="border p-2 text-center">
              <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 border border-green-200">
                {{ $correctKey }}
              </span>
            </td>

            <td class="border p-2">
              <div class="flex gap-3">
                <a class="underline"
                   href="{{ route('admin.exams.questions.edit', [$exam, $q]) }}">
                   Edit
                </a>

                <form method="POST"
                      action="{{ route('admin.exams.questions.destroy', [$exam, $q]) }}"
                      onsubmit="return confirm('Delete this question?');">
                  @csrf
                  @method('DELETE')
                  <button class="underline text-red-600">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

<script>
function csrfToken(){
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.content : '';
}

async function toggleInclude(examId, questionId, checkbox){
  try{
    const res = await fetch(`/admin/exams/${examId}/questions/${questionId}/toggle-include`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });

    const data = await res.json();

    if(!res.ok || !data.ok){
      throw new Error(data.message || 'Failed to update');
    }
  } catch(e){
    checkbox.checked = !checkbox.checked; // revert UI
    alert(e.message || 'Failed');
  }
}
</script>
@endsection
