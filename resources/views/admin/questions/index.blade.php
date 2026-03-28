@extends('layouts.dashboard')

@section('title', 'Manage Questions')

@section('content')
<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-bold text-gray-900">Questions</h1>
    <p class="text-sm text-slate-600 mt-1">
      Exam: <span class="font-semibold">{{ $exam->title }}</span>
    </p>

    <div class="mt-2 text-xs text-slate-600">
      <span class="font-semibold">Selection Mode:</span> {{ $exam->selection_mode ?? 'all' }} |
      <span class="font-semibold">Question Limit:</span> {{ $exam->question_limit ?? 40 }}
      @if(($exam->selection_mode ?? 'all') === 'manual')
        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-800 font-semibold">
          Manual mode: use “Include” switch
        </span>
      @endif
    </div>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.exams.index') }}"
       class="px-4 py-2 rounded-lg border bg-white hover:bg-slate-50">
      ← Back
    </a>

    <a href="{{ route('admin.exams.questions.create', $exam) }}"
       class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
      + Add Question
    </a>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Question</th>
        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Included</th>
        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($questions as $q)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-slate-700">{{ $q->position }}</td>

          <td class="px-4 py-3">
            <div class="text-sm font-semibold text-slate-900">
              {{ \Illuminate\Support\Str::limit($q->question_text, 120) }}
            </div>
            <div class="text-xs text-slate-500 mt-1">
              Options: {{ $q->options?->count() ?? 0 }}
            </div>
          </td>

          <td class="px-4 py-3">
            {{-- Include toggle --}}
            <label class="inline-flex items-center gap-2">
              <input
                type="checkbox"
                class="h-5 w-5"
                @checked($q->is_included)
                onchange="toggleInclude({{ $exam->id }}, {{ $q->id }}, this)"
              >
              <span class="text-sm {{ $q->is_included ? 'text-green-700' : 'text-slate-500' }}">
                {{ $q->is_included ? 'Included' : 'Excluded' }}
              </span>
            </label>

            @if(($exam->selection_mode ?? 'all') !== 'manual')
              <div class="text-xs text-slate-400 mt-1">
                (Only used in manual mode)
              </div>
            @endif
          </td>

          <td class="px-4 py-3 text-right">
            <div class="flex justify-end gap-2">
              <a href="{{ route('admin.exams.questions.edit', [$exam, $q]) }}"
                 class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">
                Edit
              </a>

              <form method="POST" action="{{ route('admin.exams.questions.destroy', [$exam, $q]) }}"
                    onsubmit="return confirm('Delete this question?')">
                @csrf
                @method('DELETE')
                <button class="px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-sm">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="px-4 py-8 text-center text-slate-500">
            No questions yet. Click “Add Question”.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Toast --}}
<div aria-live="polite" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6">
  <div id="global-toast" class="w-full flex flex-col items-end space-y-2"></div>
</div>

<script>
function csrfToken(){
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.content : '';
}

function showToast(message, type='success'){
  const id = 't-' + Date.now();
  const container = document.getElementById('global-toast');
  if(!container) return;

  const el = document.createElement('div');
  el.id = id;
  el.className = 'pointer-events-auto max-w-sm w-full bg-white border rounded-md p-3 shadow';
  el.innerHTML = `
    <div class="flex items-start gap-3">
      <div class="flex-1 text-sm ${type==='error' ? 'text-red-700' : 'text-green-700'}">${message}</div>
      <button class="text-sm text-slate-400" onclick="document.getElementById('${id}')?.remove()">×</button>
    </div>
  `;
  container.appendChild(el);

  setTimeout(()=>{ document.getElementById(id)?.remove(); }, 3500);
}

async function toggleInclude(examId, questionId, checkbox){
  try{
    const url = `/admin/exams/${examId}/questions/${questionId}/toggle-include`;

    const res = await fetch(url, {
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

    // Update label text
    const labelSpan = checkbox.closest('label')?.querySelector('span');
    if(labelSpan){
      labelSpan.textContent = data.is_included ? 'Included' : 'Excluded';
      labelSpan.className = 'text-sm ' + (data.is_included ? 'text-green-700' : 'text-slate-500');
    }

    showToast(data.message || 'Updated');
  }catch(e){
    // revert checkbox
    checkbox.checked = !checkbox.checked;
    showToast(e.message || 'Failed', 'error');
  }
}
</script>
@endsection
