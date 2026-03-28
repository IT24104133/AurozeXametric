@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 md:p-6">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
      <img src="/logo.png" alt="Logo" class="h-10 w-10">
      <div>
        <h1 class="text-2xl font-bold">Review Answers - {{ $exam->title }}</h1>
        <p class="text-sm text-gray-600">Review your answers before final submission.</p>
      </div>
    </div>

    {{-- ✅ Timer --}}
    <div class="text-sm text-gray-600">
      Time Left: <span id="timeLeft"></span>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow p-4 md:p-6 mb-6">
    {{-- ✅ Responsive grid: mobile 4 cols, tablet 6, desktop 6 --}}
    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
      @php $i = 0; @endphp
      @foreach($questions as $q)
        @php
          $i++;
          $saved = $existing[$q->id]->selected_option ?? null;
        @endphp

        <div
          class="rounded text-center cursor-pointer flex flex-col items-center justify-center
                 h-16 md:h-20 px-2
                 {{ $saved ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}"
          onclick="location.href='{{ route('student.exams.attempt', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}?goto={{ $i-1 }}'">

          <div class="font-semibold text-lg">{{ $i }}</div>

          {{-- ✅ Desktop only text (prevents mobile overflow) --}}
          <div class="hidden md:block text-xs mt-1">
            {{ $saved ? 'Answered' : 'Not Answered' }}
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="flex justify-between">
    <a href="{{ route('student.exams.attempt', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
       class="px-4 py-2 bg-slate-100 rounded">
      Back to Exam
    </a>

    <form method="POST" action="{{ route('student.exams.submit', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}">
      @csrf
      <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded">
        Submit Exam
      </button>
    </form>
  </div>
</div>

{{-- ✅ Single clean timer --}}
<script>
(function () {
  let remaining = {{ (int) $remainingSeconds }};
  const el = document.getElementById('timeLeft');
  if (!el) return;

  function render(sec) {
    sec = Math.max(0, sec);
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = sec % 60;
    const pad = (n) => String(n).padStart(2, '0');
    el.textContent = h > 0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
  }

  render(remaining);

  const timer = setInterval(() => {
    remaining--;
    render(remaining);

    if (remaining <= 0) {
      clearInterval(timer);
      // optional: refresh/redirect when time ends
      // location.reload();
    }
  }, 1000);
})();
</script>

@endsection
