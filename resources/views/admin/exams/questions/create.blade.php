@extends('layouts.dashboard')

@section('title', 'Add Question')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Add Question {{ $nextNumber }} – {{ $exam->title }}</h1>
      <p class="text-sm text-slate-600 mt-1">
        Total: {{ $count }} / {{ $exam->question_limit ?? 40 }}
      </p>
      <p class="text-xs text-slate-500 mt-1">
        Options can be 4 or 5. Each option can have text, image, or both.
      </p>
    </div>

    <a href="{{ route('admin.exams.questions.index', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      Back to Questions
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 text-red-700 rounded border border-red-200">
      <div class="font-semibold mb-2">Please fix the errors:</div>
      <ul class="list-disc pl-5 text-sm space-y-1">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $limit = $exam->question_limit ?? 40;
  @endphp

  @if($count >= $limit)
    <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
      You already added {{ $limit }} questions.
    </div>
  @else
    <form method="POST"
          action="{{ route('admin.exams.questions.store', $exam) }}"
          class="space-y-4"
          enctype="multipart/form-data">
      @csrf

      {{-- Question Text --}}
      <div>
        <label class="block font-semibold mb-1">Question</label>
        <textarea name="question_text"
                  class="w-full border p-2 rounded"
                  rows="4"
                  required>{{ old('question_text') }}</textarea>
        @error('question_text') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>

      {{-- Optional Question Images --}}
      <div class="border rounded p-4 bg-gray-50">
        <h3 class="font-semibold mb-3">Question Images (Optional)</h3>

        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block font-semibold mb-1">Image 1 (optional)</label>
            <input type="file" name="image_1" accept="image/*" class="w-full border p-2 rounded bg-white">
            @error('image_1') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block font-semibold mb-1">Image 2 (optional)</label>
            <input type="file" name="image_2" accept="image/*" class="w-full border p-2 rounded bg-white">
            @error('image_2') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block font-semibold mb-1">Image 3 (optional)</label>
            <input type="file" name="image_3" accept="image/*" class="w-full border p-2 rounded bg-white">
            @error('image_3') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
        </div>

        <p class="text-xs text-gray-600 mt-3">
          1 image → left. 2 images → left + center. 3 images → left + center + right.
        </p>
      </div>

      {{-- Option count selector --}}
      <div class="border rounded p-4">
        <div class="flex items-center justify-between gap-4 flex-wrap">
          <div>
            <label class="block font-semibold mb-1">Number of Options</label>
            <select id="optionCount" class="border p-2 rounded w-48">
              <option value="4" selected>4 Options</option>
              <option value="5">5 Options</option>
            </select>
          </div>

          <div class="text-sm text-gray-600">
            Correct answer: select “Correct” radio inside an option.
          </div>
        </div>

        {{-- correct_index hidden --}}
        <input type="hidden" name="correct_index" id="correct_index" value="{{ old('correct_index', 0) }}">

        <div id="optionsWrap" class="mt-4 space-y-3"></div>

        <p class="text-xs text-gray-500 mt-3">
          Each option must have text or an image (or both).
        </p>
      </div>

      <button class="bg-black text-white px-4 py-2 rounded">
        Save & Add Next
      </button>
    </form>
  @endif
</div>

<script>
(function(){
  const keys = ['A','B','C','D','E'];
  const optionCountEl = document.getElementById('optionCount');
  const wrap = document.getElementById('optionsWrap');
  const correctIndexEl = document.getElementById('correct_index');

  let count = 4;

  function render(){
    wrap.innerHTML = '';

    let correctIndex = parseInt(correctIndexEl.value || '0', 10);
    if (correctIndex >= count) {
      correctIndex = 0;
      correctIndexEl.value = '0';
    }

    for(let i=0; i<count; i++){
      const div = document.createElement('div');
      div.className = 'border rounded p-3 bg-gray-50';

      // NOTE: old() repopulation for option text is not included here (simple UI).
      // If you want it, tell me—I'll add it with JSON encode.

      div.innerHTML = `
        <div class="flex items-center justify-between mb-2">
          <div class="font-semibold">Option ${keys[i]}</div>

          <label class="inline-flex items-center gap-2 text-sm">
            <input type="radio" name="correct_radio" value="${i}" ${i===correctIndex ? 'checked' : ''}>
            Correct
          </label>
        </div>

        <div class="grid grid-cols-1 gap-3">
          <div>
            <label class="block text-sm font-semibold mb-1">Option Text (optional)</label>
            <input type="text"
                   name="options[${i}][option_text]"
                   class="w-full border p-2 rounded bg-white"
                   placeholder="Type option text...">
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Option Image (optional)</label>
            <input type="file"
                   name="options[${i}][option_image]"
                   accept="image/*"
                   class="w-full border p-2 rounded bg-white">
          </div>
        </div>
      `;

      wrap.appendChild(div);
    }

    document.querySelectorAll('input[name="correct_radio"]').forEach(r => {
      r.addEventListener('change', (e) => {
        correctIndexEl.value = e.target.value;
      });
    });
  }

  optionCountEl.addEventListener('change', () => {
    count = parseInt(optionCountEl.value, 10);
    render();
  });

  render();
})();
</script>
@endsection
