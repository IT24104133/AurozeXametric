<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;

class StudentExamController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Exam::where('status', 'published');
        
        // Filter by exam_uid if provided
        if ($request->filled('code')) {
            $query->where('exam_uid', 'like', '%' . $request->code . '%');
        }

        $exams = $query
            ->with(['attempts' => function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->select('id', 'exam_id', 'user_id', 'status', 'submitted_at');
            }])
            ->orderByDesc('id')
            ->get()
            ->map(function ($exam) {
                $attempt = $exam->attempts->first();
                $exam->attempted = $attempt !== null;
                $exam->completed = $attempt && in_array($attempt->status, ['submitted', 'auto_submitted']);
                $exam->attempt_status = $attempt->status ?? null;
                $exam->first_attempt_id = $attempt->id ?? null;
                return $exam;
            });

        return view('student.exams.index', compact('exams'));
    }

    public function verifyCode(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'exam_code' => ['required', 'string'],
        ]);

        if (empty($exam->exam_code)) {
            return redirect()->route('student.exams.start', $exam);
        }

        if (!hash_equals((string) $exam->exam_code, (string) $data['exam_code'])) {
            return back()
                ->withErrors(['exam_code' => 'Invalid exam code.'])
                ->withInput()
                ->with('exam_code_for', $exam->id);
        }

        $request->session()->put("exam_code_ok.{$exam->id}", true);

        return redirect()->route('student.exams.start', $exam);
    }

    public function start(Exam $exam)
{
    if (!empty($exam->exam_code) && !session()->get("exam_code_ok.{$exam->id}")) {
        return redirect()
            ->route('student.exams.index')
            ->with('error', 'Exam code required to start this exam.');
    }

    $user = auth()->user();

    // Get the latest attempt for this exam by this user (any status)
    $latestAttempt = ExamAttempt::where('exam_id', $exam->id)
        ->where('user_id', $user->id)
        ->latest('id')
        ->first();

    // ✅ If there's an in-progress attempt, never create a new one
    if ($latestAttempt && $latestAttempt->status === 'in_progress') {

        // ✅ If expired -> auto-submit on server (no client needed)
        if ($latestAttempt->ends_at && now()->greaterThanOrEqualTo($latestAttempt->ends_at)) {

            // --- compute score using saved answers (same logic as submit) ---
            $orderIds = $latestAttempt->question_order ?? [];
            if (!is_array($orderIds)) $orderIds = (array) $orderIds;
            $orderIds = array_values(array_map('intval', $orderIds));

            $optionsByQuestion = QuestionOption::whereIn('question_id', $orderIds)
                ->get()
                ->groupBy('question_id');

            $existing = ExamAttemptAnswer::where('attempt_id', $latestAttempt->id)
                ->get()
                ->keyBy('question_id');

            $score = 0;

            foreach ($orderIds as $qid) {
                $qid = (int) $qid;
                $selectedOptionId = $existing[$qid]->selected_option_id ?? null;

                if ($selectedOptionId === null || $selectedOptionId === '') {
                    ExamAttemptAnswer::updateOrCreate(
                        ['attempt_id' => $latestAttempt->id, 'question_id' => $qid],
                        ['selected_option_id' => null, 'is_correct' => false]
                    );
                    continue;
                }

                $selectedOptionId = (int) $selectedOptionId;
                $opt = ($optionsByQuestion[$qid] ?? collect())->firstWhere('id', $selectedOptionId);

                if (!$opt) {
                    ExamAttemptAnswer::updateOrCreate(
                        ['attempt_id' => $latestAttempt->id, 'question_id' => $qid],
                        ['selected_option_id' => null, 'is_correct' => false]
                    );
                    continue;
                }

                $isCorrect = (bool) $opt->is_correct;
                if ($isCorrect) $score++;

                ExamAttemptAnswer::updateOrCreate(
                    ['attempt_id' => $latestAttempt->id, 'question_id' => $qid],
                    ['selected_option_id' => $selectedOptionId, 'is_correct' => $isCorrect]
                );
            }

            $latestAttempt->score = $score;
            $latestAttempt->submitted_at = now();
            $latestAttempt->status = 'auto_submitted';
            $latestAttempt->save();

            // ✅ After timeout, do NOT start new attempt
            return redirect()->route('student.exams.index')
                ->with('success', 'Time is over. Your exam was auto-submitted.');
        }

        // ✅ Not expired -> continue attempt
        return redirect()->route('student.exams.attempt', [$exam, $latestAttempt]);
    }

    // ✅ If already submitted, block new attempt (unless you want retake)
    if ($latestAttempt && in_array($latestAttempt->status, ['submitted', 'auto_submitted'], true)) {
        return redirect()->route('student.exams.index')
            ->with('success', 'You have already submitted this exam.');
    }

    // ✅ defaults
    $limit = (int) ($exam->question_limit ?? 40);
    if ($limit < 1) $limit = 40;

    $mode = $exam->selection_mode ?? 'all'; // all | first_n | random_n | manual

    $poolQuery = $exam->questions()->orderBy('position');

    if ($mode === 'manual') {
        $poolQuery->where('is_included', 1);
    }

    $poolIds = $poolQuery->pluck('id')->map(fn($v) => (int) $v)->toArray();

    if (count($poolIds) === 0) {
        return back()->with('error', 'No questions available for this exam.');
    }

    $selectedIds = $poolIds;

    if ($mode === 'first_n') {
        $selectedIds = array_slice($poolIds, 0, $limit);
    } elseif ($mode === 'random_n') {
        if (count($poolIds) > $limit) {
            shuffle($poolIds);
            $selectedIds = array_slice($poolIds, 0, $limit);
        } else {
            $selectedIds = $poolIds;
        }
    } elseif ($mode === 'manual') {
        $selectedIds = array_slice($poolIds, 0, $limit);
    } else {
        $selectedIds = $poolIds; // all
    }

    // ✅ shuffle per attempt if exam says shuffled
    if (($exam->question_mode ?? 'ordered') === 'shuffled') {
        shuffle($selectedIds);
    }

    // ✅ Create attempt ONCE
    $attempt = new ExamAttempt();
    $attempt->exam_id = $exam->id;
    $attempt->user_id = $user->id;
    $attempt->status = 'in_progress';
    $attempt->started_at = now();

    if (!empty($exam->duration_minutes)) {
        $attempt->ends_at = now()->addMinutes((int) $exam->duration_minutes);
    }

    $attempt->total_questions = count($selectedIds);
    $attempt->question_order = $selectedIds;
    $attempt->save();

    return redirect()->route('student.exams.attempt', [$exam, $attempt]);
}


    public function attempt(Exam $exam, ExamAttempt $attempt)
    {
        abort_if($attempt->exam_id !== $exam->id, 403);
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // Block if submitted or auto-submitted - redirect to dashboard
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'This attempt is already submitted.');
        }

        // Auto-submit when time is over
        if ($attempt->ends_at && now()->greaterThanOrEqualTo($attempt->ends_at)) {
            $this->finalizeAttempt($attempt, true);
            return redirect()->route('student.exams.result', ['exam' => $exam->id, 'attempt' => $attempt->id])
                ->with('info', 'This attempt is already submitted.');
        }

        $remaining = $attempt->ends_at ? now()->diffInSeconds($attempt->ends_at, false) : 0;
        $remaining = max(0, (int) $remaining);

        $serverNow = now()->getTimestamp();
        $expiresAt = $attempt->ends_at ? $attempt->ends_at->getTimestamp() : null;

        return view('student.exams.attempt', [
            'exam' => $exam,
            'attempt' => $attempt,
            'remaining' => $remaining,
            'server_now' => $serverNow,
            'expires_at' => $expiresAt,
        ]);
    }

    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        abort_if($attempt->exam_id !== $exam->id, 403);
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // Block if submitted or auto-submitted
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt is already submitted.',
                'redirect' => route('student.dashboard'),
            ], 403);
        }

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'selected' => ['nullable', 'in:A,B,C,D'],
        ]);

        $qid = (int) $data['question_id'];
        $selected = $data['selected'] ?? null;

        $question = Question::find($qid);
        if (!$question) {
            return response()->json(['ok' => false, 'message' => 'Question not found'], 404);
        }

        $isCorrect = $selected !== null && $selected === $question->correct_option;

        // Map to question_options.id when possible
        $selectedOptionId = null;
        if ($selected !== null) {
            $opt = \App\Models\QuestionOption::where('question_id', $qid)
                ->where('option_key', $selected)
                ->first();
            if ($opt) $selectedOptionId = $opt->id;
        }

        ExamAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $qid],
            [
                'selected_option' => $selected,
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function review(Exam $exam, ExamAttempt $attempt)
    {
        abort_if((int) $attempt->exam_id !== (int) $exam->id, 403);
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // Block if submitted or auto-submitted - redirect to dashboard
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'This attempt is already submitted.');
        }

        $orderIds = $attempt->question_order ?? [];

        $questions = Question::whereIn('id', $orderIds)->get()->keyBy('id');

        $orderedQuestions = collect($orderIds)
            ->map(fn ($id) => $questions->get($id))
            ->filter()
            ->values();

        $existing = ExamAttemptAnswer::where('attempt_id', $attempt->id)
            ->with('selectedOption')
            ->get()
            ->keyBy('question_id');

        $server_now = now()->getTimestamp();
        $expires_at = $attempt->ends_at ? $attempt->ends_at->getTimestamp() : null;
        $remainingSeconds = $expires_at ? max(0, $expires_at - $server_now) : 0;

        return view('student.exams.review', [
            'exam' => $exam,
            'attempt' => $attempt,
            'questions' => $orderedQuestions,
            'existing' => $existing,
            'remainingSeconds' => $remainingSeconds,
            'server_now' => $server_now,
            'expires_at' => $expires_at,
        ]);
    }

    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        abort_if($attempt->exam_id !== $exam->id, 403);
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // Block if submitted or auto-submitted - redirect to dashboard
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return redirect()->route('student.dashboard')
                ->with('info', 'This attempt has already been submitted.');
        }

        $this->saveAnswers($request, $attempt);

        $auto = $attempt->ends_at && now()->greaterThanOrEqualTo($attempt->ends_at);
        if ($request->input('reason') === 'timeout') {
            $auto = true;
        }

        $this->finalizeAttempt($attempt, $auto);

        return redirect()->route('student.exams.index')
            ->with('success', 'Your exam was submitted. Please wait until results are published.');
    }

    public function result(Exam $exam, ExamAttempt $attempt)
    {
        abort_if($attempt->exam_id !== $exam->id, 403);
        abort_if($attempt->user_id !== auth()->id(), 403);
        abort_if(!in_array($attempt->status, ['submitted', 'auto_submitted'], true), 403);

        $answers = ExamAttemptAnswer::with('selectedOption')
            ->where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        $orderedQuestions = $this->buildOrderedQuestions($exam, $attempt);
        $published = (bool) ($exam->results_published ?? false);

        return view('student.exams.result', compact(
            'exam',
            'attempt',
            'answers',
            'orderedQuestions',
            'published'
        ));
    }

    public function resultsIndex()
    {
        $user = Auth::user();

        $attempts = ExamAttempt::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->with('exam')
            ->orderByDesc('submitted_at')
            ->get();

        return view('student.results.index', compact('attempts'));
    }

    /**
     * ✅ PDF Exam Paper
     * Tamil fonts: put font file here => storage/fonts/NotoSansTamil-Regular.ttf
     */
    public function paper(Exam $exam, ExamAttempt $attempt)
    {
        abort_if($attempt->exam_id !== $exam->id, 403);
        abort_if($attempt->user_id !== auth()->id(), 403);

        if (!$exam->results_published) {
            return redirect()
                ->route('student.exams.index')
                ->with('error', 'Results are not published yet.');
        }

        $orderedQuestions = $this->buildOrderedQuestions($exam, $attempt);

        // ✅ watermark (base64)
        $watermark = null;
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            $watermark = "data:image/{$ext};base64," . base64_encode(file_get_contents($logoPath));
        }

        // ✅ Tamil font absolute path (must exist)
        $tamilFontPath = storage_path('fonts/NotoSansTamil-Regular.ttf');

        // ✅ DomPDF options (helps unicode + remote images)
        $pdf = Pdf::loadView('student.exams.paper', [
            'exam' => $exam,
            'attempt' => $attempt,
            'orderedQuestions' => $orderedQuestions,
            'watermark' => $watermark,
            'tamilFontPath' => $tamilFontPath,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $exam->title);
        $fileName = "ExamPaper_{$safeTitle}_Attempt{$attempt->id}.pdf";

        return $pdf->download($fileName);
    }

    // -----------------------
    // Helpers
    // -----------------------

    private function saveAnswers(Request $request, ExamAttempt $attempt): void
    {
        $answers  = $request->input('answers', []);
        $orderIds = $attempt->question_order ?? [];

        // Load all options for the questions in this attempt
        $optionsByQuestion = \App\Models\QuestionOption::whereIn('question_id', $orderIds)
            ->get()
            ->groupBy('question_id');

        foreach ($orderIds as $qid) {
            $qid = (int) $qid;

            $value = $answers[$qid] ?? null;  // can be option_id (number) OR A/B/C/D

            $selectedOptionId = null;
            $selectedKey      = null;
            $isCorrect        = false;

            if ($value !== null && $value !== '') {

                // ✅ If frontend sends numeric option_id
                if (is_numeric($value)) {
                    $selectedOptionId = (int) $value;

                    $opt = ($optionsByQuestion[$qid] ?? collect())->firstWhere('id', $selectedOptionId);
                    if ($opt) {
                        $selectedKey = $opt->option_key;           // A/B/C/D/E
                        $isCorrect   = (bool) $opt->is_correct;    // true/false
                    } else {
                        // invalid option id for this question
                        $selectedOptionId = null;
                    }
                }
                // ✅ If frontend sends legacy A/B/C/D
                else if (in_array($value, ['A', 'B', 'C', 'D', 'E'], true)) {
                    $selectedKey = $value;

                    $opt = ($optionsByQuestion[$qid] ?? collect())->firstWhere('option_key', $selectedKey);
                    if ($opt) {
                        $selectedOptionId = (int) $opt->id;
                        $isCorrect        = (bool) $opt->is_correct;
                    }
                }
            }

            \App\Models\ExamAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qid],
                [
                    'selected_option_id' => $selectedOptionId,
                    'selected_option'    => $selectedKey,     // keeps compatibility
                    'is_correct'         => $isCorrect,
                ]
            );
        }
    }

    private function finalizeAttempt(ExamAttempt $attempt, bool $auto): void
    {
        // ✅ Calculate score by counting correct answers after they've been saved
        $score = ExamAttemptAnswer::where('attempt_id', $attempt->id)
            ->where('is_correct', true)
            ->count();

        // ✅ Save score AFTER answers are confirmed to be saved
        $attempt->score = $score;
        $attempt->submitted_at = now();
        $attempt->status = $auto ? 'auto_submitted' : 'submitted';
        $attempt->save();
    }

    private function buildOrderedQuestions(Exam $exam, ExamAttempt $attempt)
    {
        $orderIds = $attempt->question_order ?? [];

        if (!is_array($orderIds) || count($orderIds) === 0) {
            $orderIds = Question::where('exam_id', $exam->id)->pluck('id')->toArray();
        }

        $questionsMap = Question::with(['options' => function ($q) {
            $q->orderBy('id');
        }])
            ->whereIn('id', $orderIds)
            ->get()
            ->keyBy('id');

        return collect($orderIds)
            ->map(fn ($id) => $questionsMap[$id] ?? null)
            ->filter()
            ->values();
    }
}
