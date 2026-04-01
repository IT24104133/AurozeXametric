@extends('layouts.app')

@section('content')
<div
  x-data="attemptUI({
    attemptId: {{ $attempt->id }},
    examId: {{ $exam->id }},
    examTitle: @js($exam->title),
    instructions: @js($exam->instructions ?? 'Answer all questions before time ends.'),
    serverNow: {{ (int) $server_now }},
    expiresAt: {{ $expires_at ? (int)$expires_at : 'null' }},
  })"
  x-init="init()"
  class="min-h-screen bg-slate-50 exam-attempt-page"
>

  <style>
    .exam-attempt-page .exam-question-text,
    .exam-attempt-page .exam-option-text,
    .exam-attempt-page .exam-option-key {
      font-size: 0.85rem !important;
      line-height: 1.5 !important;
    }
  </style>

  <!-- Top Header (Mobile) -->
  <header class="lg:hidden sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">

        <!-- Left -->
        <div class="flex items-start gap-3 sm:gap-4 min-w-0">
          <div class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl sm:rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-5 w-5 sm:h-6 sm:w-6 object-contain">
          </div>

          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-[10px] sm:text-[11px] font-extrabold tracking-widest text-teal-700">EXAMPORTAL</div>
              <span class="inline-flex items-center gap-2 px-2 py-0.5 sm:px-3 sm:py-1 rounded-full bg-teal-50 text-teal-800 ring-1 ring-teal-100 text-[10px] sm:text-[11px] font-extrabold">
                <span class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full bg-emerald-400"></span>
                Live Attempt
              </span>
            </div>

            <div class="mt-1 text-base sm:text-xl lg:text-2xl font-extrabold text-slate-900 line-clamp-1" x-text="examTitle"></div>
            <div class="mt-1 text-xs sm:text-sm text-slate-600 hidden sm:block" x-text="instructions"></div>
          </div>
        </div>

        <!-- Right -->
        <div class="flex flex-row items-center justify-between gap-2 sm:gap-3 sm:justify-end w-full sm:w-auto">
          <!-- Timer -->
          <div class="flex items-center justify-between sm:justify-start gap-2 rounded-xl sm:rounded-2xl bg-slate-900 text-white px-3 py-2 sm:px-4 sm:py-3 shadow-sm">
            <div class="leading-tight">
              <div class="text-[10px] sm:text-[11px] opacity-80 font-semibold">Time Left</div>
              <div class="text-sm sm:text-lg font-extrabold tabular-nums" x-text="timeLeftText"></div>
            </div>
            <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center">
              ⏱️
            </div>
          </div>

          <!-- Submit -->
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 min-h-[44px] h-11 sm:h-12 px-4 sm:px-5 py-2.5 sm:py-3 text-sm sm:text-base rounded-xl sm:rounded-2xl bg-rose-600 text-white font-extrabold hover:bg-rose-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed active:scale-95"
            @click="confirmSubmit()"
            :disabled="loadingSubmit || locked"
          >
            <span x-show="!loadingSubmit">Submit</span>
            <span x-show="loadingSubmit" x-cloak>Submitting…</span>
            <span aria-hidden="true" class="hidden xs:inline">→</span>
          </button>
        </div>

      </div>
    </div>
  </header>

  <!-- Top Header (Desktop) -->
  <header class="hidden lg:block sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <!-- Left -->
        <div class="flex items-start gap-3 min-w-0">
          <div class="h-11 w-11 rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
          </div>

          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-[11px] font-extrabold tracking-widest text-teal-700">EXAMPORTAL</div>
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-800 ring-1 ring-teal-100 text-[11px] font-extrabold">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Live Attempt
              </span>
            </div>

            <div class="mt-1 text-xl sm:text-2xl font-extrabold text-slate-900 truncate" x-text="examTitle"></div>
            <div class="mt-1 text-sm text-slate-600 line-clamp-2" x-text="instructions"></div>
          </div>
        </div>

        <!-- Right -->
        <div class="flex flex-row items-center justify-between gap-2 sm:flex-row sm:items-center sm:justify-start lg:justify-end">
          <!-- Timer -->
          <div class="flex items-center justify-between sm:justify-start gap-2 rounded-2xl bg-slate-900 text-white px-3 py-2 sm:px-4 sm:py-3 shadow-sm">
            <div class="leading-tight">
              <div class="text-[10px] sm:text-[11px] opacity-80 font-semibold">Time Left</div>
              <div class="text-base sm:text-lg font-extrabold tabular-nums" x-text="timeLeftText"></div>
            </div>
            <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center">
              ⏱️
            </div>
          </div>

          <!-- Submit -->
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 h-10 sm:h-12 px-4 sm:px-5 py-2 sm:py-3 text-sm sm:text-base rounded-2xl bg-rose-600 text-white font-extrabold hover:bg-rose-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
            @click="confirmSubmit()"
            :disabled="loadingSubmit"
          >
            <span x-show="!loadingSubmit">Submit</span>
            <span x-show="loadingSubmit" x-cloak>Submitting…</span>
            <span aria-hidden="true">→</span>
          </button>
        </div>

      </div>
    </div>
  </header>

  <!-- Page Layout (Mobile) -->
  <div class="lg:hidden max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 pb-32">

    <!-- Main Question Panel -->
    <main>
      <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200 overflow-hidden">

        <!-- Top meta -->
        <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-slate-200 flex flex-col xs:flex-row xs:items-center xs:justify-between gap-2 sm:gap-3">
          <div class="text-sm text-slate-600">
            Question
            <span class="font-extrabold text-slate-900 tabular-nums" x-text="currentIndex + 1"></span>
            <span class="text-slate-400">/</span>
            <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
          </div>

          <div class="flex items-center gap-2 sm:gap-3">
            <div class="text-xs text-slate-500" x-show="saving" x-cloak>Saving…</div>

            <span
              class="text-[10px] sm:text-xs font-extrabold px-2.5 py-1 sm:px-3 rounded-full ring-1"
              :class="statusPillClass()"
              x-text="statusText()"
            ></span>
          </div>
        </div>

        <!-- Body -->
        <div class="p-4 sm:p-5 lg:p-6">

          <!-- Loading skeleton -->
          <div x-show="loadingQuestion" x-cloak class="space-y-3">
            <div class="h-6 bg-slate-100 rounded-xl w-2/3"></div>
            <div class="h-4 bg-slate-100 rounded-xl w-full"></div>
            <div class="h-4 bg-slate-100 rounded-xl w-5/6"></div>
            <div class="h-44 bg-slate-100 rounded-2xl w-full mt-6"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
          </div>

          <!-- Question content -->
          <template x-if="!loadingQuestion && question">
            <div class="space-y-4 sm:space-y-6">

              <!-- Question Header Row -->
              <div class="flex items-start gap-3 sm:gap-4">
                <div class="shrink-0">
                  <div class="h-11 w-11 sm:h-12 sm:w-12 lg:h-14 lg:w-14 rounded-xl sm:rounded-2xl bg-teal-50 ring-1 ring-teal-100 flex items-center justify-center">
                    <span class="text-lg sm:text-xl lg:text-2xl font-extrabold text-teal-800 tabular-nums" x-text="currentIndex + 1"></span>
                  </div>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex flex-col gap-2 sm:gap-0 sm:flex-row sm:items-start sm:justify-between">
                    <div class="font-extrabold text-slate-900 exam-question-text text-sm sm:text-base" x-html="question.text"></div>

                    <div class="sm:text-right shrink-0">
                      <div class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Status</div>
                      <div class="text-xs sm:text-sm font-extrabold" :class="statusTextClass()" x-text="statusText()"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Images -->
              <template x-if="question.images && question.images.length">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                  <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wider mb-3">Reference Images</div>

                  <div
                    class="grid gap-4"
                    :class="question.images.length === 1
                      ? 'grid-cols-1'
                      : question.images.length === 2
                        ? 'grid-cols-1 sm:grid-cols-2'
                        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'"
                  >
                    <template x-for="(img, idx) in question.images" :key="idx">
                      <a :href="img" target="_blank" class="group block">
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                          <img :src="img" alt="Question Image" class="w-full h-52 sm:h-60 object-contain p-2">
                        </div>
                        <div class="mt-2 text-xs text-slate-500 group-hover:text-slate-700 transition">Tap to open</div>
                      </a>
                    </template>
                  </div>
                </div>
              </template>

              <!-- Options -->
              <div class="space-y-2.5 sm:space-y-3">
                <div class="text-[10px] sm:text-xs font-extrabold text-slate-600 uppercase tracking-wider">Choose one answer</div>

                <template x-for="opt in question.options" :key="opt.id">
                  <button
                    type="button"
                    class="w-full text-left p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed active:scale-[0.98] min-h-[56px]"
                    :class="optionClass(opt.id)"
                    @click="selectOption(opt.id)"
                    :disabled="locked"
                  >
                    <div class="flex items-start gap-2.5 sm:gap-3">
                      <div
                        class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl flex items-center justify-center font-extrabold border-2 shrink-0 exam-option-key text-sm sm:text-base"
                        :class="bubbleClass(opt.id)"
                        x-text="opt.key"
                      ></div>

                      <div class="min-w-0 w-full">
                        <div class="text-slate-900 font-semibold leading-relaxed exam-option-text text-sm sm:text-base"
                          x-html="opt.text && opt.text.trim() ? opt.text : '(See image)'"></div>

                        <template x-if="opt.image">
                          <div class="mt-3">
                            <a :href="opt.image" target="_blank" class="block">
                              <img :src="opt.image" alt="Option Image" class="max-h-48 sm:max-h-64 rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-2 object-contain">
                            </a>
                            <div class="mt-1 text-xs text-slate-500">Tap to open</div>
                          </div>
                        </template>
                      </div>
                    </div>
                  </button>
                </template>
              </div>

              <!-- Actions -->
              <div class="pt-2 flex flex-col gap-2.5 sm:gap-3">
                <div class="flex items-center justify-between gap-2 sm:gap-3">
                  <button
                    type="button"
                    class="flex-1 sm:flex-none sm:w-auto px-4 sm:px-5 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 text-slate-900 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed min-h-[48px]"
                    @click="prev()"
                    :disabled="currentIndex === 0 || loadingQuestion || locked"
                  >
                    <span class="hidden xs:inline">← </span>Prev
                  </button>

                  <button
                    type="button"
                    class="flex-1 sm:flex-none sm:w-auto px-4 sm:px-5 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 text-slate-900 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed min-h-[48px]"
                    @click="clearAnswer()"
                    :disabled="locked"
                  >
                    Clear
                  </button>

                  <template x-if="currentIndex === totalQuestions - 1">
                    <button
                      type="button"
                      class="flex-1 sm:flex-none sm:w-auto px-4 sm:px-5 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl bg-rose-600 text-white hover:bg-rose-700 font-extrabold text-sm transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed min-h-[48px]"
                      @click="confirmSubmit()"
                      :disabled="loadingSubmit || locked"
                    >
                      <span x-show="!loadingSubmit">Submit</span>
                      <span x-show="loadingSubmit" x-cloak>Submitting…</span>
                    </button>
                  </template>

                  <template x-if="currentIndex !== totalQuestions - 1">
                    <button
                      type="button"
                      class="flex-1 sm:flex-none sm:w-auto px-4 sm:px-5 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl bg-teal-600 text-white hover:bg-teal-500 font-extrabold text-sm transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed min-h-[48px]"
                      @click="next()"
                      :disabled="loadingQuestion || locked"
                    >
                      Next<span class="hidden xs:inline"> →</span>
                    </button>
                  </template>
                </div>
              </div>

              <!-- Error -->
              <div x-show="errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
                <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
              </div>

            </div>
          </template>

          <!-- Fallback error -->
          <div x-show="!loadingQuestion && !question && errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
            <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
          </div>

        </div>
      </div>
    </main>
  </div>

  <!-- Page Layout (Desktop) -->
  <div class="hidden lg:grid max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid-cols-1 lg:grid-cols-12 gap-6 pb-28 lg:pb-6">

    <!-- Main Question Panel -->
    <main class="lg:col-span-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">

        <!-- Top meta -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="text-sm text-slate-600">
            Question
            <span class="font-extrabold text-slate-900 tabular-nums" x-text="currentIndex + 1"></span>
            <span class="text-slate-400">/</span>
            <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
          </div>

          <div class="flex items-center gap-3">
            <div class="text-xs text-slate-500" x-show="saving" x-cloak>Saving…</div>

            <span
              class="text-xs font-extrabold px-3 py-1 rounded-full ring-1"
              :class="statusPillClass()"
              x-text="statusText()"
            ></span>
          </div>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6">

          <!-- Loading skeleton -->
          <div x-show="loadingQuestion" x-cloak class="space-y-3">
            <div class="h-6 bg-slate-100 rounded-xl w-2/3"></div>
            <div class="h-4 bg-slate-100 rounded-xl w-full"></div>
            <div class="h-4 bg-slate-100 rounded-xl w-5/6"></div>
            <div class="h-44 bg-slate-100 rounded-2xl w-full mt-6"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
            <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
          </div>

          <!-- Question content -->
          <template x-if="!loadingQuestion && question">
            <div class="space-y-6">

              <!-- Question Header Row -->
              <div class="flex items-start gap-4">
                <div class="shrink-0">
                  <div class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl bg-teal-50 ring-1 ring-teal-100 flex items-center justify-center">
                    <span class="text-xl sm:text-2xl font-extrabold text-teal-800 tabular-nums" x-text="currentIndex + 1"></span>
                  </div>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                    <div class="font-extrabold text-slate-900 exam-question-text" x-html="question.text"></div>

                    <div class="sm:text-right">
                      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</div>
                      <div class="text-sm font-extrabold" :class="statusTextClass()" x-text="statusText()"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Images -->
              <template x-if="question.images && question.images.length">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                  <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wider mb-3">Reference Images</div>

                  <div
                    class="grid gap-4"
                    :class="question.images.length === 1
                      ? 'grid-cols-1'
                      : question.images.length === 2
                        ? 'grid-cols-1 sm:grid-cols-2'
                        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'"
                  >
                    <template x-for="(img, idx) in question.images" :key="idx">
                      <a :href="img" target="_blank" class="group block">
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                          <img :src="img" alt="Question Image" class="w-full h-52 sm:h-60 object-contain p-2">
                        </div>
                        <div class="mt-2 text-xs text-slate-500 group-hover:text-slate-700 transition">Tap to open</div>
                      </a>
                    </template>
                  </div>
                </div>
              </template>

              <!-- Options -->
              <div class="space-y-3">
                <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wider">Choose one answer</div>

                <template x-for="opt in question.options" :key="opt.id">
                  <button
                    type="button"
                    class="w-full text-left p-4 rounded-2xl border transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                    :class="optionClass(opt.id)"
                    @click="selectOption(opt.id)"
                    :disabled="locked"
                  >
                    <div class="flex items-start gap-3">
                      <div
                        class="h-9 w-9 rounded-xl flex items-center justify-center font-extrabold border shrink-0 exam-option-key"
                        :class="bubbleClass(opt.id)"
                        x-text="opt.key"
                      ></div>

                      <div class="min-w-0 w-full">
                        <div class="text-slate-900 font-semibold leading-relaxed exam-option-text"
                          x-html="opt.text && opt.text.trim() ? opt.text : '(See image)'"></div>

                        <template x-if="opt.image">
                          <div class="mt-3">
                            <a :href="opt.image" target="_blank" class="block">
                              <img :src="opt.image" alt="Option Image" class="max-h-64 rounded-2xl border border-slate-200 bg-white p-2 object-contain">
                            </a>
                            <div class="mt-1 text-xs text-slate-500">Tap to open</div>
                          </div>
                        </template>
                      </div>
                    </div>
                  </button>
                </template>
              </div>

              <!-- Actions -->
              <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <button
                  type="button"
                  class="w-full sm:w-auto px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold transition disabled:opacity-60 disabled:cursor-not-allowed"
                  @click="prev()"
                  :disabled="currentIndex === 0 || loadingQuestion"
                >
                  ← Prev
                </button>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                  <button
                    type="button"
                    class="w-full sm:w-auto px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold transition disabled:opacity-60 disabled:cursor-not-allowed"
                    @click="clearAnswer()"
                    :disabled="locked"
                  >
                    Clear
                  </button>

                  <template x-if="currentIndex === totalQuestions - 1">
                    <button
                      type="button"
                      class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-rose-600 text-white hover:bg-rose-700 font-extrabold transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                      @click="confirmSubmit()"
                      :disabled="loadingSubmit || locked"
                    >
                      <span x-show="!loadingSubmit">Submit</span>
                      <span x-show="loadingSubmit" x-cloak>Submitting…</span>
                    </button>
                  </template>

                  <template x-if="currentIndex !== totalQuestions - 1">
                    <button
                      type="button"
                      class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-teal-600 text-white hover:bg-teal-500 font-extrabold transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                      @click="next()"
                      :disabled="loadingQuestion"
                    >
                      Next →
                    </button>
                  </template>
                </div>
              </div>

              <!-- Error -->
              <div x-show="errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
                <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
              </div>

            </div>
          </template>

          <!-- Fallback error -->
          <div x-show="!loadingQuestion && !question && errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
            <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
          </div>

        </div>
      </div>
    </main>

    <!-- Right Sidebar -->
    <aside class="hidden lg:block lg:col-span-4">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sticky top-24">
        <div class="flex items-center justify-between mb-4">
          <div class="font-extrabold text-slate-900">Questions</div>
          <div class="text-xs text-slate-500">
            Answered:
            <span class="font-extrabold text-slate-900 tabular-nums" x-text="answeredCount"></span>
            /
            <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
          </div>
        </div>

        <div class="grid grid-cols-6 gap-2">
          <template x-for="(qid, idx) in questionOrder" :key="qid">
            <button
              type="button"
              class="h-10 rounded-xl border font-extrabold text-sm transition"
              :class="paletteClass(idx, qid)"
              @click="jump(idx)"
            >
              <span x-text="idx + 1"></span>
            </button>
          </template>
        </div>

        <div class="mt-5">
          <a
            class="block text-center px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold transition"
            href="{{ route('student.exams.review', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
          >
            Review Page →
          </a>
        </div>
      </div>
    </aside>

  </div>

  <!-- Mobile Bottom Palette -->
  <div class="lg:hidden fixed inset-x-0 bottom-0 z-40">
    <div class="bg-white/90 backdrop-blur border-t border-slate-200">
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between gap-3">
          <div class="text-xs text-slate-600">
            Answered:
            <span class="font-extrabold text-slate-900 tabular-nums" x-text="answeredCount"></span>
            /
            <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
          </div>

          <a
            class="px-3 py-2 rounded-2xl bg-teal-50 text-teal-800 ring-1 ring-teal-100 text-xs font-extrabold"
            href="{{ route('student.exams.review', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
          >
            Review →
          </a>
        </div>

        <div class="mt-3 overflow-x-auto">
          <div class="flex gap-2 min-w-max">
            <template x-for="(qid, idx) in questionOrder" :key="qid">
              <button
                type="button"
                class="h-10 w-10 rounded-xl border font-extrabold text-sm transition shrink-0 disabled:opacity-60 disabled:cursor-not-allowed"
                :class="paletteClass(idx, qid)"
                @click="jump(idx)"
                :disabled="locked"
              >
                <span x-text="idx + 1"></span>
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Back-block Popup -->
  <div x-show="backPopup" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative w-[92%] max-w-md rounded-3xl bg-white shadow-xl border border-slate-200 p-6">
      <div class="text-lg font-extrabold text-slate-900">⚠️ Action blocked</div>
      <div class="mt-2 text-sm text-slate-700">
        You can't go back during the exam attempt. Please submit the exam first.
      </div>

      <div class="mt-5 flex justify-end">
        <button
          type="button"
          class="px-4 py-2 rounded-2xl bg-teal-600 text-white font-extrabold hover:bg-teal-500"
          @click="backPopup=false"
        >
          OK
        </button>
      </div>
    </div>
  </div>

  <!-- ✅ Submit/Timeout Popup (Custom) -->
  <div x-show="submitPopupOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="submitPopupOpen=false"></div>

    <div class="relative w-[92%] max-w-md rounded-3xl bg-white shadow-xl border border-slate-200 p-6">
      <div class="text-lg font-extrabold text-slate-900" x-text="submitPopupTitle"></div>

      <div class="mt-2 text-sm text-slate-700 whitespace-pre-wrap" x-text="submitPopupMessage"></div>

      <template x-if="submitPopupLink">
        <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200">
          <div class="text-xs font-bold text-slate-500 mb-1">Link</div>

          <div class="flex items-center gap-2">
            <a :href="submitPopupLink" target="_blank" class="text-sm font-bold text-teal-700 break-all hover:underline" x-text="submitPopupLink"></a>

            <button
              type="button"
              class="shrink-0 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-extrabold hover:bg-slate-800"
              x-show="submitPopupShowCopy"
              @click="copySubmitPopupLink()"
            >
              Copy
            </button>
          </div>

          <div class="mt-2 text-xs text-emerald-600 font-bold" x-show="submitPopupCopied" x-cloak>
            Copied ✅
          </div>
        </div>
      </template>

      <div class="mt-6 flex justify-end gap-2">
        <button
          type="button"
          class="px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold hover:bg-slate-50"
          x-show="submitPopupType==='confirm'"
          @click="submitPopupOpen=false"
        >
          Cancel
        </button>

        <button
          type="button"
          class="px-4 py-2 rounded-2xl bg-teal-600 text-white font-extrabold hover:bg-teal-500 disabled:opacity-60"
          :disabled="loadingSubmit"
          @click="handleSubmitPopupOK()"
        >
          <span x-show="!loadingSubmit">OK</span>
          <span x-show="loadingSubmit" x-cloak>Submitting…</span>
        </button>
      </div>
    </div>
  </div>

</div>

<script>
function attemptUI({ attemptId, examId, examTitle, instructions, serverNow, expiresAt }) {
  return {
    attemptId,
    examId,
    examTitle,
    instructions,

    questionOrder: [],
    answers: {},
    totalQuestions: 0,

    currentIndex: 0,
    question: null,
    loadingQuestion: true,
    saving: false,
    loadingSubmit: false,
    errorMessage: '',
    locked: false,

    timeLeftSec: 0,
    timerId: null,

    expiresAtTs: null,
    serverOffsetMs: 0,

    backPopup: false,

    // ✅ submit popup state
    submitPopupOpen: false,
    submitPopupType: 'confirm', // confirm | info
    submitPopupTitle: '',
    submitPopupMessage: '',
    submitPopupLink: '',
    submitPopupShowCopy: false,
    submitPopupCopied: false,
    submitPopupAction: null,

    openSubmitPopup({ type='confirm', title='', message='', link=null, show_copy=false, onOk=null }) {
      this.submitPopupType = type;
      this.submitPopupTitle = title;
      this.submitPopupMessage = message;
      this.submitPopupLink = link || '';
      this.submitPopupShowCopy = !!show_copy;
      this.submitPopupCopied = false;
      this.submitPopupAction = onOk;
      this.submitPopupOpen = true;
    },

    handleSubmitPopupOK() {
      const action = this.submitPopupAction;
      this.submitPopupOpen = false;
      if (typeof action === 'function') action();
    },

    async copySubmitPopupLink() {
      if (!this.submitPopupLink) return;
      try {
        await navigator.clipboard.writeText(this.submitPopupLink);
      } catch {
        const ta = document.createElement('textarea');
        ta.value = this.submitPopupLink;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }
      this.submitPopupCopied = true;
      setTimeout(() => (this.submitPopupCopied = false), 1500);
    },

    statusText() {
      if (!this.question) return '';
      const qid = this.question.id;
      return this.answers[qid] ? 'Answered' : 'Not Answered';
    },

    statusPillClass() {
      if (!this.question) return 'ring-slate-200 bg-slate-50 text-slate-700';
      const qid = this.question.id;
      return this.answers[qid]
        ? 'ring-emerald-200 bg-emerald-50 text-emerald-800'
        : 'ring-slate-200 bg-slate-50 text-slate-700';
    },

    statusTextClass() {
      if (!this.question) return 'text-slate-500';
      const qid = this.question.id;
      return this.answers[qid] ? 'text-emerald-700' : 'text-slate-500';
    },

    get timeLeftText() {
      const s = Math.max(0, this.timeLeftSec);
      const h = Math.floor(s / 3600);
      const m = Math.floor((s % 3600) / 60);
      const ss = s % 60;
      const pad = (n) => String(n).padStart(2, '0');
      return (h > 0) ? `${pad(h)}:${pad(m)}:${pad(ss)}` : `${pad(m)}:${pad(ss)}`;
    },

    get answeredCount() {
      return Object.keys(this.answers || {}).length;
    },

    csrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    },

    async jsonOrThrow(res) {
      const text = await res.text();
      try { return JSON.parse(text); }
      catch { throw new Error(`Request failed (${res.status}). Please refresh and try again.`); }
    },

    setupBackBlock() {
      try { history.pushState({ examAttempt: true }, '', location.href); } catch (_) {}

      window.addEventListener('popstate', () => {
        if (this.locked) return;
        try { history.pushState({ examAttempt: true }, '', location.href); } catch (_) {}
        this.backPopup = true;
      });

      window.addEventListener('beforeunload', (e) => {
        if (!this.locked) {
          e.preventDefault();
          e.returnValue = '';
        }
      });

      window.addEventListener('pageshow', async (e) => {
        if (e.persisted && !this.locked) {
          await this.loadMeta();
        }
      });
    },

    async init() {
      this.setupBackBlock();

      const saved = localStorage.getItem(`attempt:${this.attemptId}:idx`);
      if (saved !== null && !isNaN(parseInt(saved))) {
        this.currentIndex = Math.max(0, parseInt(saved));
      }

      await this.loadMeta();
      await this.loadQuestion(this.currentIndex);
      this.startTimer();
    },

    async loadMeta() {
      this.errorMessage = '';
      try {
        const res = await fetch(`/student/attempts/${this.attemptId}/meta`, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });

        const data = await this.jsonOrThrow(res);
        if (!res.ok || !data.ok) throw new Error(data.message || 'Failed to load attempt meta');

        this.questionOrder = data.question_order || [];
        this.answers = data.answers || {};
        this.totalQuestions = this.questionOrder.length;

        const status = data.attempt?.status;
        if (status === 'submitted' || status === 'auto_submitted') this.locked = true;

        const expiresAtTs = data.attempt?.expires_at ?? expiresAt;
        const serverNowTs  = data.attempt?.server_now ?? serverNow;

        this.expiresAtTs = expiresAtTs || null;

        const serverNowMs = (serverNowTs || 0) * 1000;
        const clientNowMs = Date.now();
        this.serverOffsetMs = serverNowMs - clientNowMs;

        this.timeLeftSec = this.expiresAtTs
          ? Math.max(0, this.expiresAtTs - Math.floor((Date.now() + this.serverOffsetMs) / 1000))
          : 0;

        if (this.currentIndex >= this.totalQuestions) this.currentIndex = 0;

      } catch (e) {
        this.errorMessage = e.message || 'Failed to load attempt meta';
      }
    },

    async loadQuestion(i) {
      this.loadingQuestion = true;
      this.errorMessage = '';
      try {
        const res = await fetch(`/student/attempts/${this.attemptId}/question?i=${i}`, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });

        const data = await this.jsonOrThrow(res);
        if (!res.ok || !data.ok) throw new Error(data.message || 'Failed to load question');

        this.question = data.question;

        if (!Array.isArray(this.question.options)) this.question.options = [];
        if (!Array.isArray(this.question.images)) this.question.images = [];

        if (data.selected !== null && data.selected !== undefined) {
          this.answers[this.question.id] = data.selected;
        }

        this.currentIndex = data.index ?? i;
        localStorage.setItem(`attempt:${this.attemptId}:idx`, String(this.currentIndex));

      } catch (e) {
        this.errorMessage = e.message || 'Failed to load question';
      } finally {
        this.loadingQuestion = false;
      }
    },

    optionClass(optionId) {
      const qid = this.question?.id;
      const selected = qid ? this.answers[qid] : null;
      if (selected === optionId) return 'border-teal-500 bg-teal-50 ring-2 ring-teal-200';
      return 'border-slate-200 bg-white hover:bg-slate-50';
    },

    bubbleClass(optionId) {
      const qid = this.question?.id;
      const selected = qid ? this.answers[qid] : null;
      if (selected === optionId) return 'border-teal-600 bg-teal-600 text-white';
      return 'border-slate-200 bg-white text-slate-700';
    },

    paletteClass(idx, qid) {
      const isCurrent = idx === this.currentIndex;
      const isAnswered = !!this.answers[qid];
      if (isCurrent) return 'border-teal-500 bg-teal-600 text-white';
      if (isAnswered) return 'border-emerald-300 bg-emerald-50 text-emerald-800';
      return 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50';
    },

    async selectOption(optionId) {
      if (this.locked || !this.question) return;
      const qid = this.question.id;
      this.answers[qid] = optionId;
      await this.saveAnswer(qid, optionId);
    },

    async clearAnswer() {
      if (this.locked || !this.question) return;
      const qid = this.question.id;
      delete this.answers[qid];
      await this.saveAnswer(qid, null);
    },

    async saveAnswer(questionId, selectedOptionId) {
      this.saving = true;
      this.errorMessage = '';
      try {
        const res = await fetch(`/student/attempts/${this.attemptId}/answers`, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.csrf(),
            'Accept': 'application/json'
          },
          body: JSON.stringify({ question_id: questionId, selected_option_id: selectedOptionId })
        });

        const data = await this.jsonOrThrow(res);
        if (!res.ok || !data.ok) throw new Error(data.message || 'Save failed');

      } catch (e) {
        this.errorMessage = e.message || 'Failed to save answer';
      } finally {
        this.saving = false;
      }
    },

    async next() {
      if (this.currentIndex >= this.totalQuestions - 1) return;
      await this.loadQuestion(this.currentIndex + 1);
    },

    async prev() {
      if (this.currentIndex <= 0) return;
      await this.loadQuestion(this.currentIndex - 1);
    },

    async jump(i) {
      if (i < 0 || i >= this.totalQuestions) return;
      await this.loadQuestion(i);
    },

    startTimer() {
      if (this.timerId) clearInterval(this.timerId);

      this.timerId = setInterval(async () => {
        if (this.locked) return;

        const nowServerSec = Math.floor((Date.now() + this.serverOffsetMs) / 1000);
        this.timeLeftSec = this.expiresAtTs ? Math.max(0, this.expiresAtTs - nowServerSec) : 0;

        if (this.timeLeftSec <= 0) {
          clearInterval(this.timerId);
          this.timerId = null;

          await this.loadMeta();

          if (this.locked) {
            window.location.href = `/student/exams`;
            return;
          }

          // ✅ show timeout popup first
          this.openSubmitPopup({
            type: 'info',
            title: '⏱️ Time is up!',
            message: 'Time is over. Your answers are saved and the exam will be submitted automatically.',
            onOk: () => this.autoSubmit()
          });
        }
      }, 500);
    },

    // ✅ replace browser confirm with custom popup
    confirmSubmit() {
      if (this.locked) return;

      this.openSubmitPopup({
        type: 'confirm',
        title: '✅ Submit Exam?',
        message: 'Are you sure you want to submit the exam now?',
        onOk: () => this.submitNow()
      });
    },

    async submitNow() {
      this.loadingSubmit = true;
      this.errorMessage = '';
      try {
        const res = await fetch(`/student/attempts/${this.attemptId}/submit`, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.csrf(),
            'Accept': 'application/json'
          },
          body: JSON.stringify({ answers: this.answers })
        });

        const data = await this.jsonOrThrow(res);
        if (!res.ok || !data.ok) throw new Error(data.message || 'Submit failed');

        this.locked = true;

        // ✅ show popup from backend (admin custom)
        const popup = data.popup || {};
        this.openSubmitPopup({
          type: 'info',
          title: popup.title || '✅ Exam submitted successfully!',
          message: popup.message || 'Your answers were saved and submitted.',
          link: popup.link || null,
          show_copy: !!popup.show_copy,
          onOk: () => window.location.href = (data.redirect || `/student/exams`)
        });

      } catch (e) {
        this.errorMessage = e.message || 'Failed to submit';
      } finally {
        this.loadingSubmit = false;
      }
    },

    async autoSubmit() {
      if (this.locked) return;
      this.loadingSubmit = true;
      this.errorMessage = '';

      try {
        const res = await fetch(`/student/attempts/${this.attemptId}/submit`, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.csrf(),
            'Accept': 'application/json'
          },
          body: JSON.stringify({ reason: 'timeout', answers: this.answers })
        });

        const data = await this.jsonOrThrow(res);
        if (!res.ok || !data.ok) throw new Error(data.message || 'Auto submit failed');

        this.locked = true;

        // ✅ show popup from backend (admin custom)
        const popup = data.popup || {};
        this.openSubmitPopup({
          type: 'info',
          title: popup.title || '⏱️ Time is up!',
          message: popup.message || 'Your answers were saved and submitted automatically.',
          link: popup.link || null,
          show_copy: !!popup.show_copy,
          onOk: () => window.location.href = (data.redirect || `/student/exams`)
        });

      } catch (e) {
        this.errorMessage = e.message || 'Auto submit failed';
      } finally {
        this.loadingSubmit = false;
      }
    }
  };
}
</script>
@endsection
