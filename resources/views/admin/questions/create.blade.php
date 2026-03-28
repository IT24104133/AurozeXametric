@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <h2 style="margin:0;">Add Question</h2>
        <a href="{{ route('admin.exams.questions.index', $exam) }}" style="text-decoration:none;">
            ← Back to Questions
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div style="background:#e8fff0; padding:10px; border:1px solid #b7f0c8; margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error message --}}
    @if ($errors->any())
        <div style="background:#ffecec; padding:10px; border:1px solid #f5b5b5; margin-bottom:12px;">
            <b>Please fix the errors:</b>
            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.exams.questions.store', $exam) }}"
          enctype="multipart/form-data"
          style="border:1px solid #ddd; padding:16px; border-radius:10px;">

        @csrf

        {{-- Position --}}
        <div style="margin-bottom:12px;">
            <label><b>Position *</b></label><br>
            <input type="number"
                   name="position"
                   value="{{ old('position', $nextPos ?? 1) }}"
                   min="1"
                   required
                   style="width:160px; padding:8px;">
        </div>

        {{-- Question Text --}}
        <div style="margin-bottom:12px;">
            <label><b>Question Text *</b></label><br>
            <textarea name="question_text"
                      rows="4"
                      required
                      style="width:100%; padding:10px;">{{ old('question_text') }}</textarea>
        </div>

        {{-- Question Images --}}
        <div style="margin-bottom:12px;">
            <label><b>Question Images (optional)</b></label>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-top:8px;">
                <div>
                    <small>Image 1</small><br>
                    <input type="file" name="image_1" accept="image/*">
                </div>
                <div>
                    <small>Image 2</small><br>
                    <input type="file" name="image_2" accept="image/*">
                </div>
                <div>
                    <small>Image 3</small><br>
                    <input type="file" name="image_3" accept="image/*">
                </div>
            </div>
        </div>

        {{-- Explanation (optional) --}}
        <div style="margin-bottom:12px;">
            <label><b>Explanation (optional)</b></label><br>
            <textarea name="explanation"
                      rows="3"
                      style="width:100%; padding:10px;">{{ old('explanation') }}</textarea>
        </div>

        <hr style="margin:18px 0;">

        {{-- Options header --}}
        <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
            <div style="flex:1;">
                <label><b>Number of Options (fixed by exam setting)</b></label><br>
                <div style="padding:8px; background:#f0f9ff; border:1px solid #0284c7; border-radius:8px; color:#0c4a6e;">
                    <strong>{{ $optionCount }} Options</strong> (configured for this exam)
                </div>
                <div style="font-size:12px; color:#666; margin-top:6px;">
                    Tip: Each option must have <b>text</b> or an <b>image</b> (or both).
                </div>
            </div>

            <div style="text-align:right;">
                <label><b>Correct Option</b></label><br>
                <span style="font-size:13px; color:#666;">Select using “Correct” radio on an option.</span>
            </div>
        </div>

        {{-- Hidden correct_index --}}
        <input type="hidden" name="correct_index" id="correct_index" value="{{ old('correct_index', 0) }}">
        
        {{-- Hidden option_count for JS --}}
        <input type="hidden" id="optionCountValue" value="{{ $optionCount }}">

        {{-- Options container --}}
        <div id="optionsWrap"></div>

        <div style="margin-top:16px; display:flex; gap:10px;">
            <button type="submit" style="padding:10px 14px; cursor:pointer;">
                Save Question
            </button>
            <a href="{{ route('admin.exams.questions.index', $exam) }}"
               style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; text-decoration:none;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
(function () {
    const wrap = document.getElementById('optionsWrap');
    const optionCountValue = document.getElementById('optionCountValue');
    const correctIndexInput = document.getElementById('correct_index');

    const keys = ['A', 'B', 'C', 'D', 'E'];

    // Get option count from exam setting (passed from controller)
    let count = parseInt(optionCountValue.value, 10);

    function renderOptions() {
        wrap.innerHTML = '';

        let correctIndex = parseInt(correctIndexInput.value || '0', 10);
        if (correctIndex >= count) {
            correctIndex = 0;
            correctIndexInput.value = '0';
        }

        for (let i = 0; i < count; i++) {
            const card = document.createElement('div');
            card.style.border = '1px solid #ddd';
            card.style.borderRadius = '10px';
            card.style.padding = '12px';
            card.style.marginBottom = '10px';

            card.innerHTML = `
                <div style="display:flex; align-items:center; gap:10px;">
                    <b style="font-size:16px;">Option ${keys[i]}</b>

                    <label style="margin-left:auto; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="radio" name="correct_radio" value="${i}" ${i === correctIndex ? 'checked' : ''}>
                        Correct
                    </label>
                </div>

                <div style="margin-top:10px;">
                    <label>Option Text</label><br>
                    <input type="text"
                           name="options[${i}][option_text]"
                           placeholder="Type option text..."
                           style="width:100%; padding:10px;">
                </div>

                <div style="margin-top:10px;">
                    <label>Option Image (optional)</label><br>
                    <input type="file" name="options[${i}][option_image]" accept="image/*">
                </div>
            `;

            wrap.appendChild(card);
        }

        // hook radio change -> correct_index
        document.querySelectorAll('input[name="correct_radio"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                correctIndexInput.value = e.target.value;
            });
        });
    }

    // Render options immediately
    renderOptions();
})();
</script>
@endsection
