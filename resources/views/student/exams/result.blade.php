@extends('layouts.app')

@section('content')
<style>
.result-question-text{font-size:.9rem;line-height:1.4}
@media (max-width:640px){.result-question-text{font-size:.85rem;line-height:1.35}}
.result-question-box{padding:.7rem .9rem!important}
.result-q-number{height:2.2rem;width:2.2rem;font-size:.9rem}
.result-badge{padding:.25rem .65rem!important;font-size:.72rem!important}
.result-option-text{font-size:.85rem;line-height:1.35}
.result-option-box{padding:.6rem .75rem!important}
.result-card{border-radius:1.25rem!important}
</style>

@php
  use App\Helpers\QuestionFormatter;
  $returnUrl = request()->query('return');
  $safeReturnUrl = ($returnUrl && str_starts_with($returnUrl, '/'))
      ? $returnUrl
      : route('student.dashboard');
@endphp

<div class="min-h-screen bg-slate-50">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4 mb-6">
      <div class="min-w-0">
        <div class="flex items-center gap-2">
          <div class="h-11 w-11 rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
          </div>
          <div class="min-w-0">
            <div class="text-[11px] font-extrabold tracking-widest text-teal-700">EXAMPORTAL</div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 truncate">
              {{ $exam->title }} – Result
            </h1>
          </div>
        </div>
        <p class="mt-2 text-sm text-slate-600">
          @if($published)
            Review your answers below. Correct answers are highlighted.
          @else
            Results are pending. Scores and solutions will appear once published.
          @endif
        </p>
      </div>

      <div class="shrink-0">
        <a href="{{ $safeReturnUrl }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-slate-800 transition">
          ← Back
        </a>
      </div>
    </div>

    {{-- SUMMARY --}}
    @php
      $score = $attempt->score ?? null;
      $total = $orderedQuestions->count();

      $status = $attempt->status ?? 'submitted';
      $statusLabel = match($status) {
        'auto_submitted' => 'Auto Submitted',
        'submitted' => 'Submitted',
        default => ucfirst(str_replace('_',' ', $status)),
      };
    @endphp

    <div class="bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6 result-card">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
          <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Score</div>
          <div class="mt-1 flex items-baseline gap-2">
            @if($published)
              <div class="text-3xl font-extrabold text-slate-900 tabular-nums">{{ $score ?? '-' }}</div>
              <div class="text-slate-500 font-bold">/ {{ $total }}</div>
            @else
              <div class="text-3xl font-extrabold text-slate-900 tabular-nums">-</div>
              <div class="text-slate-500 font-bold">/ {{ $total }}</div>
            @endif
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-extrabold ring-1 ring-slate-200 result-badge">
              Status: {{ $statusLabel }}
            </span>

            <span class="inline-flex items-center px-3 py-1 rounded-full bg-teal-50 text-teal-800 font-extrabold ring-1 ring-teal-100 result-badge">
              Attempt ID: {{ $attempt->id }}
            </span>

            {{-- ✅ Download --}}
           <!-- <a href="{{ route('student.exams.paper', ['exam'=>$exam->id, 'attempt'=>$attempt->id]) }}"
   target="_blank"
   class="inline-flex items-center px-3 py-1 rounded-full bg-slate-900 text-white font-extrabold hover:bg-slate-800 transition">
   View Exam Paper
</a> -->
          </div>
        </div>

        @if($published)
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="flex items-center gap-2 text-slate-700">
              <span class="h-3 w-3 rounded-full bg-emerald-500 inline-block"></span> Correct Answer
            </div>
            <div class="flex items-center gap-2 text-slate-700">
              <span class="h-3 w-3 rounded-full bg-rose-500 inline-block"></span> Your Wrong Answer
            </div>
            <div class="flex items-center gap-2 text-slate-700">
              <span class="h-3 w-3 rounded-full bg-slate-300 inline-block"></span> Not Answered
            </div>
            <div class="flex items-center gap-2 text-slate-700">
              <span class="h-3 w-3 rounded-full bg-indigo-500 inline-block"></span> Your Answer (if correct)
            </div>
          </div>
        @else
          <div class="text-xs text-slate-600 font-bold">Pending Results</div>
        @endif
      </div>
    </div>

    {{-- REVIEW --}}
    @if($published)
      <div class="bg-white border border-slate-200 shadow-sm overflow-hidden result-card">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between">
          <div class="font-extrabold text-slate-900 text-lg">Detailed Review</div>
          <div class="text-xs text-slate-500 font-bold">Questions: {{ $orderedQuestions->count() }}</div>
        </div>

        <div class="p-5 sm:p-6 space-y-4">
          @foreach($orderedQuestions as $index => $q)
          @php
            $ans = $answers[$q->id] ?? null;
            $studentOptionId = $ans->selected_option_id ?? null;

            $correctOpt = $q->options->firstWhere('is_correct', true);
            $correctOptionId = $correctOpt?->id;

            $isAnswered = !is_null($studentOptionId);
            $isCorrect = $isAnswered && $correctOptionId && ((int)$studentOptionId === (int)$correctOptionId);

            $headerRing = $isAnswered
              ? ($isCorrect ? 'ring-emerald-200 bg-emerald-50' : 'ring-rose-200 bg-rose-50')
              : 'ring-slate-200 bg-slate-50';
          @endphp

          <div class="border border-slate-200 overflow-hidden result-card">

            {{-- QUESTION HEADER --}}
            <div class="result-question-box flex items-start justify-between gap-4 ring-1 {{ $headerRing }}">
              <div class="flex items-start gap-3 min-w-0">
                <div class="result-q-number rounded-xl bg-white border border-slate-200 flex items-center justify-center font-extrabold text-slate-800 shrink-0 tabular-nums">
                  {{ $index + 1 }}
                </div>

                <div class="min-w-0">
                  <div class="font-semibold text-slate-900 break-words result-question-text">
                    {!! QuestionFormatter::format($q->question_text) !!}
                  </div>
                </div>
              </div>

              <div class="shrink-0 text-right">
                @if(!$isAnswered)
                  <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 font-extrabold ring-1 ring-slate-200 result-badge">Not Answered</span>
                @elseif($isCorrect)
                  <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-800 font-extrabold ring-1 ring-emerald-200 result-badge">Correct</span>
                @else
                  <span class="inline-flex items-center rounded-full bg-rose-100 text-rose-800 font-extrabold ring-1 ring-rose-200 result-badge">Wrong</span>
                @endif
              </div>
            </div>

            {{-- OPTIONS --}}
            <div class="p-4 sm:p-5 bg-white space-y-3">
              @foreach($q->options as $opt)
                @php
                  $isThisStudent = $isAnswered && ((int)$opt->id === (int)$studentOptionId);
                  $isThisCorrect = $correctOptionId && ((int)$opt->id === (int)$correctOptionId);

                  $boxClass = 'border-slate-200 bg-white';
                  $tag = '';

                  if ($isThisCorrect) { $boxClass = 'border-emerald-300 bg-emerald-50 ring-2 ring-emerald-200'; $tag = 'Correct Answer'; }
                  if ($isThisStudent && !$isThisCorrect) { $boxClass = 'border-rose-300 bg-rose-50 ring-2 ring-rose-200'; $tag = 'Your Answer (Wrong)'; }
                  if ($isThisStudent && $isThisCorrect) { $tag = 'Your Answer (Correct)'; }
                @endphp

                <div class="rounded-xl border result-option-box {{ $boxClass }}">
                  <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center font-extrabold text-slate-800 shrink-0">
                      {{ $opt->option_key }}
                    </div>

                    <div class="min-w-0 flex-1">
                      <div class="font-medium text-slate-900 break-words result-option-text">
                        {!! nl2br(e(trim($opt->option_text ?? ''))) !!}
                      </div>

                      @if($tag)
                        <div class="mt-2">
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold
                            {{ str_contains($tag, 'Correct') ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200' : '' }}
                            {{ str_contains($tag, 'Wrong') ? 'bg-rose-100 text-rose-800 ring-1 ring-rose-200' : '' }}
                            {{ $tag === 'Your Answer (Correct)' ? 'bg-indigo-100 text-indigo-800 ring-1 ring-indigo-200' : '' }}">
                            {{ $tag }}
                          </span>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            </div>
          @endforeach
          </div>
        </div>
      @else
        <div class="bg-white border border-slate-200 shadow-sm p-5 sm:p-6 result-card">
          <div class="text-sm text-slate-600">Pending Results — answers and solutions will appear after publishing.</div>
        </div>
      @endif

    <div class="mt-6 text-center text-xs text-slate-500">
      ExamPortal • Results are shown only when enabled by admin.
    </div>

  </div>
</div>
@endsection
