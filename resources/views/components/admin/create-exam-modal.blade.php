{{-- Create Exam Modal (3-step Wizard) --}}
<div
  x-data="createExamWizardModal()"
  x-init="init()"
>
  <template x-if="isOpen">
    <div
      @keydown.escape="close()"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm px-3 py-6"
      aria-modal="true"
      role="dialog"
    >
      <!-- Backdrop Click to Close -->
      <div class="absolute inset-0" @click="close()"></div>

      <!-- Modal -->
      <div class="relative w-full max-w-2xl sm:max-w-3xl max-h-[85vh] flex flex-col rounded-[28px] bg-white shadow-2xl border border-slate-200 overflow-hidden" @click.stop>
    <!-- Header -->
    <div class="flex items-start justify-between gap-4 px-4 sm:px-6 py-4 border-b border-slate-200">
      <div class="flex items-center gap-4">
        <div class="h-10 w-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-sm">
          +
        </div>
        <div>
          <div class="text-lg sm:text-xl font-extrabold text-slate-900">Create Exam</div>
          <div class="text-sm text-slate-600">Modern step-by-step exam creation</div>
        </div>
      </div>

      <button
        type="button"
        class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700"
        @click="close()"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Stepper -->
    <div class="px-4 sm:px-6 pt-4 pb-3">
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm"
               :class="step >= 1 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">1</div>
          <div class="font-extrabold text-sm hidden sm:block" :class="step === 1 ? 'text-slate-900' : 'text-slate-500'">Details</div>
        </div>

        <div class="h-px flex-1 bg-slate-200"></div>

        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm"
               :class="step >= 2 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">2</div>
          <div class="font-extrabold text-sm hidden sm:block" :class="step === 2 ? 'text-slate-900' : 'text-slate-500'">Setup</div>
        </div>

        <template x-if="showPopupStep">
          <div class="contents">
            <div class="h-px flex-1 bg-slate-200"></div>
            <div class="flex items-center gap-2">
              <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm"
                   :class="step >= 3 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">3</div>
              <div class="font-extrabold text-sm hidden sm:block" :class="step === 3 ? 'text-slate-900' : 'text-slate-500'">Popup</div>
            </div>
          </div>
        </template>

        <div class="ml-auto">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100"
                x-text="`Step ${step} of ${stepsCount}`"></span>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.exams.store') }}" class="flex-1 overflow-y-auto px-4 sm:px-6 pb-4">
      @csrf

      <!-- Body -->
      <div class="mt-4 border border-slate-200 rounded-2xl overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
          <div class="font-extrabold text-sm text-slate-900" x-text="stepTitle()"></div>
          <div class="text-xs text-slate-600" x-text="stepSubtitle()"></div>
        </div>

        <div class="p-4 space-y-4">
          <!-- STEP 1 -->
          <div x-show="step === 1" x-transition>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Title <span class="text-rose-600">*</span></label>
                <input
                  name="title"
                  x-model="form.title"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., Mathematics Term Test"
                  required
                >
                <div class="text-xs text-rose-600 mt-1" x-show="errors.title" x-text="errors.title"></div>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Teacher Name</label>
                <input
                  name="teacher_name"
                  x-model="form.teacher_name"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., Mr. John Doe"
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Exam Code (Optional)</label>
                <input
                  name="exam_code"
                  x-model="form.exam_code"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., ABC123"
                  maxlength="50"
                >
                <p class="text-xs text-slate-500 mt-1">Leave empty for public exam. Students must enter this code to start.</p>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Description</label>
                <textarea
                  name="description"
                  x-model="form.description"
                  rows="2"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., Monthly test for Grade 10"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Instructions</label>
                <textarea
                  name="instructions"
                  x-model="form.instructions"
                  rows="3"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="e.g., No tab switching, no calculators..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- STEP 2 -->
          <div x-show="step === 2" x-transition>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Duration (minutes)</label>
                <input
                  type="number"
                  name="duration_minutes"
                  x-model.number="form.duration_minutes"
                  min="1"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                <div class="text-xs text-rose-600 mt-1" x-show="errors.duration_minutes" x-text="errors.duration_minutes"></div>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Question Order</label>
                <select
                  name="question_mode"
                  x-model="form.question_mode"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option value="ordered">Ordered</option>
                  <option value="shuffled">Shuffled</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Question Limit</label>
                <input
                  type="number"
                  name="question_limit"
                  x-model.number="form.question_limit"
                  min="1"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                <p class="text-xs text-slate-500 mt-1">Example: 60 questions exist, student answers only 40.</p>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Selection Mode</label>
                <select
                  name="selection_mode"
                  x-model="form.selection_mode"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option value="all">All Questions</option>
                  <option value="first_n">First N</option>
                  <option value="random_n">Random N</option>
                  <option value="manual">Manual</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">MCQ Options Count</label>
                <select
                  name="option_count"
                  x-model.number="form.option_count"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
                  <option :value="3">3 options (Grade 5)</option>
                  <option :value="4">4 options (default)</option>
                  <option :value="5">5 options</option>
                </select>
                <p class="text-xs text-slate-500 mt-1">Number of answer choices per question.</p>
              </div>
            </div>

            <div class="mt-3 rounded-xl border border-slate-200 p-3">
              <label class="inline-flex items-center gap-2">
                <input
                  type="checkbox"
                  name="custom_success_popup_enabled"
                  value="1"
                  x-model="form.custom_success_popup_enabled"
                  class="h-4 w-4 rounded border-slate-300"
                >
                <span class="font-extrabold text-sm text-slate-900">Enable custom popup message</span>
              </label>
              <p class="text-xs text-slate-600 mt-1.5">
                If enabled, step 3 will appear so you can set the popup content.
              </p>
            </div>
          </div>

          <!-- STEP 3 -->
          <div x-show="step === 3" x-transition>
            <input type="hidden" name="custom_success_popup_enabled" :value="form.custom_success_popup_enabled ? 1 : 0">

            <div class="space-y-3">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Popup Title (optional)</label>
                <input
                  name="custom_success_popup_title"
                  x-model="form.custom_success_popup_title"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="✅ Exam submitted successfully!"
                >
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Popup Message (optional)</label>
                <textarea
                  name="custom_success_popup_message"
                  x-model="form.custom_success_popup_message"
                  rows="3"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="Example: Join WhatsApp group. Next class is tomorrow 7PM."
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Popup Link (optional)</label>
                <input
                  name="custom_success_popup_link"
                  x-model="form.custom_success_popup_link"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                  placeholder="https://chat.whatsapp.com/xxxxxx"
                >
              </div>

              <label class="inline-flex items-center gap-2">
                <input
                  type="checkbox"
                  name="custom_success_popup_show_copy"
                  value="1"
                  x-model="form.custom_success_popup_show_copy"
                  class="h-4 w-4 rounded border-slate-300"
                >
                <span class="font-extrabold text-sm text-slate-900">Show Copy button</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
        <button
          type="button"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm disabled:opacity-50"
          @click.prevent="back()"
          :disabled="step === 1"
        >
          ← Back
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm"
            @click="close()"
          >
            Cancel
          </button>

          <button
            type="button"
            class="px-5 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-extrabold text-sm shadow-sm"
            @click.prevent="nextOrSubmit()"
          >
            <span x-text="step < stepsCount ? 'Next →' : 'Create Exam'"></span>
          </button>

          <!-- Real submit button (hidden) -->
          <button type="submit" x-ref="realSubmit" class="hidden"></button>
        </div>
      </div>
    </form>
    </div>
    <!-- End Modal -->
    </div>
    <!-- End Backdrop Wrapper -->
  </template>

  <script>
  function createExamWizardModal() {
    return {
      isOpen: false,
      step: 1,
      errors: {},

      form: {
        title: '',
        teacher_name: '',
        exam_code: '',
        description: '',
        instructions: '',
        duration_minutes: 30,
        question_mode: 'ordered',
        option_count: 4,
        question_limit: 40,
        selection_mode: 'all',

        custom_success_popup_enabled: false,
        custom_success_popup_title: '',
        custom_success_popup_message: '',
        custom_success_popup_link: '',
        custom_success_popup_show_copy: true,
      },

      init() {
        window.addEventListener('open-create', () => this.openModal());
      },

      openModal() {
        this.isOpen = true;
        this.step = 1;
        this.form = {
          title: '',
          teacher_name: '',
          exam_code: '',
          description: '',
          instructions: '',
          duration_minutes: 30,
          question_mode: 'ordered',
          option_count: 4,
          question_limit: 40,
          selection_mode: 'all',
          custom_success_popup_enabled: false,
          custom_success_popup_title: '',
          custom_success_popup_message: '',
          custom_success_popup_link: '',
          custom_success_popup_show_copy: true,
        };
        this.errors = {};
        document.body.style.overflow = 'hidden';
      },

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
        if (this.step === 1) return 'Set title and instructions for students.';
        if (this.step === 2) return 'Configure duration, question ordering and selection rules.';
        return 'Optional: Show custom info after submit or timeout.';
      },

      close() {
        this.isOpen = false;
        document.body.style.overflow = 'auto';
      },

      back() {
        if (this.step > 1) this.step--;
      },

      validateStep() {
        this.errors = {};

        if (this.step === 1) {
          if (!this.form.title || !this.form.title.trim()) {
            this.errors.title = 'Title is required.';
          }
        }

        if (this.step === 2) {
          if (!this.form.duration_minutes || this.form.duration_minutes < 1) {
            this.errors.duration_minutes = 'Duration must be at least 1 minute.';
          }
        }

        return Object.keys(this.errors).length === 0;
      },

      nextOrSubmit() {
        if (!this.validateStep()) return;

        // If popup enabled and currently in step 2, jump to step 3
        if (this.step === 2 && this.showPopupStep) {
          this.step = 3;
          return;
        }

        // Normal next
        if (this.step < this.stepsCount) {
          this.step++;
          return;
        }

        // Final submit
        this.$refs.realSubmit.click();
      },
    }
  }
</script>

</div>
