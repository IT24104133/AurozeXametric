<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        // Load options also (so you can show them if needed)
        $questions = $exam->questions()->with('options')->orderBy('position')->get();

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam)
    {
        $nextPos = ($exam->questions()->max('position') ?? 0) + 1;
        return view('admin.questions.create', compact('exam', 'nextPos'));
    }

    public function store(Request $request, Exam $exam)
    {
        // ✅ Validate: 4 or 5 options + exactly 1 correct + images
        $validated = $request->validate([
            'position' => ['required', 'integer', 'min:1'],
            'question_text' => ['required', 'string'],

            // question images (optional)
            'image_1' => ['nullable', 'image', 'max:4096'],
            'image_2' => ['nullable', 'image', 'max:4096'],
            'image_3' => ['nullable', 'image', 'max:4096'],

            // options array
            'options' => ['required', 'array', 'min:4', 'max:5'],
            'options.*.option_text' => ['nullable', 'string', 'max:255'],
            'options.*.option_image' => ['nullable', 'image', 'max:4096'],

            // index of correct option (0..4)
            'correct_index' => ['required', 'integer', 'min:0', 'max:4'],

            'explanation' => ['nullable', 'string'],
        ]);

        // Extra rule: each option must have text OR image (at least one)
        foreach ($validated['options'] as $i => $opt) {
            $hasText = isset($opt['option_text']) && trim($opt['option_text']) !== '';
            $hasImage = $request->hasFile("options.$i.option_image");
            if (!$hasText && !$hasImage) {
                return back()
                    ->withErrors(["options.$i.option_text" => "Option ".($i+1)." must have text or an image."])
                    ->withInput();
            }
        }

        // correct_index must be within option count
        $optionCount = count($validated['options']);
        if ((int)$validated['correct_index'] > ($optionCount - 1)) {
            return back()
                ->withErrors(['correct_index' => 'Correct option is invalid for selected option count.'])
                ->withInput();
        }

        $keys = ['A', 'B', 'C', 'D', 'E'];

        DB::transaction(function () use ($request, $exam, $validated, $keys) {
            // 1) Create Question
            $question = new Question();
            $question->exam_id = $exam->id;
            $question->position = $validated['position'];
            $question->question_text = $validated['question_text'];
            $question->explanation = $validated['explanation'] ?? null;

            // Save question images
            if ($request->hasFile('image_1')) {
                $question->image_1 = $request->file('image_1')->store('questions', 'public');
            }
            if ($request->hasFile('image_2')) {
                $question->image_2 = $request->file('image_2')->store('questions', 'public');
            }
            if ($request->hasFile('image_3')) {
                $question->image_3 = $request->file('image_3')->store('questions', 'public');
            }

            $question->save();

            // 2) Create options (4 or 5)
            foreach ($validated['options'] as $index => $opt) {
                $option = new QuestionOption();
                $option->question_id = $question->id;
                $option->position = $index + 1;        // 1..5
                $option->option_key = $keys[$index];   // A..E
                $option->option_text = $opt['option_text'] ?? null;
                $option->is_correct = ($index === (int)$validated['correct_index']);

                if ($request->hasFile("options.$index.option_image")) {
                    $option->option_image = $request->file("options.$index.option_image")->store('options', 'public');
                }

                $option->save();
            }
        });

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question added!');
    }

    public function edit(Exam $exam, Question $question)
    {
        abort_unless($question->exam_id === $exam->id, 404);

        $question->load('options');

        return view('admin.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        abort_unless($question->exam_id === $exam->id, 404);

        $validated = $request->validate([
            'position' => ['required', 'integer', 'min:1'],
            'question_text' => ['required', 'string'],

            'image_1' => ['nullable', 'image', 'max:4096'],
            'image_2' => ['nullable', 'image', 'max:4096'],
            'image_3' => ['nullable', 'image', 'max:4096'],

            'options' => ['required', 'array', 'min:4', 'max:5'],
            'options.*.id' => ['nullable', 'integer'], // existing option id (if editing)
            'options.*.option_text' => ['nullable', 'string', 'max:255'],
            'options.*.option_image' => ['nullable', 'image', 'max:4096'],

            'correct_index' => ['required', 'integer', 'min:0', 'max:4'],
            'explanation' => ['nullable', 'string'],
        ]);

        foreach ($validated['options'] as $i => $opt) {
            $hasText = isset($opt['option_text']) && trim($opt['option_text']) !== '';
            $hasImage = $request->hasFile("options.$i.option_image");
            if (!$hasText && !$hasImage) {
                return back()
                    ->withErrors(["options.$i.option_text" => "Option ".($i+1)." must have text or an image."])
                    ->withInput();
            }
        }

        $optionCount = count($validated['options']);
        if ((int)$validated['correct_index'] > ($optionCount - 1)) {
            return back()
                ->withErrors(['correct_index' => 'Correct option is invalid for selected option count.'])
                ->withInput();
        }

        $keys = ['A', 'B', 'C', 'D', 'E'];

        DB::transaction(function () use ($request, $question, $validated, $keys) {

            // Update question main data
            $question->position = $validated['position'];
            $question->question_text = $validated['question_text'];
            $question->explanation = $validated['explanation'] ?? null;

            // Update images if new uploaded
            if ($request->hasFile('image_1')) {
                $question->image_1 = $request->file('image_1')->store('questions', 'public');
            }
            if ($request->hasFile('image_2')) {
                $question->image_2 = $request->file('image_2')->store('questions', 'public');
            }
            if ($request->hasFile('image_3')) {
                $question->image_3 = $request->file('image_3')->store('questions', 'public');
            }

            $question->save();

            // Rebuild options safely: easiest + cleanest
            // delete old options then insert new ones
            $question->options()->delete();

            foreach ($validated['options'] as $index => $opt) {
                $option = new QuestionOption();
                $option->question_id = $question->id;
                $option->position = $index + 1;
                $option->option_key = $keys[$index];
                $option->option_text = $opt['option_text'] ?? null;
                $option->is_correct = ($index === (int)$validated['correct_index']);

                if ($request->hasFile("options.$index.option_image")) {
                    $option->option_image = $request->file("options.$index.option_image")->store('options', 'public');
                }

                $option->save();
            }
        });

        return back()->with('success', 'Question updated!');
    }

    public function destroy(Exam $exam, Question $question)
    {
        abort_unless($question->exam_id === $exam->id, 404);

        DB::transaction(function () use ($question) {
            $question->options()->delete();
            $question->delete();
        });

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question deleted!');
    }
}
