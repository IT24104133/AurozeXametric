<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExamQuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $questions = Question::where('exam_id', $exam->id)
            ->orderBy('order_index')
            ->get();

        $count = $questions->count();

        return view('admin.exams.questions.index', compact('exam', 'questions', 'count'));
    }

    public function create(Exam $exam)
    {
        $count = Question::where('exam_id', $exam->id)->count();
        $nextNumber = $count + 1;
        $optionCount = $exam->option_count ?? 4; // Get exam's option count (default 4)

        return view('admin.exams.questions.create', compact('exam', 'count', 'nextNumber', 'optionCount'));
    }

    public function store(Request $request, Exam $exam)
    {
        // Get exam's option count (default 4 for backward compatibility)
        $optionCount = $exam->option_count ?? 4;
        $maxOptionIndex = $optionCount - 1;

        $validated = $request->validate([
            'question_text' => ['required', 'string'],

            // images
            'image_1' => ['nullable', 'image', 'max:2048'],
            'image_2' => ['nullable', 'image', 'max:2048'],
            'image_3' => ['nullable', 'image', 'max:2048'],

            // ✅ NEW: Dynamic option count based on exam setting (3, 4, or 5)
            'options' => ['required', 'array', "size:$optionCount"],
            'options.*.option_text' => ['nullable', 'string'],
            'options.*.option_image' => ['nullable', 'image', 'max:2048'],

            // ✅ which option is correct (0..optionCount-1)
            'correct_index' => ['required', 'integer', 'min:0', "max:$maxOptionIndex"],
        ], [
            'options.size' => "You must provide exactly $optionCount options for this exam.",
            'correct_index.max' => "Correct option index cannot exceed " . ($optionCount - 1) . ".",
        ]);

        $orderIndex = Question::where('exam_id', $exam->id)->count() + 1;
        $keys = ['A', 'B', 'C', 'D', 'E'];

        return DB::transaction(function () use ($request, $exam, $validated, $orderIndex, $keys) {

            $question = new Question();
            $question->exam_id = $exam->id;
            $question->order_index = $orderIndex;
            $question->question_text = $validated['question_text'];

            // store question images
            foreach (['image_1', 'image_2', 'image_3'] as $imgKey) {
                if ($request->hasFile($imgKey)) {
                    $question->$imgKey = $request->file($imgKey)->store('questions', 'public');
                } else {
                    $question->$imgKey = null;
                }
            }

            // ✅ Only touch is_included if the column actually exists in DB
            if (Schema::hasColumn('questions', 'is_included')) {
                // default true if not provided
                $question->is_included = true;
            }

            $question->save();

            // Save options (4 or 5)
            foreach ($validated['options'] as $index => $opt) {
                $option = new QuestionOption();
                $option->question_id = $question->id;

                // if your table has these columns, keep them:
                if (Schema::hasColumn('question_options', 'position')) {
                    $option->position = $index + 1; // 1..5
                }

                if (Schema::hasColumn('question_options', 'option_key')) {
                    $option->option_key = $keys[$index];
                }

                $option->option_text = $opt['option_text'] ?? null;
                $option->is_correct = ($index === (int) $validated['correct_index']);

                if ($request->hasFile("options.$index.option_image")) {
                    $option->option_image = $request->file("options.$index.option_image")->store('options', 'public');
                }

                $option->save();
            }

            return redirect()
                ->route('admin.exams.questions.create', $exam)
                ->with('success', 'Question added. Add next one.');
        });
    }

    public function edit(Exam $exam, Question $question)
    {
        abort_if($question->exam_id !== $exam->id, 404);

        $question->load(['options' => function ($q) {
            if (Schema::hasColumn('question_options', 'position')) {
                $q->orderBy('position');
            }
        }]);

        return view('admin.exams.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        abort_if($question->exam_id !== $exam->id, 404);

        $validated = $request->validate([
            'question_text' => ['required', 'string'],

            'image_1' => ['nullable', 'image', 'max:2048'],
            'image_2' => ['nullable', 'image', 'max:2048'],
            'image_3' => ['nullable', 'image', 'max:2048'],

            'options' => ['required', 'array', 'min:4', 'max:5'],
            'options.*.id' => ['nullable', 'integer'],
            'options.*.option_text' => ['nullable', 'string'],
            'options.*.option_image' => ['nullable', 'image', 'max:2048'],

            'correct_index' => ['required', 'integer', 'min:0', 'max:4'],
        ]);

        $keys = ['A', 'B', 'C', 'D', 'E'];

        return DB::transaction(function () use ($request, $exam, $question, $validated, $keys) {

            $question->question_text = $validated['question_text'];

            // replace question images if uploaded
            foreach (['image_1', 'image_2', 'image_3'] as $imgKey) {
                if ($request->hasFile($imgKey)) {
                    if (!empty($question->$imgKey)) {
                        Storage::disk('public')->delete($question->$imgKey);
                    }
                    $question->$imgKey = $request->file($imgKey)->store('questions', 'public');
                }
            }

            $question->save();

            // delete old options and recreate (simple & safe)
            $question->options()->delete();

            foreach ($validated['options'] as $index => $opt) {
                $option = new QuestionOption();
                $option->question_id = $question->id;

                if (Schema::hasColumn('question_options', 'position')) {
                    $option->position = $index + 1;
                }

                if (Schema::hasColumn('question_options', 'option_key')) {
                    $option->option_key = $keys[$index];
                }

                $option->option_text = $opt['option_text'] ?? null;
                $option->is_correct = ($index === (int) $validated['correct_index']);

                if ($request->hasFile("options.$index.option_image")) {
                    $option->option_image = $request->file("options.$index.option_image")->store('options', 'public');
                }

                $option->save();
            }

            return redirect()
                ->route('admin.exams.questions.index', $exam)
                ->with('success', 'Question updated.');
        });
    }

    public function destroy(Exam $exam, Question $question)
    {
        abort_if($question->exam_id !== $exam->id, 404);

        foreach (['image_1', 'image_2', 'image_3'] as $imgKey) {
            if (!empty($question->$imgKey)) {
                Storage::disk('public')->delete($question->$imgKey);
            }
        }

        // delete option images too (if you store them)
        foreach ($question->options as $opt) {
            if (!empty($opt->option_image)) {
                Storage::disk('public')->delete($opt->option_image);
            }
        }

        $question->delete();

        // re-number order_index
        $remaining = Question::where('exam_id', $exam->id)->orderBy('order_index')->get();
        foreach ($remaining as $i => $q) {
            $q->update(['order_index' => $i + 1]);
        }

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question deleted.');
    }

    public function toggleInclude(Exam $exam, Question $question)
    {
        abort_unless($question->exam_id === $exam->id, 404);

        // ✅ prevent crash if column not migrated
        if (!Schema::hasColumn('questions', 'is_included')) {
            return response()->json([
                'ok' => false,
                'message' => 'is_included column not found. Run migrations.',
            ], 422);
        }

        $question->is_included = !$question->is_included;
        $question->save();

        return response()->json([
            'ok' => true,
            'is_included' => (bool) $question->is_included,
            'message' => $question->is_included ? 'Included' : 'Excluded',
        ]);
    }
}
