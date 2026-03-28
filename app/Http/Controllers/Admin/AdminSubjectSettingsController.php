<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PastPaperSubject;
use Illuminate\Http\Request;

class AdminSubjectSettingsController extends Controller
{
    /**
     * Show Free Style settings form for a subject
     */
    public function show($stream, $subject)
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

        return view('admin.past_papers.subjects.settings', [
            'stream' => $stream,
            'subject' => $subjectModel,
        ]);
    }

    /**
     * Update Free Style settings for a subject
     */
    public function update($stream, $subject, Request $request)
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

        // Validate input
        $validated = $request->validate([
            'fs_total_questions' => 'required|integer|min:10|max:100',
            'fs_count_e' => 'required|integer|min:0',
            'fs_count_m' => 'required|integer|min:0',
            'fs_count_h' => 'required|integer|min:0',
            'fs_ultra_easy_e' => 'required|integer|min:0',
            'fs_ultra_easy_m' => 'required|integer|min:0',
            'fs_ultra_easy_h' => 'required|integer|min:0',
            'fs_ultra_medium_e' => 'required|integer|min:0',
            'fs_ultra_medium_m' => 'required|integer|min:0',
            'fs_ultra_medium_h' => 'required|integer|min:0',
            'fs_ultra_hard_e' => 'required|integer|min:0',
            'fs_ultra_hard_m' => 'required|integer|min:0',
            'fs_ultra_hard_h' => 'required|integer|min:0',
            'fs_timer_minutes' => 'required|integer|min:5|max:300',
        ]);

        // Validate distribution totals match fs_total_questions
        $normalTotal = $validated['fs_count_e'] + $validated['fs_count_m'] + $validated['fs_count_h'];
        $ultraEasyTotal = $validated['fs_ultra_easy_e'] + $validated['fs_ultra_easy_m'] + $validated['fs_ultra_easy_h'];
        $ultraMediumTotal = $validated['fs_ultra_medium_e'] + $validated['fs_ultra_medium_m'] + $validated['fs_ultra_medium_h'];
        $ultraHardTotal = $validated['fs_ultra_hard_e'] + $validated['fs_ultra_hard_m'] + $validated['fs_ultra_hard_h'];

        if ($normalTotal !== (int)$validated['fs_total_questions']) {
            return back()->withErrors(['normal' => "Normal mode totals ({$normalTotal}) must equal Total Questions ({$validated['fs_total_questions']})"]);
        }

        if ($ultraEasyTotal !== (int)$validated['fs_total_questions']) {
            return back()->withErrors(['ultra_easy' => "Ultra Easy mode totals ({$ultraEasyTotal}) must equal Total Questions ({$validated['fs_total_questions']})"]);
        }

        if ($ultraMediumTotal !== (int)$validated['fs_total_questions']) {
            return back()->withErrors(['ultra_medium' => "Ultra Medium mode totals ({$ultraMediumTotal}) must equal Total Questions ({$validated['fs_total_questions']})"]);
        }

        if ($ultraHardTotal !== (int)$validated['fs_total_questions']) {
            return back()->withErrors(['ultra_hard' => "Ultra Hard mode totals ({$ultraHardTotal}) must equal Total Questions ({$validated['fs_total_questions']})"]);
        }

        // Update subject
        $subjectModel->update($validated);

        session()->flash('success', "Free Style settings for '{$subjectModel->name}' updated successfully!");

        return redirect()->route('admin.past_papers.subjects.settings', ['stream' => $stream, 'subject' => $subject]);
    }
}
