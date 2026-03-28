<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperQuestion;
use App\Models\PastPaperOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPastPaperQuestionController extends Controller
{
    public function index($stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        $questions = $paper->questions()
            ->with('options')
            ->orderBy('id')
            ->get();

        return view('admin.past_papers.questions.index', compact('stream', 'paper', 'questions'));
    }

    public function create($stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        return view('admin.past_papers.questions.create', compact('stream', 'paper'));
    }

    public function store(Request $request, $stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        $data = $request->validate([
            'question_text' => 'nullable|string',
            'question_image_1' => 'nullable|image|max:2048',
            'question_image_2' => 'nullable|image|max:2048',
            'question_image_3' => 'nullable|image|max:2048',
            'difficulty' => 'required|in:E,M,H',
            'options' => 'required|array|size:4',
            'options.*.text' => 'nullable|string|max:500',
            'options.*.image' => 'nullable|image|max:2048',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $hasQuestionText = trim($data['question_text'] ?? '') !== '';
        $hasQuestionImage = $request->hasFile('question_image_1')
            || $request->hasFile('question_image_2')
            || $request->hasFile('question_image_3');

        if (!$hasQuestionText && !$hasQuestionImage) {
            return back()
                ->withErrors(['question_text' => 'Provide question text or at least one image.'])
                ->withInput();
        }

        $keys = ['A', 'B', 'C', 'D'];
        $optionErrors = [];

        foreach ($keys as $key) {
            $optionText = trim($data['options'][$key]['text'] ?? '');
            $hasOptionImage = $request->hasFile("options.{$key}.image");

            if ($optionText === '' && !$hasOptionImage) {
                $optionErrors["options.{$key}.text"] = "Option {$key} requires text or an image.";
            }
        }

        if (!empty($optionErrors)) {
            return back()->withErrors($optionErrors)->withInput();
        }

        $questionData = [
            'question_text' => $hasQuestionText ? $data['question_text'] : '',
            'difficulty' => $data['difficulty'],
            'weight' => $data['difficulty'] === 'E' ? 'S' : $data['difficulty'],
        ];

        if ($request->hasFile('question_image_1')) {
            $path = $request->file('question_image_1')->store('past_papers/questions', 'public');
            $questionData['question_image_1'] = $path;
            $questionData['question_image'] = $path;
        }

        if ($request->hasFile('question_image_2')) {
            $questionData['question_image_2'] = $request->file('question_image_2')
                ->store('past_papers/questions', 'public');
        }

        if ($request->hasFile('question_image_3')) {
            $questionData['question_image_3'] = $request->file('question_image_3')
                ->store('past_papers/questions', 'public');
        }

        $question = $paper->questions()->create($questionData);

        // Create options
        foreach ($keys as $index => $key) {
            $optionText = trim($data['options'][$key]['text'] ?? '');
            $hasOptionImage = $request->hasFile("options.{$key}.image");

            $optionData = [
                'question_id' => $question->id,
                'option_key' => $key,
                'option_text' => $optionText !== '' ? $optionText : null,
                'is_correct' => $key === $data['correct_option'],
                'position' => $index + 1,
            ];

            if ($hasOptionImage) {
                $optionData['option_image'] = $request->file("options.{$key}.image")
                    ->store('past_papers/options', 'public');
            }

            PastPaperOption::create($optionData);
        }

        return redirect()->route('admin.past_papers.questions.index', [$stream, $paper->id])
            ->with('success', 'Question created successfully.');
    }

    public function edit($stream, PastPaperQuestion $question)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $question->load('options', 'pastPaper.subject');
        $paper = $question->pastPaper;

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        return view('admin.past_papers.questions.edit', compact('stream', 'question', 'paper'));
    }

    public function update(Request $request, $stream, PastPaperQuestion $question)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $paper = $question->pastPaper;

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        $data = $request->validate([
            'question_text' => 'nullable|string',
            'question_image_1' => 'nullable|image|max:2048',
            'question_image_2' => 'nullable|image|max:2048',
            'question_image_3' => 'nullable|image|max:2048',
            'difficulty' => 'required|in:E,M,H',
            'options' => 'required|array|size:4',
            'options.*.text' => 'nullable|string|max:500',
            'options.*.image' => 'nullable|image|max:2048',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $hasQuestionText = trim($data['question_text'] ?? '') !== '';
        $hasQuestionImage = $request->hasFile('question_image_1')
            || $request->hasFile('question_image_2')
            || $request->hasFile('question_image_3')
            || $question->question_image
            || $question->question_image_1
            || $question->question_image_2
            || $question->question_image_3;

        if (!$hasQuestionText && !$hasQuestionImage) {
            return back()
                ->withErrors(['question_text' => 'Provide question text or at least one image.'])
                ->withInput();
        }

        $keys = ['A', 'B', 'C', 'D'];
        $options = $question->options()->get()->keyBy('option_key');
        $optionErrors = [];

        foreach ($keys as $key) {
            $optionText = trim($data['options'][$key]['text'] ?? '');
            $hasOptionImage = $request->hasFile("options.{$key}.image")
                || !empty($options[$key]?->option_image);

            if ($optionText === '' && !$hasOptionImage) {
                $optionErrors["options.{$key}.text"] = "Option {$key} requires text or an image.";
            }
        }

        if (!empty($optionErrors)) {
            return back()->withErrors($optionErrors)->withInput();
        }

        $questionData = [
            'question_text' => $hasQuestionText ? $data['question_text'] : '',
            'difficulty' => $data['difficulty'],
            'weight' => $data['difficulty'] === 'E' ? 'S' : $data['difficulty'],
        ];

        if ($request->hasFile('question_image_1')) {
            if ($question->question_image_1 && Storage::disk('public')->exists($question->question_image_1)) {
                Storage::disk('public')->delete($question->question_image_1);
            }
            if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
                Storage::disk('public')->delete($question->question_image);
            }
            $path = $request->file('question_image_1')->store('past_papers/questions', 'public');
            $questionData['question_image_1'] = $path;
            $questionData['question_image'] = $path;
        }

        if ($request->hasFile('question_image_2')) {
            if ($question->question_image_2 && Storage::disk('public')->exists($question->question_image_2)) {
                Storage::disk('public')->delete($question->question_image_2);
            }
            $questionData['question_image_2'] = $request->file('question_image_2')
                ->store('past_papers/questions', 'public');
        }

        if ($request->hasFile('question_image_3')) {
            if ($question->question_image_3 && Storage::disk('public')->exists($question->question_image_3)) {
                Storage::disk('public')->delete($question->question_image_3);
            }
            $questionData['question_image_3'] = $request->file('question_image_3')
                ->store('past_papers/questions', 'public');
        }

        $question->update($questionData);

        // Update options
        foreach ($keys as $index => $key) {
            $option = $options[$key];
            $optionText = trim($data['options'][$key]['text'] ?? '');

            $optionData = [
                'option_text' => $optionText !== '' ? $optionText : null,
                'is_correct' => $key === $data['correct_option'],
                'position' => $index + 1,
            ];

            if ($request->hasFile("options.{$key}.image")) {
                if ($option->option_image && Storage::disk('public')->exists($option->option_image)) {
                    Storage::disk('public')->delete($option->option_image);
                }
                $optionData['option_image'] = $request->file("options.{$key}.image")
                    ->store('past_papers/options', 'public');
            }

            $option->update($optionData);
        }

        return redirect()->route('admin.past_papers.questions.index', [$stream, $paper->id])
            ->with('success', 'Question updated successfully.');
    }

    public function destroy($stream, PastPaperQuestion $question)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $paper = $question->pastPaper;

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        if ($paper->category === 'free_style') {
            return redirect()
                ->route('admin.past_papers.papers.index', ['stream' => $stream, 'subject' => $paper->subject_id])
                ->with('error', 'Free Style papers do not have manual questions. They use the question bank automatically.');
        }

        // Delete question images if exist
        $questionImages = [
            $question->question_image,
            $question->question_image_1,
            $question->question_image_2,
            $question->question_image_3,
        ];

        foreach (array_filter($questionImages) as $image) {
            if (Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete option images if exist
        foreach ($question->options as $option) {
            if ($option->option_image && Storage::disk('public')->exists($option->option_image)) {
                Storage::disk('public')->delete($option->option_image);
            }
        }

        // Delete question (cascade will handle options)
        $question->delete();

        return redirect()->route('admin.past_papers.questions.index', [$stream, $paper->id])
            ->with('success', 'Question deleted successfully.');
    }
}
