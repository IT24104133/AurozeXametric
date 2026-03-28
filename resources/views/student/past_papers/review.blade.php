@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="reviewPage()">
  <!-- Header -->
  <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 lg:py-4">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <!-- Left -->
        <div class="flex items-start gap-3 min-w-0">
          <div class="h-10 w-10 lg:h-11 lg:w-11 rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-5 w-5 lg:h-6 lg:w-6 object-contain">
          </div>

          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-[10px] lg:text-[11px] font-extrabold tracking-widest text-teal-700">EXAMPORTAL</div>
              <span class="inline-flex items-center gap-2 px-2.5 py-0.5 lg:px-3 lg:py-1 rounded-full bg-teal-50 text-teal-800 ring-1 ring-teal-100 text-[10px] lg:text-[11px] font-extrabold">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Review
              </span>
            </div>

            <div class="mt-1 text-lg lg:text-xl font-extrabold text-slate-900 truncate">
              {{ $paper->title ?? $paper->subject->name }}
            </div>
            <div class="mt-0.5 text-xs lg:text-sm text-slate-600">
              Review your answers before final submission.
            </div>
          </div>
        </div>

        <!-- Right: Timer -->
        <div class="flex items-center justify-between sm:justify-start gap-2 rounded-xl lg:rounded-2xl bg-slate-900 text-white px-3 py-2 lg:px-4 lg:py-2.5 shadow-sm shrink-0">
          <div class="leading-tight">
            <div class="text-[9px] lg:text-[10px] opacity-80 font-semibold">Time Left</div>
            <div class="text-sm lg:text-base font-extrabold tabular-nums" id="timeLeft">00:00</div>
          </div>
          <div class="h-7 w-7 lg:h-8 lg:w-8 rounded-lg lg:rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-sm">
            ⏱️
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 flex flex-col gap-4 sm:gap-6">
    <!-- Question Grid Card -->
    <div class="bg-white rounded-xl sm:rounded-2xl lg:rounded-3xl shadow-sm border border-slate-200 p-4 sm:p-5 lg:p-6">
      <h2 class="text-base lg:text-lg font-bold text-slate-900 mb-3 sm:mb-4 lg:mb-5">Review Questions</h2>

      <!-- Grid: 5 cols mobile, 6 cols sm, 8 cols md, 10 cols lg+ -->
      <div 
        class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 sm:gap-2.5"
        id="questionGrid"
      >
        @foreach($questions as $q)
          @php
            $answered = isset($existingAnswers[$q->id]) && $existingAnswers[$q->id]->selected_option_id;
            $questionNumber = $loop->iteration; // 1-based for both display and query param
          @endphp

          <!-- Question Status Chip -->
          <a
            href="{{ route('student.past_papers.attempt.show', $attempt) }}?q={{ $questionNumber }}"
            data-q="{{ $questionNumber }}"
            title="Question {{ $questionNumber }}: {{ $answered ? 'Answered' : 'Not Answered' }}"
            class="question-box h-11 sm:h-12 rounded-xl border-2 font-semibold text-xs sm:text-sm transition flex flex-col items-center justify-center cursor-pointer hover:shadow-md active:scale-95
                   {{ $answered ? 'bg-emerald-50 border-emerald-200 text-emerald-900 hover:border-emerald-300' : 'bg-slate-50 border-slate-200 text-slate-700 hover:border-slate-300' }}"
          >
            <div class="font-bold text-sm sm:text-base">{{ $questionNumber }}</div>
            <div class="text-[9px] sm:text-[10px] font-medium opacity-70 hidden xs:block">
              {{ $answered ? '✓' : '○' }}
            </div>
          </a>
        @endforeach
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
      <!-- Back Button -->
      <a
        href="{{ route('student.past_papers.attempt.show', $attempt) }}"
        onclick="window.__internalNav = true"
        class="min-h-[44px] flex items-center justify-center gap-2 px-4 sm:px-6 rounded-xl border-2 border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-slate-50 hover:border-slate-400 transition active:scale-95"
      >
        <span class="hidden xs:inline">←</span> Back
      </a>

      <!-- Submit Button -->
      <button
        type="button"
        @click="submitPaper()"
        :disabled="submitting"
        class="min-h-[44px] flex items-center justify-center gap-2 px-4 sm:px-6 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 text-white font-semibold text-sm hover:from-rose-700 hover:to-pink-700 transition shadow-sm active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
      >
        <span x-show="!submitting">Submit <span class="hidden xs:inline">Paper</span> <span aria-hidden="true">→</span></span>
        <span x-show="submitting" x-cloak>Submitting...</span>
      </button>
    </div>
  </main>
</div>

<script>
function reviewPage() {
  return {
    submitting: false,
    
    async submitPaper() {
      if (this.submitting) return;
      
      const confirmed = confirm('Are you sure you want to submit this past paper?\n\nYou cannot change your answers after submission.');
      if (!confirmed) return;
      
      this.submitting = true;
      window.__internalNav = true;
      
      try {
        const response = await fetch('{{ route('student.past_papers.attempt.submit', $attempt) }}', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            answers: {},
            _token: '{{ csrf_token() }}'
          })
        });
        
        const data = await response.json();
        
        if (data.ok && data.redirect) {
          window.location.href = data.redirect;
        } else {
          alert(data.message || 'Failed to submit. Please try again.');
          window.__internalNav = false;
          this.submitting = false;
        }
      } catch (error) {
        console.error('Submit error:', error);
        alert('An error occurred while submitting. Please try again.');
        window.__internalNav = false;
        this.submitting = false;
      }
    }
  };
}
</script>

<!-- Timer Script -->
<script>
(function () {
  let remaining = {{ (int) $remainingSeconds }};
  const expiresAt = {{ (int) $expires_at }};
  const el = document.getElementById('timeLeft');
  if (!el) return;

  function formatTime(sec) {
    sec = Math.max(0, sec);
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = sec % 60;
    const pad = (n) => String(n).padStart(2, '0');
    return h > 0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
  }

  function updateTime() {
    const now = Math.floor(Date.now() / 1000);
    const diff = expiresAt - now;
    remaining = Math.max(0, diff);
    el.textContent = formatTime(remaining);

    if (remaining <= 0) {
      clearInterval(timer);
    }
  }

  updateTime();
  const timer = setInterval(updateTime, 1000);
})();

// Question box navigation with event delegation
(function() {
  const grid = document.getElementById('questionGrid');
  if (!grid) return;

  grid.addEventListener('click', function(e) {
    // Find the closest .question-box element
    const box = e.target.closest('.question-box');
    if (!box) return;

    // Set internal navigation flag to prevent beforeunload warning
    window.__internalNav = true;
    
    // Let the default <a> behavior handle navigation
  });
})();
</script>

@endsection
