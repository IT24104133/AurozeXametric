<!-- Edit Exam Modal (3-step wizard) -->
<div
  x-data="editExamModal()"
>
  <template x-if="open">
    <div
      @keydown.escape="close()"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-3 py-6"
    >
      <!-- Backdrop Click to Close -->
      <div class="absolute inset-0" @click="close()"></div>

      <!-- Modal Container -->
      <div
        class="relative w-full max-w-xl sm:max-w-2xl max-h-[85vh] flex flex-col bg-white rounded-[28px] shadow-2xl overflow-hidden"
        @click.stop
      >
    <!-- Header -->
    <div class="flex items-start justify-between gap-4 px-4 sm:px-6 py-4 border-b border-slate-200">
      <div class="flex items-center gap-4">
        <div class="h-10 w-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-sm">
          ✎
        </div>
        <div>
          <div class="text-lg sm:text-xl font-extrabold text-slate-900">Edit Exam</div>
          <div class="text-sm text-slate-600">Modern step-by-step editor</div>
        </div>
      </div>

      <button
        @click="close()"
        class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Stepper -->
    <div class="px-4 sm:px-6 pt-4 pb-3">
      <div class="flex items-center gap-3">
        <!-- Step 1 -->
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm transition-all"
               :class="step >= 1 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">1</div>
          <div class="font-extrabold text-sm hidden sm:block" :class="step === 1 ? 'text-slate-900' : 'text-slate-500'">Details</div>
        </div>

        <div class="h-px flex-1 bg-slate-200"></div>

        <!-- Step 2 -->
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm transition-all"
               :class="step >= 2 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">2</div>
          <div class="font-extrabold text-sm hidden sm:block" :class="step === 2 ? 'text-slate-900' : 'text-slate-500'">Setup</div>
        </div>

        <!-- Step 3 (conditional) -->
        <template x-if="showPopupStep">
          <div class="contents">
            <div class="h-px flex-1 bg-slate-200"></div>
            <div class="flex items-center gap-2">
              <div class="h-8 w-8 rounded-full grid place-items-center font-extrabold text-sm transition-all"
                   :class="step >= 3 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'">3</div>
              <div class="font-extrabold text-sm hidden sm:block" :class="step === 3 ? 'text-slate-900' : 'text-slate-500'">Popup</div>
            </div>
          </div>
        </template>

        <!-- Step counter badge -->
        <div class="ml-auto">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100"
                x-text="`Step ${step} of ${stepsCount}`"></span>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form method="POST" :action="`/admin/exams/${examId}`" class="flex-1 overflow-y-auto px-4 sm:px-6 pb-4">
      @csrf
      @method('PUT')

      <!-- Body -->
      <div class="border border-slate-200 rounded-2xl overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
          <div class="font-extrabold text-sm text-slate-900" x-text="stepTitle()"></div>
          <div class="text-xs text-slate-600" x-text="stepSubtitle()"></div>
        </div>

        <div class="p-4 space-y-4">
          <!-- STEP 1: Details -->
          <div x-show="step === 1" x-transition class="space-y-3">
            <div>
              <label class="block text-sm font-extrabold text-slate-900 mb-1.5">
                Title <span class="text-rose-600">*</span>
              </label>
              <input
                name="title"
                x-model="form.title"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                required
              >
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
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Instructions</label>
              <textarea
                name="instructions"
                x-model="form.instructions"
                rows="2"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
              ></textarea>
            </div>
          </div>

          <!-- STEP 2: Exam Setup -->
          <div x-show="step === 2" x-transition class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-extrabold text-slate-900 mb-1.5">Duration (minutes) <span class="text-rose-600">*</span></label>
                <input
                  type="number"
                  name="duration_minutes"
                  x-model.number="form.duration_minutes"
                  min="1"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                >
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
            </div>

            <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
              <label class="inline-flex items-center gap-2">
                <input
                  type="checkbox"
                  name="custom_success_popup_enabled"
                  value="1"
                  x-model="form.custom_success_popup_enabled"
                  class="h-4 w-4 rounded border-slate-300 cursor-pointer"
                >
                <span class="font-extrabold text-sm text-slate-900 cursor-pointer">Enable custom popup message</span>
              </label>
              <p class="text-xs text-slate-600 mt-1.5">If enabled, step 3 will appear.</p>
            </div>
          </div>

          <!-- STEP 3: Popup Message (conditional) -->
          <div x-show="step === 3" x-transition class="space-y-3">
            <input type="hidden" name="custom_success_popup_enabled" :value="form.custom_success_popup_enabled ? 1 : 0">

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
                class="h-4 w-4 rounded border-slate-300 cursor-pointer"
              >
              <span class="font-extrabold text-sm text-slate-900 cursor-pointer">Show Copy button</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Footer / Buttons -->
      <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between gap-2">
        <button
          type="button"
          @click.prevent="back()"
          :disabled="step === 1"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          ← Back
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="close()"
            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-extrabold text-sm transition-all"
          >
            Cancel
          </button>

          <button
            type="button"
            @click.prevent="nextOrSubmit()"
            :disabled="busy"
            class="px-5 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-extrabold text-sm shadow-sm transition-all disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span x-show="!busy" x-text="step < stepsCount ? 'Next →' : 'Update Exam'"></span>
            <span x-show="busy" x-cloak>Updating...</span>
          </button>

          <button type="submit" x-ref="realSubmit" class="hidden"></button>
        </div>
      </div>
    </form>
      </div>
      <!-- End Modal Container -->
    </div>
    <!-- End Backdrop Wrapper -->
  </template>

  <script>
function editExamModal() {
  return {
    step: 1,
    open: false,
    busy: false,
    examId: null,

    form: {
      title: '',
      teacher_name: '',
      exam_code: '',
      description: '',
      instructions: '',
      duration_minutes: 30,
      question_mode: 'ordered',
      question_limit: 40,
      selection_mode: 'all',
      custom_success_popup_enabled: false,
      custom_success_popup_title: '',
      custom_success_popup_message: '',
      custom_success_popup_link: '',
      custom_success_popup_show_copy: true,
    },

    init() {
      window.addEventListener('open-edit', (e) => {
        const ex = e.detail || {};
        
        // Reset step and populate form
        this.step = 1;
        this.examId = ex.id;
        this.form.title = ex.title || '';
        this.form.teacher_name = ex.teacher_name || '';
        this.form.exam_code = ex.exam_code || '';
        this.form.description = ex.description || '';
        this.form.instructions = ex.instructions || '';
        this.form.duration_minutes = Number(ex.duration_minutes ?? 30);
        this.form.question_mode = ex.question_mode || 'ordered';
        this.form.question_limit = Number(ex.question_limit ?? 40);
        this.form.selection_mode = ex.selection_mode || 'all';
        this.form.custom_success_popup_enabled = !!ex.custom_success_popup_enabled;
        this.form.custom_success_popup_title = ex.custom_success_popup_title || '';
        this.form.custom_success_popup_message = ex.custom_success_popup_message || '';
        this.form.custom_success_popup_link = ex.custom_success_popup_link || '';
        this.form.custom_success_popup_show_copy = (ex.custom_success_popup_show_copy ?? true) ? true : false;

        // Show modal and disable body scroll
        this.open = true;
        document.body.style.overflow = 'hidden';
      });
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
      if (this.step === 1) return 'Update title and instructions for students.';
      if (this.step === 2) return 'Configure duration, question ordering and selection rules.';
      return 'Optional: Show custom info after submit or timeout.';
    },

    close() {
      this.open = false;
      this.busy = false;
      document.body.style.overflow = 'auto';
    },

    back() {
      if (this.step > 1) this.step--;
    },

    validateStep() {
      if (this.step === 1) {
        return this.form.title && this.form.title.trim().length > 0;
      }
      if (this.step === 2) {
        return Number(this.form.duration_minutes) >= 1;
      }
      return true;
    },

    nextOrSubmit() {
      if (!this.validateStep()) {
        alert('Please fill in required fields before proceeding.');
        return;
      }

      if (this.step === 2 && this.showPopupStep) {
        this.step = 3;
        return;
      }

      if (this.step < this.stepsCount) {
        this.step++;
        return;
      }

      // Submit form
      this.busy = true;
      this.$refs.realSubmit.click();
    },
  }
}
</script>
