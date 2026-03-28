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
      font-size: 0.65rem !important;
      line-height: 1.5 !important;
    }

    /* Question reference image containers */
    .question-img-container {
      position: relative;
      cursor: pointer;
      overflow: hidden;
      border-radius: 0.75rem;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      transition: all 0.2s ease;
    }

    .question-img-container:hover {
      transform: scale(1.03);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-color: #cbd5e1;
    }

    .question-img-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 0.5rem;
    }

    /* Desktop: max-width ~280px, height ~150px */
    @media (min-width: 1024px) {
      .question-img-container {
        max-width: 280px;
        height: 150px;
      }
    }

    /* Tablet */
    @media (min-width: 640px) and (max-width: 1023px) {
      .question-img-container {
        height: 130px;
      }
    }

    /* Mobile: height ~90px */
    @media (max-width: 639px) {
      .question-img-container {
        height: 90px;
      }
    }

    /* Image zoom modal */
    .image-zoom-modal {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.9);
      backdrop-filter: blur(4px);
      animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .image-zoom-modal img {
      max-width: 95%;
      max-height: 95%;
      object-fit: contain;
      border-radius: 0.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .image-zoom-close {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      width: 3rem;
      height: 3rem;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.95);
      border: 2px solid rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.5rem;
      font-weight: bold;
      color: #1e293b;
      transition: all 0.2s ease;
      z-index: 10000;
    }

    .image-zoom-close:hover {
      background: #ffffff;
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
  </style>

  <!-- Sticky Header -->
  <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 md:h-20 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
          <div class="h-10 w-10 md:h-11 md:w-11 rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm shrink-0">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-5 w-5 md:h-6 md:w-6 object-contain">
          </div>
          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-[11px] font-extrabold tracking-widest text-teal-700">EXAMPORTAL</div>
              <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 ring-1 ring-teal-100 text-[11px] font-extrabold">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Live Attempt
              </span>
            </div>
            <div class="mt-1 text-base md:text-xl font-extrabold text-slate-900 truncate" x-text="examTitle"></div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 rounded-2xl bg-slate-900 text-white px-3 py-2 md:px-4 md:py-3 shadow-sm">
            <div class="leading-tight">
              <div class="text-[10px] md:text-[11px] opacity-80 font-semibold">Time Left</div>
              <div class="text-sm md:text-lg font-extrabold tabular-nums" x-text="timeLeftText"></div>
            </div>
            <div class="h-8 w-8 md:h-9 md:w-9 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center">⏱️</div>
          </div>

          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 h-10 md:h-12 px-4 md:px-5 py-2 md:py-3 text-sm md:text-base rounded-2xl bg-rose-600 text-white font-extrabold hover:bg-rose-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
            @click="confirmSubmit()"
            :disabled="loadingSubmit || locked"
          >
            <span x-show="!loadingSubmit">Submit</span>
            <span x-show="loadingSubmit" x-cloak>Submitting…</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Fixed Workspace Container -->
    <div class="h-[calc(100vh-90px)] overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full py-3">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4 h-full">

          <!-- Left Panel: Flex container for smart content distribution -->
          <main class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col min-h-0">
            
            <!-- Header: Question metadata (non-scrolling) -->
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between shrink-0">
              <div class="text-sm text-slate-600">
                Question
                <span class="font-extrabold text-slate-900 tabular-nums" x-text="currentIndex + 1"></span>
                <span class="text-slate-400">/</span>
                <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
              </div>
              <div class="flex items-center gap-2">
                <div class="text-xs text-slate-500" x-show="saving" x-cloak>Saving…</div>
                <span class="text-xs font-extrabold px-2.5 py-1 rounded-full ring-1" :class="statusPillClass()" x-text="statusText()"></span>
              </div>
            </div>

            <!-- Section A: Question + Reference Images (flexible, scrolls if content is long) -->
            <div class="flex-1 min-h-0 overflow-y-auto px-4 pt-4 pb-24 md:pb-3">
              <!-- Loading skeleton -->
              <div x-show="loadingQuestion" x-cloak class="space-y-3">
                <div class="h-5 bg-slate-100 rounded-xl w-2/3"></div>
                <div class="h-4 bg-slate-100 rounded-xl w-full"></div>
                <div class="h-4 bg-slate-100 rounded-xl w-5/6"></div>
                <div class="h-36 bg-slate-100 rounded-2xl w-full mt-4"></div>
              </div>

              <!-- Question text -->
              <template x-if="!loadingQuestion && question">
                <div class="space-y-3">
                  <!-- Question header -->
                  <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-teal-50 ring-1 ring-teal-100 flex items-center justify-center shrink-0">
                      <span class="text-lg font-extrabold text-teal-800 tabular-nums" x-text="currentIndex + 1"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Question</div>
                      <div class="exam-question-text text-sm font-semibold text-slate-900 leading-snug max-h-[32vh] overflow-y-auto md:max-h-none md:overflow-visible" x-html="question.text"></div>
                    </div>
                  </div>

                  <!-- Reference Images (smaller, fixed-size with click-to-zoom) -->
                  <template x-if="question.images && question.images.length">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 mt-4">
                      <div class="text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-3">Reference Images (Click to zoom)</div>
                      <div class="max-h-[20vh] md:max-h-60 overflow-y-auto">
                        <div
                          class="grid gap-3"
                          :class="question.images.length === 1
                            ? 'grid-cols-1 justify-items-center'
                            : question.images.length === 2
                              ? 'grid-cols-1 sm:grid-cols-2'
                              : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'"
                        >
                          <template x-for="(img, idx) in question.images" :key="idx">
                            <div
                              class="question-img-container"
                              @click="openImageZoom(img)"
                              role="button"
                              tabindex="0"
                              @keydown.enter="openImageZoom(img)"
                            >
                              <img :src="img" :alt="'Question Reference Image ' + (idx + 1)" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 120%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22200%22 height=%22120%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%236b7280%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EImage not found%3C/text%3E%3C/svg%3E'">
                            </div>
                          </template>
                        </div>
                      </div>
                    </div>
                  </template>

                  <!-- Error message -->
                  <div x-show="errorMessage" x-cloak class="p-3 rounded-xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
                    <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
                  </div>
                </div>
              </template>

              <!-- Fallback error -->
              <div x-show="!loadingQuestion && !question && errorMessage" x-cloak class="p-3 rounded-xl bg-rose-50 text-rose-700 text-sm border border-rose-200">
                <span class="font-bold">Error: </span><span x-text="errorMessage"></span>
              </div>
            </div>

            <!-- Section B: Answer Options (shrink-0, max-h-[45%], scrolls if needed) -->
            <div class="shrink-0 max-h-[40vh] md:max-h-[45%] overflow-y-auto border-t border-slate-200 px-4 py-3">
              <template x-if="!loadingQuestion && question">
                <div class="space-y-2">
                  <div class="text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Choose one answer</div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <template x-for="opt in question.options" :key="opt.id">
                      <button
                        type="button"
                        class="w-full text-left px-3 py-2 rounded-xl border transition disabled:opacity-60 disabled:cursor-not-allowed"
                        :class="optionClass(opt.id)"
                        @click="selectOption(opt.id)"
                        :disabled="locked"
                      >
                        <!-- Option content wrapper -->
                        <div class="flex items-start gap-2">
                          <!-- Option bubble -->
                          <div
                            class="h-7 w-7 rounded-lg flex items-center justify-center font-extrabold border shrink-0 exam-option-key"
                            :class="bubbleClass(opt.id)"
                            x-text="opt.key"
                          ></div>
                          <!-- Option text + image -->
                          <div class="min-w-0 flex-1">
                            <div class="exam-option-text text-sm text-slate-900 leading-snug" x-html="opt.text && opt.text.trim() ? opt.text : '(See image)'"></div>
                            <!-- Option image (small thumbnail) -->
                            <template x-if="opt.image">
                              <div class="mt-1">
                                <img :src="opt.image" alt="Option Image" class="w-24 h-16 object-contain rounded border border-slate-200 bg-white" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 96 64%22%3E%3Crect fill=%22%23f3f4f6%22 width=%2296%22 height=%2264%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2210%22 fill=%22%236b7280%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3ENo img%3C/text%3E%3C/svg%3E'">
                              </div>
                            </template>
                          </div>
                        </div>
                      </button>
                    </template>
                  </div>
                </div>
              </template>
            </div>

            <!-- Section C: Bottom Action Bar (fixed at bottom, non-scrolling) -->
            <div class="border-t border-slate-200 px-4 py-3 bg-white shrink-0">
              <div class="flex items-center justify-between gap-2">
                <button
                  type="button"
                  class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
                  @click="prev()"
                  :disabled="currentIndex === 0 || loadingQuestion"
                >
                  ← Prev
                </button>

                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
                    @click="clearAnswer()"
                    :disabled="locked"
                  >
                    Clear
                  </button>

                  <template x-if="currentIndex === totalQuestions - 1">
                    <button
                      type="button"
                      class="px-4 py-2 rounded-xl bg-rose-600 text-white hover:bg-rose-700 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
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
                      class="px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-500 font-extrabold text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
                      @click="next()"
                      :disabled="loadingQuestion"
                    >
                      Next →
                    </button>
                  </template>
                </div>
              </div>
            </div>
          </main>

          <!-- Right Panel (Navigator) - UNCHANGED -->
          <aside class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 lg:p-5 h-full flex flex-col">
            <div class="flex items-center justify-between mb-3">
              <div class="font-extrabold text-slate-900">Questions</div>
              <div class="text-xs text-slate-500">
                Answered:
                <span class="font-extrabold text-slate-900 tabular-nums" x-text="answeredCount"></span>
                /
                <span class="font-semibold tabular-nums" x-text="totalQuestions"></span>
              </div>
            </div>

            <div class="grid grid-cols-5 gap-2 overflow-y-auto pr-1">
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

            <div class="mt-4">
              <a
                class="block text-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold transition"
                href="{{ route('student.exams.review', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}"
              >
                Review Page →
              </a>
            </div>
          </aside>

        </div>
      </div>
    </div>
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

  <!-- Image Zoom Modal -->
  <div x-show="imageZoomOpen" x-cloak class="image-zoom-modal" @click.self="closeImageZoom()" @keydown.escape.window="closeImageZoom()">
    <button class="image-zoom-close" @click="closeImageZoom()" aria-label="Close image zoom">
      ✕
    </button>
    <img :src="imageZoomSrc" alt="Zoomed Question Image" @click.stop>
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

    // Image zoom modal state
    imageZoomOpen: false,
    imageZoomSrc: '',

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

    openImageZoom(imageSrc) {
      this.imageZoomSrc = imageSrc;
      this.imageZoomOpen = true;
      document.body.style.overflow = 'hidden';
    },

    closeImageZoom() {
      this.imageZoomOpen = false;
      this.imageZoomSrc = '';
      document.body.style.overflow = '';
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
