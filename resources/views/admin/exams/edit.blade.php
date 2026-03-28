@extends('layouts.app')

@section('content')
{{-- IMPORTANT: Put this once in your main layout head if not already:
  <style>[x-cloak]{display:none!important;}</style>
--}}

<div class="max-w-5xl mx-auto p-6">
  <div class="flex items-center justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Edit Exam</h1>
      <p class="text-sm text-slate-600">Update details, exam setup, and popup message.</p>
    </div>

    <a href="{{ route('admin.exams.index') }}"
       class="px-4 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold">
      ← Back to Exams
    </a>
  </div>

  @if ($errors->any())
    <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800">
      <div class="font-extrabold">Please fix the errors:</div>
      <ul class="list-disc ml-5 mt-2 text-sm">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('success'))
    <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
      {{ session('success') }}
    </div>
  @endif

  <div
    x-data="editExamWizard()"
    x-init="init()"
    class="rounded-[28px] bg-white shadow-sm border border-slate-200 overflow-hidden"
  >
    <!-- Header -->
    <div class="flex items-start justify-between gap-4 px-6 sm:px-8 py-6 border-b border-slate-200">
      <div class="flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-sm">
          ✎
        </div>
        <div>
          <div class="text-xl sm:text-2xl font-extrabold text-slate-900">Edit Exam</div>
          <div class="text-sm text-slate-600">Modern step-by-step editor</div>
        </div>
      </div>

      <div class="text-xs font-extrabold px-3 py-1 rounded-full bg-slate-50 text-slate-700 ring-1 ring-slate-200">
        ID: {{ $exam->id }}
      </div>
    </div>

    <!-- Stepper -->
    <div class="px-6 sm:px-8 pt-5">
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-full grid place-items-center font-extrabold"
               :class="step >= 1 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">1</div>
          <div class="font-extrabold" :class="step === 1 ? 'text-slate-900' : 'text-slate-500'">Details</div>
        </div>

        <div class="h-px flex-1 bg-slate-200"></div>

        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-full grid place-items-center font-extrabold"
               :class="step >= 2 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">2</div>
          <div class="font-extrabold" :class="step === 2 ? 'text-slate-900' : 'text-slate-500'">Exam Setup</div>
        </div>

        <template x-if="showPopupStep">
          <div class="contents">
            <div class="h-px flex-1 bg-slate-200"></div>
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full grid place-items-center font-extrabold"
                   :class="step >= 3 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">3</div>
              <div class="font-extrabold" :class="step === 3 ? 'text-slate-900' : 'text-slate-500'">Popup Message</div>
            </div>
          </div>
        </template>

        <div class="ml-auto">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100"
                x-text="`Step ${step} of ${stepsCount}`"></span>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.exams.update', $exam) }}" class="px-6 sm:px-8 pb-6">
      @csrf
      @method('PUT')

      <!-- Body -->
      <div class="mt-6 border border-slate-200 rounded-3xl overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200">
          <div class="font-extrabold text-slate-900" x-text="stepTitle()"></div>
          <div class="text-sm text-slate-600" x-text="stepSubtitle()"></div>
        </div>

        <div class="p-6 space-y-5">
          <!-- STEP 1 -->
          <div x-show="step === 1" x-transition>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Title <span class="text-rose-600">*</span></label>
                <input
                  name="title"
                  x-model="form.title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  required
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Teacher Name</label>
                <input
                  name="teacher_name"
                  x-model="form.teacher_name"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., Mr. John Doe"
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Description</label>
                <textarea
                  name="description"
                  x-model="form.description"
                  rows="3"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Instructions</label>
                <textarea
                  name="instructions"
                  x-model="form.instructions"
                  rows="4"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- STEP 2 -->
          <div x-show="step === 2" x-transition>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Duration (minutes)</label>
                <input
                  type="number"
                  name="duration_minutes"
                  x-model.number="form.duration_minutes"
                  min="1"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Question Order</label>
                <select
                  name="question_mode"
                  x-model="form.question_mode"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option value="ordered">Ordered</option>
                  <option value="shuffled">Shuffled</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">MCQ Answer Count</label>
                <select
                  name="option_count"
                  x-model.number="form.option_count"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option :value="3">3 options (Grade 5)</option>
                  <option :value="4">4 options (default)</option>
                  <option :value="5">5 options</option>
                </select>
                <p class="text-xs text-slate-500 mt-2">Number of answer choices per MCQ question.</p>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Question Limit</label>
                <input
                  type="number"
                  name="question_limit"
                  x-model.number="form.question_limit"
                  min="1"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                <p class="text-xs text-slate-500 mt-2">Example: 60 questions exist, student answers only 40.</p>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Selection Mode</label>
                <select
                  name="selection_mode"
                  x-model="form.selection_mode"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option value="all">All Questions</option>
                  <option value="first_n">First N</option>
                  <option value="random_n">Random N</option>
                  <option value="manual">Manual</option>
                </select>
              </div>
            </div>

            <div class="mt-4 rounded-2xl border border-slate-200 p-4">
              <label class="inline-flex items-center gap-3">
                <input
                  type="checkbox"
                  name="custom_success_popup_enabled"
                  value="1"
                  x-model="form.custom_success_popup_enabled"
                  class="h-4 w-4 rounded border-slate-300"
                >
                <span class="font-extrabold text-slate-900">Enable custom popup message (After Submit / Time Out)</span>
              </label>
              <p class="text-sm text-slate-600 mt-2">If enabled, step 3 will appear.</p>
            </div>
          </div>

          <!-- STEP 3 -->
          <div x-show="step === 3" x-transition>
            <input type="hidden" name="custom_success_popup_enabled" :value="form.custom_success_popup_enabled ? 1 : 0">

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Popup Title (optional)</label>
                <input
                  name="custom_success_popup_title"
                  x-model="form.custom_success_popup_title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="✅ Exam submitted successfully!"
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Popup Message (optional)</label>
                <textarea
                  name="custom_success_popup_message"
                  x-model="form.custom_success_popup_message"
                  rows="4"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="Example: Join WhatsApp group. Next class is tomorrow 7PM."
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-2">Popup Link (optional)</label>
                <input
                  name="custom_success_popup_link"
                  x-model="form.custom_success_popup_link"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="https://chat.whatsapp.com/xxxxxx"
                >
              </div>

              <label class="inline-flex items-center gap-3">
                <input
                  type="checkbox"
                  name="custom_success_popup_show_copy"
                  value="1"
                  x-model="form.custom_success_popup_show_copy"
                  class="h-4 w-4 rounded border-slate-300"
                >
                <span class="font-extrabold text-slate-900">Show Copy button (when link exists)</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-6 flex items-center justify-between">
        <button
          type="button"
          class="px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold disabled:opacity-50"
          @click.prevent="back()"
          :disabled="step === 1"
        >
          ← Back
        </button>

        <div class="flex items-center gap-3">
          <a
            href="{{ route('admin.exams.index') }}"
            class="px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold"
          >
            Cancel
          </a>

          <button
            type="button"
            class="px-6 py-3 rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700 font-extrabold shadow-sm"
            @click.prevent="nextOrSubmit()"
          >
            <span x-text="step < stepsCount ? 'Next →' : 'Update Exam'"></span>
          </button>

          <button type="submit" x-ref="realSubmit" class="hidden"></button>
        </div>
      </div>
    </form>
  </div>

  <script>
    function editExamWizard() {
      return {
        step: 1,
        form: {
          title: @js(old('title', $exam->title)),
          teacher_name: @js(old('teacher_name', $exam->teacher_name ?? '')),
          description: @js(old('description', $exam->description)),
          instructions: @js(old('instructions', $exam->instructions)),
          duration_minutes: Number(@js(old('duration_minutes', $exam->duration_minutes ?? 30))),
          question_mode: @js(old('question_mode', $exam->question_mode ?? 'ordered')),
          option_count: Number(@js(old('option_count', $exam->option_count ?? 4))),
          question_limit: Number(@js(old('question_limit', $exam->question_limit ?? 40))),
          selection_mode: @js(old('selection_mode', $exam->selection_mode ?? 'all')),

          custom_success_popup_enabled: Boolean(@js(old('custom_success_popup_enabled', (bool)($exam->custom_success_popup_enabled ?? false)))),
          custom_success_popup_title: @js(old('custom_success_popup_title', $exam->custom_success_popup_title ?? '')),
          custom_success_popup_message: @js(old('custom_success_popup_message', $exam->custom_success_popup_message ?? '')),
          custom_success_popup_link: @js(old('custom_success_popup_link', $exam->custom_success_popup_link ?? '')),
          custom_success_popup_show_copy: Boolean(@js(old('custom_success_popup_show_copy', (bool)($exam->custom_success_popup_show_copy ?? true)))),
        },

        init() {},

        get showPopupStep() {
          return !!this.form.custom_success_popup_enabled;
        },

        get stepsCount() {
          return this.showPopupStep ? 3 : 2;
        },

        stepTitle() {
          if (this.step === 1) return 'Basic details';
          if (this.step === 2) return 'Exam setup';
          return 'Popup message';
        },

        stepSubtitle() {
          if (this.step === 1) return 'Update title and instructions for students.';
          if (this.step === 2) return 'Configure duration, question ordering and selection rules.';
          return 'Optional: Show custom info after submit or timeout.';
        },

        back() {
          if (this.step > 1) this.step--;
        },

        validateStep() {
          // keep simple; server validation handles final
          if (this.step === 1) {
            return this.form.title && this.form.title.trim().length > 0;
          }
          if (this.step === 2) {
            return Number(this.form.duration_minutes) >= 1;
          }
          return true;
        },

        nextOrSubmit() {
          if (!this.validateStep()) return;

          if (this.step === 2 && this.showPopupStep) {
            this.step = 3;
            return;
          }

          if (this.step < this.stepsCount) {
            this.step++;
            return;
          }

          this.$refs.realSubmit.click();
        },
      }
    }
  </script>
</div>
@endsection
