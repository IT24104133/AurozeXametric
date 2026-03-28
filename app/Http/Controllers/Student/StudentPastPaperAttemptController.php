<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\PastPaperAttempt;
use App\Models\PastPaperAttemptAnswer;
use App\Models\PastPaperOption;
use App\Models\PastPaperQuestion;
use App\Services\CoinRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentPastPaperAttemptController extends Controller
{
    /**
     * Start a new attempt on a past paper.
     */
    public function start(PastPaper $paper)
    {
        // Ensure paper is published
        if ($paper->status !== 'published') {
            abort(404);
        }

        $studentId = Auth::id();
        $mode = request('mode', 'normal');

        // Validate and normalize mode
        $validModes = ['normal', 'ultra_easy', 'ultra_medium', 'ultra_hard'];
        if (!in_array($mode, $validModes)) {
            $mode = 'normal';
        }

        // Check if there's an existing in_progress attempt
        $existingAttempt = PastPaperAttempt::where('past_paper_id', $paper->id)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        // If there's an in_progress attempt, redirect to it
        if ($existingAttempt) {
            return redirect()->route('student.past_papers.attempt.show', $existingAttempt->id);
        }

        // Get the highest attempt_no for this student and paper
        $maxAttemptNo = PastPaperAttempt::where('past_paper_id', $paper->id)
            ->where('student_id', $studentId)
            ->max('attempt_no') ?? 0;

        $questionOrder = null;
        $questionCount = 0;

        if ($paper->category === 'edu_department') {
            // Prevent starting if paper has no questions
            $questionCount = $paper->questions()->count();
            if ($questionCount <= 0) {
                return redirect()
                    ->route('student.past_papers.subject.papers', [
                        'stream' => $paper->stream,
                        'subject' => $paper->subject_id,
                        'source' => 'education',
                    ])
                    ->with('no_questions', 'Please try another paper.');
            }
        } else {
            $questions = $this->generateFreeStyleQuestions($paper, $mode);
            $questionOrder = $questions->pluck('id')->toArray();
            $questionCount = $questions->count();

            if ($questionCount <= 0) {
                return redirect()
                    ->route('student.past_papers.subject.papers', [
                        'stream' => $paper->stream,
                        'subject' => $paper->subject_id,
                        'source' => 'free',
                    ])
                    ->with('no_questions', 'Not enough bank questions.');
            }
        }

        // Create new attempt (always increment attempt_no)
        $attempt = PastPaperAttempt::create([
            'past_paper_id' => $paper->id,
            'student_id' => $studentId,
            'attempt_no' => $maxAttemptNo + 1,
            'status' => 'in_progress',
            'mode' => $mode,
            'total_questions' => $questionCount,
            'started_at' => now(),
            'question_order' => $questionOrder,
        ]);

        return redirect()->route('student.past_papers.attempt.show', $attempt->id);
    }

    /**
     * Show the attempt page with questions.
     */
    public function show(PastPaperAttempt $attempt)
    {
        // Ensure attempt belongs to auth student
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        // Block if submitted or auto-submitted - redirect to dashboard
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'This attempt is already submitted.');
        }

        // Load paper with subject
        $paper = PastPaper::query()
            ->with('subject')
            ->findOrFail($attempt->past_paper_id);

        $questions = $this->getAttemptQuestionsWithOptions($attempt);

        if ($questions->count() <= 0) {
            $source = $paper->category === 'free_style' ? 'free' : 'education';
            return redirect()
                ->route('student.past_papers.subject.papers', [
                    'stream' => $paper->stream,
                    'subject' => $paper->subject_id,
                    'source' => $source,
                ])
                ->with('no_questions', 'Please try another paper.');
        }

        // Load answers for pre-filling selections
        $attempt->load('answers');

        // Build existing answers map keyed by question_id
        $existingAnswers = $attempt->answers->pluck('selected_option_id', 'question_id')->toArray();

        // Calculate expiration and remaining time
        $startedAt = $attempt->started_at ?? now();
        $expiresAt = $startedAt->copy()->addMinutes($paper->duration_minutes);
        $remainingSeconds = max(0, now()->diffInSeconds($expiresAt, false));
        
        $server_now = now()->timestamp;
        $expires_at = $expiresAt->timestamp;

        return view('student.past_papers.attempt', [
            'attempt' => $attempt,
            'paper' => $paper,
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
            'remainingSeconds' => $remainingSeconds,
            'server_now' => $server_now,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * Submit the attempt.
     */
    public function submit(Request $request, PastPaperAttempt $attempt)
    {
        // Ensure attempt belongs to auth student
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Block if submitted or auto-submitted
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt has already been submitted.',
                'redirect' => route('student.dashboard'),
            ], 403);
        }

        // Validate answers - accept JSON payload from Alpine.js
        $validated = $request->validate([
            'answers' => 'nullable|array',
            'answers.*' => 'nullable|integer',
            'reason' => 'nullable|string',
            'auto_submit' => 'nullable|boolean',
        ]);

        // Get submitted answers (default to empty array if nothing submitted)
        $answers = $validated['answers'] ?? [];

        // Load questions for this attempt (free-style uses question_order)
        $questions = $this->getAttemptQuestionsWithOptions($attempt);

        // Build a map of question_id => [option_ids] for validation
        $questionOptionsMap = [];
        foreach ($questions as $question) {
            $questionOptionsMap[$question->id] = $question->options->pluck('id')->toArray();
        }

        // Process each question and validate answers
        $correctCount = 0;

        foreach ($questions as $question) {
            $selectedOptionId = $answers[$question->id] ?? null;
            $isCorrect = false;

            // If an option was selected, validate it belongs to this question
            if ($selectedOptionId) {
                // Security: Ensure the selected option belongs to THIS question
                if (!in_array($selectedOptionId, $questionOptionsMap[$question->id])) {
                    // Invalid option for this question - treat as not answered
                    $selectedOptionId = null;
                } else {
                    // Valid option - check if it's correct
                    $selectedOption = PastPaperOption::where('id', $selectedOptionId)
                        ->where('question_id', $question->id)
                        ->first();

                    if ($selectedOption && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $correctCount++;
                    }
                }
            }

            // Store the answer
            PastPaperAttemptAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect,
                ]
            );
        }

        // Calculate score
        $totalQuestions = $questions->count();
        $percentage = $totalQuestions > 0
            ? round(($correctCount / $totalQuestions) * 100)
            : 0;

        // Determine status based on whether it was auto-submitted
        $isAutoSubmit = $validated['auto_submit'] ?? false;
        $status = $isAutoSubmit ? 'auto_submitted' : 'submitted';

        // Update attempt with final results
        $attempt->update([
            'status' => $status,
            'total_questions' => $totalQuestions,
            'correct_count' => $correctCount,
            'score' => $correctCount,
            'percentage' => $percentage,
            'ended_at' => now(),
        ]);

        // Award coins for this attempt
        $coinRewardService = new CoinRewardService();
        $coinReward = $coinRewardService->awardCoins($attempt);

        if ($coinReward['coins_awarded'] > 0) {
            session()->flash('success', $coinReward['message']);
        }

        // Return JSON response for Alpine.js
        return response()->json([
            'ok' => true,
            'message' => 'Past paper submitted successfully.',
            'redirect' => route('student.past_papers.attempt.result', $attempt->id),
        ]);
    }

    /**
     * Show the result page after submission.
     */
    public function result(PastPaperAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.past_papers.attempt.show', $attempt->id);
        }

        $attempt->load([
            'pastPaper' => function ($q) {
                $q->with('subject');
            },
            'answers',
        ]);

        $paper = $attempt->pastPaper;
        $questions = $this->getAttemptQuestionsWithOptions($attempt);
        $answersMap = $attempt->answers->keyBy('question_id');

        return view('student.past_papers.result', [
            'attempt' => $attempt,
            'paper' => $paper,
            'questions' => $questions,
            'answersMap' => $answersMap,
        ]);
    }

    /**
     * Show review page for in-progress attempt (before submission).
     */
    public function review(PastPaperAttempt $attempt)
    {
        // Authorize
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        // Block if submitted or auto-submitted - redirect to dashboard
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'This attempt is already submitted.');
        }

        // Load paper with subject
        $attempt->load([
            'pastPaper' => function ($q) {
                $q->with('subject');
            },
            'answers',
        ]);

        $paper = $attempt->pastPaper;
        $questions = $this->getAttemptQuestionsWithOptions($attempt);

        // Build existing answers map keyed by question_id
        $existingAnswers = $attempt->answers->keyBy('question_id');

        // Calculate remaining time
        $startedAt = $attempt->started_at ?? now();
        $expiresAt = $startedAt->copy()->addMinutes($paper->duration_minutes);
        $remainingSeconds = max(0, now()->diffInSeconds($expiresAt, false));
        $server_now = now()->timestamp;
        $expires_at = $expiresAt->timestamp;

        return view('student.past_papers.review', [
            'attempt' => $attempt,
            'paper' => $paper,
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
            'remainingSeconds' => $remainingSeconds,
            'server_now' => $server_now,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * Get attempt meta (for Alpine.js AJAX requests).
     */
    public function meta(PastPaperAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $attempt->load(['pastPaper', 'answers']);

        if ($attempt->pastPaper->category === 'free_style') {
            $questionOrder = $attempt->question_order ?? [];
        } else {
            $questionOrder = $attempt->pastPaper->questions()->pluck('id')->toArray();
        }
        $answersMap = $attempt->answers->pluck('selected_option_id', 'question_id')->toArray();

        $expiresAt = $attempt->started_at->addMinutes($attempt->pastPaper->duration_minutes);

        return response()->json([
            'ok' => true,
            'question_order' => $questionOrder,
            'answers' => $answersMap,
            'attempt' => [
                'status' => $attempt->status,
                'expires_at' => $expiresAt->timestamp,
                'server_now' => now()->timestamp,
            ],
        ]);
    }

    /**
     * Get a specific question (for Alpine.js AJAX requests).
     */
    public function question(Request $request, PastPaperAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $index = (int) $request->query('i', 0);

        $questions = $this->getAttemptQuestionsWithOptions($attempt);

        if ($index < 0 || $index >= $questions->count()) {
            return response()->json(['ok' => false, 'message' => 'Invalid question index'], 400);
        }

        $question = $questions[$index];

        // Get existing answer
        $answer = PastPaperAttemptAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        $selectedOptionId = $answer ? $answer->selected_option_id : null;

        // Format images (legacy + multi-image support)
        $images = array_values(array_filter([
            $question->question_image ? Storage::url($question->question_image) : null,
            $question->question_image_1 ? Storage::url($question->question_image_1) : null,
            $question->question_image_2 ? Storage::url($question->question_image_2) : null,
            $question->question_image_3 ? Storage::url($question->question_image_3) : null,
        ]));

        // Format options
        $options = $question->options->map(function ($option) {
            return [
                'id' => $option->id,
                'key' => $option->option_key,
                'text' => $option->option_text ?? '',
                'image' => $option->option_image ? Storage::url($option->option_image) : null,
            ];
        })->toArray();

        return response()->json([
            'ok' => true,
            'index' => $index,
            'question' => [
                'id' => $question->id,
                'text' => $question->question_text ?? '',
                'images' => $images,
                'options' => $options,
            ],
            'selected' => $selectedOptionId,
        ]);
    }

    /**
     * Save an answer (for Alpine.js AJAX requests).
     */
    public function saveAnswer(Request $request, PastPaperAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Block if submitted or auto-submitted
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt has already been submitted.',
                'redirect' => route('student.dashboard'),
            ], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer',
            'selected_option_id' => 'nullable|integer',
        ]);

        $questionId = $validated['question_id'];
        $selectedOptionId = $validated['selected_option_id'];

        // Verify question belongs to this attempt
        $questionIds = $this->getAttemptQuestionIds($attempt);
        if (!in_array($questionId, $questionIds, true)) {
            return response()->json(['ok' => false, 'message' => 'Question not found'], 404);
        }

        // If clearing answer (null), just update
        if ($selectedOptionId === null) {
            PastPaperAttemptAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'selected_option_id' => null,
                    'is_correct' => false,
                ]
            );

            return response()->json(['ok' => true, 'message' => 'Answer cleared']);
        }

        // Verify option belongs to question
        $option = PastPaperOption::where('id', $selectedOptionId)
            ->where('question_id', $questionId)
            ->first();

        if (!$option) {
            return response()->json(['ok' => false, 'message' => 'Invalid option'], 400);
        }

        // Save answer (don't reveal is_correct yet - save as false for now)
        PastPaperAttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $selectedOptionId,
                'is_correct' => false, // Will be calculated on submission
            ]
        );

        return response()->json(['ok' => true, 'message' => 'Answer saved']);
    }

    /**
     * Build a fixed question order for free-style papers.
     */
    private function generateFreeStyleQuestions(PastPaper $paper, string $mode = 'normal')
    {
        // Map mode to difficulty counts
        $modeConfig = match($mode) {
            'ultra_easy' => ['e' => 20, 'm' => 15, 'h' => 5],
            'ultra_medium' => ['e' => 12, 'm' => 18, 'h' => 10],
            'ultra_hard' => ['e' => 5, 'm' => 20, 'h' => 15],
            default => ['e' => (int)($paper->count_e ?? $paper->count_s ?? 0), 'm' => (int)($paper->count_m ?? 0), 'h' => (int)($paper->count_h ?? 0)],
        };

        $countE = $modeConfig['e'];
        $countM = $modeConfig['m'];
        $countH = $modeConfig['h'];
        $totalQuestions = $countE + $countM + $countH;

        if ($totalQuestions <= 0) {
            return collect();
        }

        $pool = PastPaperQuestion::query()
            ->whereHas('pastPaper', function ($q) use ($paper) {
                $q->where('subject_id', $paper->subject_id)
                    ->where('stream', $paper->stream)
                    ->where('category', 'edu_department')
                    ->where('status', 'published');
            })
            ->get();

        if ($pool->isEmpty()) {
            return collect();
        }

        $groupE = $pool->filter(function ($q) {
            return $q->difficulty === 'E' || ($q->difficulty === null && $q->weight === 'S');
        })->values();

        $groupM = $pool->filter(function ($q) {
            return $q->difficulty === 'M' || ($q->difficulty === null && ($q->weight === 'M' || $q->weight === null));
        })->values();

        $groupH = $pool->filter(function ($q) {
            return $q->difficulty === 'H' || ($q->difficulty === null && $q->weight === 'H');
        })->values();

        $selected = collect();

        $selected = $selected->merge($this->selectByUsage($groupE, $countE));
        $selected = $selected->merge($this->selectByUsage($groupM, $countM));
        $selected = $selected->merge($this->selectByUsage($groupH, $countH));

        $selectedIds = $selected->pluck('id')->toArray();
        $remainingNeeded = $totalQuestions - count($selectedIds);

        if ($remainingNeeded > 0) {
            $remainingPool = $pool->whereNotIn('id', $selectedIds)->values();
            if ($remainingPool->count() < $remainingNeeded) {
                return collect();
            }

            $selected = $selected->merge($this->selectByUsage($remainingPool, $remainingNeeded));
        }

        $final = $selected->unique('id')->values();
        if ($final->count() < $totalQuestions) {
            return collect();
        }

        $final = $final->shuffle()->values();
        $finalIds = $final->pluck('id')->toArray();

        PastPaperQuestion::whereIn('id', $finalIds)->update([
            'times_used' => DB::raw('times_used + 1'),
            'last_used_at' => now(),
        ]);

        return $final;
    }

    /**
     * Select questions by least-used rotation with a small randomness window.
     */
    private function selectByUsage($pool, int $count)
    {
        if ($count <= 0) {
            return collect();
        }

        // Single sort callback: compare by times_used first, then by last_used_at
        $sorted = $pool->sort(function($a, $b) {
            // Get times_used as integer, default to 0
            $aUsed = isset($a->times_used) ? (int) $a->times_used : 0;
            $bUsed = isset($b->times_used) ? (int) $b->times_used : 0;
            
            // Compare by times_used first (least used first)
            if ($aUsed !== $bUsed) {
                return $aUsed <=> $bUsed;
            }

            // If times_used is equal, compare by last_used_at (oldest first, null treated as oldest)
            // Convert Carbon to timestamp for comparison
            $aTs = ($a->last_used_at instanceof \Illuminate\Support\Carbon) 
                ? $a->last_used_at->timestamp 
                : (int) $a->last_used_at;
            $bTs = ($b->last_used_at instanceof \Illuminate\Support\Carbon) 
                ? $b->last_used_at->timestamp 
                : (int) $b->last_used_at;

            // Treat null/0 as oldest (picked first)
            $aTs = $aTs ?: 0;
            $bTs = $bTs ?: 0;

            return $aTs <=> $bTs;
        })->values();

        $limit = max(($count * 3), ($count + 10));
        $candidates = $sorted->take($limit)->shuffle();

        return $candidates->take($count);
    }

    /**
     * Get questions for an attempt in the correct order with options.
     */
    private function getAttemptQuestionsWithOptions(PastPaperAttempt $attempt)
    {
        $attempt->loadMissing('pastPaper');

        if ($attempt->pastPaper->category === 'free_style') {
            $order = $attempt->question_order ?? [];
            if (empty($order)) {
                return collect();
            }

            $questions = PastPaperQuestion::query()
                ->with(['options' => function ($q) {
                    $q->orderBy('position');
                }])
                ->whereIn('id', $order)
                ->get()
                ->keyBy('id');

            return collect($order)
                ->map(fn ($id) => $questions->get($id))
                ->filter()
                ->values();
        }

        return $attempt->pastPaper->questions()
            ->with(['options' => function ($q) {
                $q->orderBy('position');
            }])
            ->get();
    }

    /**
     * Get question IDs for an attempt.
     */
    private function getAttemptQuestionIds(PastPaperAttempt $attempt): array
    {
        $attempt->loadMissing('pastPaper');

        if ($attempt->pastPaper->category === 'free_style') {
            return $attempt->question_order ?? [];
        }

        return $attempt->pastPaper->questions()->pluck('id')->toArray();
    }
}
