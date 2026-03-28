<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPastPaperSubjectController extends Controller
{
    public function index($stream)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $subjects = PastPaperSubject::where('stream', $stream)
            ->withCount('pastPapers')
            ->orderBy('name')
            ->get();

        return view('admin.past_papers.subjects.index', compact('stream', 'subjects'));
    }

    public function create($stream)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        return view('admin.past_papers.subjects.create', compact('stream'));
    }

    public function store(Request $request, $stream)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('past_paper_subjects', 'name')->where('stream', $stream),
            ],
        ]);

        $data['stream'] = $stream;
        $subject = PastPaperSubject::create($data);

        $hasFreeStyle = PastPaper::where('subject_id', $subject->id)
            ->where('stream', $stream)
            ->where('category', 'free_style')
            ->exists();

        if (!$hasFreeStyle) {
            PastPaper::create([
                'subject_id' => $subject->id,
                'category' => 'free_style',
                'title' => $subject->name . ' - Free Style',
                'description' => null,
                'duration_minutes' => 60,
                'status' => 'published',
                'stream' => $stream,
                'total_questions' => 40,
                'count_e' => 12,
                'count_s' => 12,
                'count_m' => 18,
                'count_h' => 10,
            ]);
        }

        $message = $hasFreeStyle
            ? 'Subject created successfully. Default Free Style paper already exists.'
            : 'Subject created successfully. Default Free Style paper created.';

        return redirect()->route('admin.past_papers.subjects.index', $stream)
            ->with('success', $message);
    }

    public function edit($stream, PastPaperSubject $subject)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure subject belongs to stream
        if ($subject->stream !== $stream) {
            abort(404);
        }

        return view('admin.past_papers.subjects.edit', compact('stream', 'subject'));
    }

    public function update(Request $request, $stream, PastPaperSubject $subject)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure subject belongs to stream
        if ($subject->stream !== $stream) {
            abort(404);
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('past_paper_subjects', 'name')
                    ->where('stream', $stream)
                    ->ignore($subject->id),
            ],
        ]);

        $subject->update($data);

        return redirect()->route('admin.past_papers.subjects.index', $stream)
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy($stream, PastPaperSubject $subject)
    {
        // Validate stream
        if (!in_array($stream, getStreams())) {
            abort(404);
        }

        // Ensure subject belongs to stream
        if ($subject->stream !== $stream) {
            abort(404);
        }

        // Check if subject has any edu_department papers
        $eduPapersCount = $subject->pastPapers()
            ->where('category', 'edu_department')
            ->count();

        if ($eduPapersCount > 0) {
            return back()->with('error', 
                "Cannot delete subject with {$eduPapersCount} education department paper(s). " .
                "Delete all education department papers from this subject first."
            );
        }

        // At this point, only free_style paper may exist (auto-created)
        // It's safe to delete
        $subject->delete();

        return redirect()->route('admin.past_papers.subjects.index', $stream)
            ->with('success', 'Subject deleted successfully. Associated Free Style configuration was also removed.');
    }
}
