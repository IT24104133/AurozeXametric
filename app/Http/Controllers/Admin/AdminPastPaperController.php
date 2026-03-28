<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPastPaperController extends Controller
{
    /**
     * Show home page with 3 stream cards
     */
    public function home()
    {
        $streams = getStreams();
        $streamData = [];

        foreach ($streams as $stream) {
            $subjectsCount = PastPaperSubject::where('stream', $stream)->count();
            $papersCount = PastPaper::where('stream', $stream)->count();

            $streamData[$stream] = [
                'subjects' => $subjectsCount,
                'papers' => $papersCount,
            ];
        }

        return view('admin.past_papers.home', compact('streamData'));
    }

    /**
     * List papers for a stream and subject
     */
    public function index($stream, $subject)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Validate subject belongs to stream
        $subjectModel = PastPaperSubject::findOrFail($subject);
        if ($subjectModel->stream !== $stream) {
            abort(404);
        }

        $papers = PastPaper::where('stream', $stream)
            ->where('subject_id', $subject)
            ->where('category', '!=', 'free_style')  // Hide free_style papers
            ->with('subject')
            ->withCount('questions')
            ->orderBy('category')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        $subjects = PastPaperSubject::where('stream', $stream)->orderBy('name')->get();
        $selectedSubject = $subject;

        return view('admin.past_papers.papers.index', compact('stream', 'papers', 'subjects', 'selectedSubject'));
    }

    /**
     * Show create paper form
     */
    public function create($stream, Request $request)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $subjects = PastPaperSubject::where('stream', $stream)->orderBy('name')->get();
        $selectedSubject = null;

        if ($request->filled('subject_id')) {
            $selectedSubject = PastPaperSubject::where('stream', $stream)
                ->where('id', $request->subject_id)
                ->first();
        }

        return view('admin.past_papers.papers.create', compact('stream', 'subjects', 'selectedSubject'));
    }

    /**
     * Store a new paper
     */
    public function store(Request $request, $stream)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $subject = PastPaperSubject::where('stream', $stream)->findOrFail($request->subject_id);

        $rules = [
            'subject_id' => 'required|exists:past_paper_subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ];

        $rules['category'] = 'required|in:edu_department,free_style';

        // Add category-specific validation
        if ($request->category === 'edu_department') {
            $rules['year'] = 'required|integer|min:1950|max:' . date('Y');
        } else {
            $rules['total_questions'] = 'required|integer|min:1';
            $rules['count_e'] = 'required|integer|min:0';
            $rules['count_m'] = 'required|integer|min:0';
            $rules['count_h'] = 'required|integer|min:0';
        }

        $data = $request->validate($rules);

        if ($data['category'] === 'free_style') {
            $sum = ($data['count_e'] ?? 0) + ($data['count_m'] ?? 0) + ($data['count_h'] ?? 0);
            if ($sum !== (int) $data['total_questions']) {
                return back()
                    ->withErrors(['total_questions' => 'Total questions must equal the sum of E, M, and H counts.'])
                    ->withInput();
            }
        }

        // Auto-generate title for edu_department
        if ($data['category'] === 'edu_department') {
            $data['title'] = $subject->name . ' ' . $data['year'];
            // Calculate count_e as the remainder
            $count_s = (int) ($data['count_s'] ?? 0);
            $count_m = (int) ($data['count_m'] ?? 0);
            $count_h = (int) ($data['count_h'] ?? 0);
            $data['count_e'] = $count_s + $count_m + $count_h;
            $data['total_questions'] = null;
        } else {
            $data['title'] = $subject->name . ' - Free Style';
            $data['year'] = null;
        }

        $data['stream'] = $stream;

        PastPaper::create($data);

        return redirect()->route('admin.past_papers.papers.index', [$stream, $subject->id])
            ->with('success', 'Paper created successfully.');
    }

    /**
     * Show edit paper form
     */
    public function edit($stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        $subjects = PastPaperSubject::where('stream', $stream)->orderBy('name')->get();
        return view('admin.past_papers.papers.edit', compact('stream', 'paper', 'subjects'));
    }

    /**
     * Update a paper
     */
    public function update(Request $request, $stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        $subject = PastPaperSubject::where('stream', $stream)->findOrFail($request->subject_id);

        $rules = [
            'subject_id' => 'required|exists:past_paper_subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ];

        $rules['category'] = 'required|in:edu_department,free_style';

        if ($request->category === 'edu_department') {
            $rules['year'] = 'required|integer|min:1950|max:' . date('Y');
        } else {
            $rules['total_questions'] = 'required|integer|min:1';
            $rules['count_s'] = 'required|integer|min:0';
            $rules['count_m'] = 'required|integer|min:0';
            $rules['count_h'] = 'required|integer|min:0';
        }

        $data = $request->validate($rules);

        if ($data['category'] === 'free_style') {
            $sum = ($data['count_s'] ?? 0) + ($data['count_m'] ?? 0) + ($data['count_h'] ?? 0);
            if ($sum !== (int) $data['total_questions']) {
                return back()
                    ->withErrors(['total_questions' => 'Total questions must equal the sum of S, M, and H counts.'])
                    ->withInput();
            }
        }

        if ($data['category'] === 'edu_department') {
            $data['title'] = $subject->name . ' ' . $data['year'];
            $data['total_questions'] = null;
            $data['count_s'] = null;
            $data['count_m'] = null;
            $data['count_h'] = null;
        } else {
            $data['title'] = $subject->name . ' - Free Style';
            $data['year'] = null;
        }

        $paper->update($data);

        return redirect()->route('admin.past_papers.papers.index', [$stream, $subject->id])
            ->with('success', 'Paper updated successfully.');
    }

    /**
     * Delete a paper
     */
    public function destroy($stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        $subject = $paper->subject;
        $isEduDepartment = $paper->category === 'edu_department';
        $questionCount = $paper->questions()->count();

        // For edu_department papers, delete stored images before cascade delete
        if ($isEduDepartment) {
            foreach ($paper->questions()->with('options')->get() as $question) {
                // Delete question image if exists
                if ($question->question_image && \Storage::disk('public')->exists($question->question_image)) {
                    \Storage::disk('public')->delete($question->question_image);
                }

                // Delete option images if exist
                foreach ($question->options as $option) {
                    if ($option->option_image && \Storage::disk('public')->exists($option->option_image)) {
                        \Storage::disk('public')->delete($option->option_image);
                    }
                }
            }
        }

        // Delete paper (cascade will handle questions and options)
        $paper->delete();

        if ($isEduDepartment) {
            $message = "Education department paper deleted successfully. Cascade removed {$questionCount} question(s) and all associated options/images.";
        } else {
            $message = 'Free Style paper configuration deleted successfully. The subject and its question bank remain unchanged.';
        }

        return redirect()->route('admin.past_papers.papers.index', [$stream, $subject->id])
            ->with('success', $message);
    }

    /**
     * Toggle publish status
     */
    public function togglePublish($stream, PastPaper $paper)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure paper belongs to stream
        if ($paper->stream !== $stream) {
            abort(404);
        }

        // If trying to publish, validate requirements
        if ($paper->status === 'draft') {
            if ($paper->category === 'edu_department') {
                // Check if paper has questions
                $questionsCount = $paper->questions()->count();
                if ($questionsCount === 0) {
                    return back()->with('error', 'Paper must have at least one question to publish.');
                }
                // Validate each question has 4 options and exactly one correct
                $questions = $paper->questions()->with('options')->get();
                foreach ($questions as $question) {
                    $optionsCount = $question->options->count();
                    $correctCount = $question->options->where('is_correct', true)->count();

                    if ($optionsCount !== 4) {
                        return back()->with('error', "Question '{$question->question_text}' must have exactly 4 options.");
                    }

                    if ($correctCount !== 1) {
                        return back()->with('error', "Question '{$question->question_text}' must have exactly one correct option.");
                    }
                }
            }
        }

        $paper->status = $paper->status === 'published' ? 'draft' : 'published';
        $paper->save();

        $message = $paper->status === 'published' ? 'Paper published successfully.' : 'Paper unpublished successfully.';
        return back()->with('success', $message);
    }
}
