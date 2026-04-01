@extends('layouts.dashboard')
<style>[x-cloak]{display:none!important;}</style>

@section('title', 'Manage Exams')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-gray-700 font-medium">Exams</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="mb-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-2xl font-extrabold text-slate-900">All Exams</h1>

    <button type="button"
        @click="window.dispatchEvent(new CustomEvent('open-create'))"
        class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-white font-bold hover:bg-indigo-700 shadow">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Create Exam
    </button>
  </div>

  @include('components.admin.create-exam-modal')
  @include('admin.exams.partials.edit-exam-modal')
</div>

<div id="exams-table-container">

  {{-- Mobile Cards View --}}
  <div class="sm:hidden space-y-3">
    <div class="px-2 py-2">
      <div class="text-sm text-slate-600 font-medium">Showing {{ $exams->count() }} exams</div>
    </div>

    @forelse($exams as $exam)
      @php
        $examPayload = [
          'id' => $exam->id,
          'title' => $exam->title,
          'teacher_name' => $exam->teacher_name,
          'exam_code' => $exam->exam_code,
          'description' => $exam->description,
          'instructions' => $exam->instructions,
          'duration_minutes' => $exam->duration_minutes ?? 30,
          'question_mode' => $exam->question_mode,
          'question_limit' => $exam->question_limit ?? 40,
          'selection_mode' => $exam->selection_mode ?? 'all',
          'custom_success_popup_enabled' => (bool) ($exam->custom_success_popup_enabled ?? false),
          'custom_success_popup_title' => $exam->custom_success_popup_title,
          'custom_success_popup_message' => $exam->custom_success_popup_message,
          'custom_success_popup_link' => $exam->custom_success_popup_link,
          'custom_success_popup_show_copy' => (bool) ($exam->custom_success_popup_show_copy ?? true),
        ];
      @endphp

      <div x-data="{ publish:@json($exam->status === 'published'), results:@json($exam->results_published) }" 
           class="bg-white rounded-3xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
        
        {{-- Title & Description --}}
        <div class="mb-3">
          <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700 border border-slate-200">
              ID: {{ $exam->id }}
            </span>
            @if($exam->exam_uid)
              <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                {{ $exam->exam_uid }}
              </span>
            @endif
            <h3 class="text-base font-semibold text-slate-900">{{ $exam->title }}</h3>
          </div>
          @if($exam->exam_code)
            <span class="inline-flex items-center rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-700 border border-teal-200 mt-1">
              {{ $exam->exam_code }}
            </span>
          @endif
          <p class="text-xs text-slate-500 mt-1">{{ Str::limit($exam->description ?? '-', 100) }}</p>
          <p class="text-xs text-slate-500 mt-1">Attempted: {{ $exam->attempted_count ?? 0 }} | Completed: {{ $exam->completed_count ?? 0 }}</p>
        </div>

        {{-- Chips --}}
        <div class="flex flex-wrap gap-2 mb-4">
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
            {{ $exam->duration_minutes }} min
          </span>
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-700 border border-teal-200">
            {{ ucfirst($exam->question_mode) }}
          </span>
          <span :class="publish ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200'" 
                class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border"
                x-text="publish ? 'Published' : 'Draft'"></span>
          <span :class="results ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200'" 
                class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border"
                x-text="results ? 'Published' : 'Hidden'"></span>
          @if($exam->results_published)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">Results Published</span>
          @else
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">Pending Results</span>
          @endif
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2">
          <button type="button"
            class="open-edit-btn inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-teal-100 text-teal-700 hover:bg-teal-200 border border-teal-200 transition"
            data-exam='@json($examPayload)'>
            Edit
          </button>

          <a href="{{ route('admin.exams.questions.index', $exam) }}"
            class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-teal-100 text-teal-700 hover:bg-teal-200 border border-teal-200 transition">
            Questions
          </a>

          <form method="POST" action="{{ route('admin.exams.publish', $exam) }}">
            @csrf
            <button type="submit"
              class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold border transition
                {{ $exam->status === 'published' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 border-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border-emerald-200' }}">
              {{ $exam->status === 'published' ? 'Unpublish' : 'Publish' }}
            </button>
          </form>

          @if($exam->status === 'published')
            <form method="POST" action="{{ route('admin.exams.results.toggle', $exam) }}"
                  onsubmit="return confirm('{{ $exam->results_published ? 'Hide results for this exam?' : 'Publish results for this exam?' }}');">
              @csrf
              <button type="submit"
                class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold border transition
                  {{ $exam->results_published ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 border-purple-200' }}">
                {{ $exam->results_published ? 'Hide Results' : 'Publish Results' }}
              </button>
            </form>
          @else
            <span class="text-xs text-slate-500 px-1.5">Publish the exam first</span>
          @endif

          <a href="{{ route('admin.exams.results.index', $exam) }}"
            class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-orange-100 text-orange-700 hover:bg-orange-200 border border-orange-200 transition">
            View Results
          </a>

          <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" style="display:inline;" 
                onsubmit="return confirm('Delete this exam? This action cannot be undone.\\n\\nAll associated attempts and answers will also be deleted.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-red-100 text-red-700 hover:bg-red-200 border border-red-200 transition">
              Delete
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 text-center">
        <p class="text-slate-500">No exams found.</p>
      </div>
    @endforelse
  </div>

  {{-- Desktop Table View --}}
  <div class="hidden sm:block bg-white rounded-3xl shadow-sm border border-slate-200">
    <div class="px-4 py-3 flex items-center justify-between">
      <div class="text-sm text-slate-600">Showing {{ $exams->count() }} exams</div>
      <div></div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full border-separate border-spacing-y-2">
        <thead class="bg-gradient-to-r from-teal-50 to-sky-50 sticky top-0">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Title</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Duration</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Mode</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Status</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wide">Results</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700 uppercase tracking-wide w-[30%] min-w-[240px]">Actions</th>
          </tr>
        </thead>

        <tbody>
          @foreach($exams as $exam)
          <tr x-data="{ publish:@json($exam->status === 'published'), results:@json($exam->results_published), busy:false }" class="bg-white hover:bg-slate-50 transition shadow-sm rounded-xl overflow-hidden">
            <td class="px-4 py-4 align-top max-w-md">
              <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">
                  ID: {{ $exam->id }}
                </span>
                @if($exam->exam_uid)
                  <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">
                    {{ $exam->exam_uid }}
                  </span>
                @endif
                <div class="text-sm font-semibold text-slate-900">{{ $exam->title }}</div>
              </div>
              @if($exam->exam_code)
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700 mt-1">
                  {{ $exam->exam_code }}
                </span>
              @endif
              <div class="text-xs text-slate-500 mt-1">{{ Str::limit($exam->description ?? '-', 120) }}</div>
              <div class="text-xs text-slate-500 mt-1">Attempted: {{ $exam->attempted_count ?? 0 }} | Completed: {{ $exam->completed_count ?? 0 }}</div>
            </td>

            <td class="px-4 py-4 align-top text-sm text-slate-700">{{ $exam->duration_minutes }} min</td>

            <td class="px-4 py-4 align-top text-sm">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-700 border border-teal-200">{{ ucfirst($exam->question_mode) }}</span>
            </td>

            <td class="px-4 py-4 align-top text-sm">
              <span :class="publish ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200'" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border"
                x-text="publish ? 'Published' : 'Draft'"></span>
            </td>

            <td class="px-4 py-4 align-top text-sm">
              <span :class="results ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200'" class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border"
                x-text="results ? 'Published' : 'Hidden'"></span>
              @if($exam->results_published)
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 ml-2">Results Published</span>
              @else
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 ml-2">Pending Results</span>
              @endif
            </td>

            <td class="w-[30%] min-w-[340px] px-4 py-4 align-top text-sm text-right">
  <div class="flex justify-end gap-2 flex-wrap items-center">

    {{-- Edit --}}
    @php
     $examPayload = [
    'id' => $exam->id,
    'title' => $exam->title,
    'teacher_name' => $exam->teacher_name,     'exam_code' => $exam->exam_code,    'description' => $exam->description,
    'instructions' => $exam->instructions,
    'duration_minutes' => $exam->duration_minutes ?? 30,
    'question_mode' => $exam->question_mode,

    'question_limit' => $exam->question_limit ?? 40,
    'selection_mode' => $exam->selection_mode ?? 'all',

    'custom_success_popup_enabled' => (bool) ($exam->custom_success_popup_enabled ?? false),
    'custom_success_popup_title' => $exam->custom_success_popup_title,
    'custom_success_popup_message' => $exam->custom_success_popup_message,
    'custom_success_popup_link' => $exam->custom_success_popup_link,
    'custom_success_popup_show_copy' => (bool) ($exam->custom_success_popup_show_copy ?? true),
  ];
    @endphp
    <button type="button"
      class="open-edit-btn inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-teal-100 text-teal-700 hover:bg-teal-200 border border-teal-200 transition"
      data-exam='@json($examPayload)'>
      Edit
    </button>

    {{-- Questions --}}
    <a href="{{ route('admin.exams.questions.index', $exam) }}"
      class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-teal-100 text-teal-700 hover:bg-teal-200 border border-teal-200 transition">
      Questions
    </a>

    {{-- Publish / Unpublish --}}
    <form method="POST" action="{{ route('admin.exams.publish', $exam) }}">
      @csrf
      <button type="submit"
        class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold border transition
          {{ $exam->status === 'published' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 border-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border-emerald-200' }}">
        {{ $exam->status === 'published' ? 'Unpublish' : 'Publish' }}
      </button>
    </form>

    {{-- Results Publish / Hide --}}
          @if($exam->status === 'published')
            <form method="POST" action="{{ route('admin.exams.results.toggle', $exam) }}"
                  onsubmit="return confirm('{{ $exam->results_published ? 'Hide results for this exam?' : 'Publish results for this exam?' }}');">
              @csrf
              <button type="submit"
                class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold border transition
                  {{ $exam->results_published ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 border-purple-200' }}">
                {{ $exam->results_published ? 'Hide Results' : 'Publish Results' }}
              </button>
            </form>
          @else
            <span class="text-xs text-slate-500 px-1.5">Publish the exam first</span>
          @endif

    {{-- View Results --}}
    <a href="{{ route('admin.exams.results.index', $exam) }}"
      class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-orange-100 text-orange-700 hover:bg-orange-200 border border-orange-200 transition">
      View Results
    </a>

    {{-- Delete --}}
    <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" style="display:inline;" onsubmit="return confirm('Delete this exam? This action cannot be undone.\\n\\nAll associated attempts and answers will also be deleted.');">
      @csrf
      @method('DELETE')
      <button type="submit" class="inline-flex items-center gap-1 rounded-2xl px-3 py-2 text-sm font-bold bg-red-100 text-red-700 hover:bg-red-200 border border-red-200 transition">
        Delete
      </button>
    </form>

  </div>
</td>

          </tr>
          @endforeach
        </tbody>

      </table>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.open-edit-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
      const raw = this.getAttribute('data-exam');
      if(!raw) return;
      try{
        const detail = JSON.parse(raw);
        window.dispatchEvent(new CustomEvent('open-edit', { detail }));
      }catch(err){
        console.error('Failed to parse data-exam', err);
      }
    });
  });
});
</script>
@endsection
