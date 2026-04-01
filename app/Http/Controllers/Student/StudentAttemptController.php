<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\QuestionFormatter;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentAttemptController extends Controller
{
    /**
     * ✅ Server-side safety net:
     * Auto-submit if attempt is expired but still "in_progress".
     * This prevents stuck in_progress attempts when JS timer fails.
     */
    private function autoSubmitIfExpired(ExamAttempt $attempt): void
    {
        if ($attempt->status !== 'in_progress') return;
        if (!$attempt->ends_at) return;
        if (now()->lt($attempt->ends_at)) return;

        DB::transaction(function () use ($attempt) {

            // Refresh inside transaction to avoid race conditions
            $attempt->refresh();

            if ($attempt->status !== 'in_progress') return;
            if (!$attempt->ends_at) return;
            if (now()->lt($attempt->ends_at)) return;

            // ✅ normalize question_order into int array
            $orderIds = $attempt->question_order ?? [];
            if (!is_array($orderIds)) $orderIds = (array) $orderIds;
            $orderIds = array_values(array_map('intval', $orderIds));

            // Load all options for all questions in this attempt
            $optionsByQuestion = QuestionOption::whereIn('question_id', $orderIds)
                ->get()
                ->groupBy('question_id');

            $existing = ExamAttemptAnswer::where('attempt_id', $attempt->id)
                ->get()
                ->keyBy('question_id');

            $score = 0;

            foreach ($orderIds as $qid) {
                $qid = (int) $qid;

                $selectedOptionId = $existing[$qid]->selected_option_id ?? null;

                // if empty => save as empty
                if ($selectedOptionId === null || $selectedOptionId === '') {
                    ExamAttemptAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $qid],
                        ['selected_option_id' => null, 'is_correct' => false]
                    );
                    continue;
                }

                $selectedOptionId = (int) $selectedOptionId;

                $opts = $optionsByQuestion[$qid] ?? collect();
                $opt = $opts->firstWhere('id', $selectedOptionId);

                // Invalid option id -> treat as empty
                if (!$opt) {
                    ExamAttemptAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $qid],
                        ['selected_option_id' => null, 'is_correct' => false]
                    );
                    continue;
                }

                $isCorrect = (bool) $opt->is_correct;
                if ($isCorrect) $score++;

                ExamAttemptAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $qid],
                    ['selected_option_id' => $selectedOptionId, 'is_correct' => $isCorrect]
                );
            }

            $attempt->score = $score;
            $attempt->submitted_at = now();
            $attempt->status = 'auto_submitted';
            $attempt->save();
        });
    }

    /**
     * Attempt meta (lightweight)
     * GET /student/attempts/{attempt}/meta
     */
    public function meta(Request $request, ExamAttempt $attempt)
    {
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // ✅ auto-submit if expired (server-side safety net)
        $this->autoSubmitIfExpired($attempt);
        $attempt->refresh();

        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt is already submitted.',
                'redirect' => route('student.exams.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id]),
            ], 403);
        }

        // ✅ normalize question_order into int array
        $orderIds = $attempt->question_order ?? [];
        if (!is_array($orderIds)) $orderIds = (array) $orderIds;
        $orderIds = array_values(array_map('intval', $orderIds));

        // ✅ return selected_option_id map: { question_id: option_id|null }
        $existing = ExamAttemptAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->mapWithKeys(fn ($a) => [(int)$a->question_id => $a->selected_option_id ? (int)$a->selected_option_id : null])
            ->toArray();

        $serverNow = now()->getTimestamp();
        $expiresAt = $attempt->ends_at ? $attempt->ends_at->getTimestamp() : null;

        return response()->json([
            'ok' => true,
            'attempt' => [
                'id' => $attempt->id,
                'status' => $attempt->status,
                'started_at' => optional($attempt->started_at)->toISOString(),
                'ends_at' => optional($attempt->ends_at)->toISOString(),
                'server_now' => $serverNow,
                'expires_at' => $expiresAt,
                'total_questions' => (int) ($attempt->total_questions ?? count($orderIds)),
            ],
            'question_order' => $orderIds,
            'answers' => $existing,
        ]);
    }

    /**
     * Fetch a single question by index
     * GET /student/attempts/{attempt}/question?i=0
     */
    public function question(Request $request, ExamAttempt $attempt)
    {
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // ✅ auto-submit if expired (server-side safety net)
        $this->autoSubmitIfExpired($attempt);
        $attempt->refresh();

        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt is already submitted.',
                'status' => $attempt->status,
                'redirect' => route('student.exams.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id]),
            ], 403);
        }

        // ✅ normalize question_order into int array
        $orderIds = $attempt->question_order ?? [];
        if (!is_array($orderIds)) $orderIds = (array) $orderIds;
        $orderIds = array_values(array_map('intval', $orderIds));

        $i = (int) $request->query('i', 0);
        if ($i < 0 || $i >= count($orderIds)) {
            return response()->json(['ok' => false, 'message' => 'Invalid question index'], 422);
        }

        $qid = (int) $orderIds[$i];

        // ✅ Load options from question_options (dynamic 4/5)
        $q = Question::with(['options' => function ($qq) {
            $qq->orderBy('position');
        }])->find($qid);

        if (!$q) {
            return response()->json(['ok' => false, 'message' => 'Question not found'], 404);
        }

        // selected option id for this attempt/question
        $selectedOptionId = ExamAttemptAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $qid)
            ->value('selected_option_id');

        // question images
        $images = array_values(array_filter([
            !empty($q->image_1) ? asset('storage/' . $q->image_1) : null,
            !empty($q->image_2) ? asset('storage/' . $q->image_2) : null,
            !empty($q->image_3) ? asset('storage/' . $q->image_3) : null,
        ]));

        // options array with id + key + text + image
        $options = $q->options->map(function ($opt) {
            return [
                'id' => (int) $opt->id,
                'key' => $opt->option_key, // A..E
                'text' => nl2br(e(trim($opt->option_text ?? ''))),
                'image' => !empty($opt->option_image) ? asset('storage/' . $opt->option_image) : null,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'index' => $i,
            'question' => [
                'id' => (int) $q->id,
                'text' => QuestionFormatter::format($q->question_text),
                'images' => $images,
                'options' => $options,
            ],
            'selected' => $selectedOptionId ? (int) $selectedOptionId : null,
        ]);
    }

    /**
     * Save a single answer (JSON endpoint)
     * POST /student/attempts/{attempt}/answers
     */
    public function answers(Request $request, ExamAttempt $attempt)
    {
        abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

        // ✅ auto-submit if expired (server-side safety net)
        $this->autoSubmitIfExpired($attempt);
        $attempt->refresh();

        // ✅ lock if already submitted
        if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'This attempt is already submitted.',
                'status' => $attempt->status,
                'redirect' => route('student.exams.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id]),
            ], 403);
        }

        // ✅ If time ended, it should already be auto_submitted above,
        // but keep this as extra guard
        if ($attempt->ends_at && now()->greaterThanOrEqualTo($attempt->ends_at)) {
            return response()->json([
                'ok' => false,
                'message' => 'Time is over. Attempt is locked.',
            ], 403);
        }

        $selectedOptionId = $request->input('selected_option_id', null);

        $validator = Validator::make(
            [
                'question_id' => $request->input('question_id'),
                'selected_option_id' => $selectedOptionId
            ],
            [
                'question_id' => ['required', 'integer'],
                'selected_option_id' => ['nullable', 'integer'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'errors' => $validator->errors()], 422);
        }

        $qid = (int) $request->input('question_id');

        // ✅ normalize question_order into int array
        $order = $attempt->question_order ?? [];
        if (!is_array($order)) $order = (array) $order;
        $order = array_values(array_map('intval', $order));

        if (!in_array($qid, $order, true)) {
            return response()->json(['ok' => false, 'message' => 'Question not part of this attempt'], 422);
        }

        // If null -> clear answer
        if ($selectedOptionId === null) {
            ExamAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qid],
                ['selected_option_id' => null, 'is_correct' => false]
            );
            return response()->json(['ok' => true]);
        }

        $selectedOptionId = (int) $selectedOptionId;

        // ✅ Ensure the option belongs to the question
        $opt = QuestionOption::where('id', $selectedOptionId)
            ->where('question_id', $qid)
            ->first();

        if (!$opt) {
            return response()->json(['ok' => false, 'message' => 'Invalid option for this question'], 422);
        }

        ExamAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $qid],
            ['selected_option_id' => $selectedOptionId, 'is_correct' => (bool) $opt->is_correct]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Submit attempt (JSON endpoint)
     * POST /student/attempts/{attempt}/submit
     */
    public function submit(Request $request, ExamAttempt $attempt)
{
    abort_if((int) $attempt->user_id !== (int) Auth::id(), 403);

    // ✅ auto-submit if expired (server-side safety net)
    $this->autoSubmitIfExpired($attempt);
    $attempt->refresh();

    // if already submitted (either manual or auto), stop
    if (in_array($attempt->status, ['submitted', 'auto_submitted'], true)) {
        return response()->json([
            'ok' => false,
            'message' => 'This attempt is already submitted.',
            'status' => $attempt->status,
            'redirect' => route('student.exams.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id]),
        ], 403);
    }

    $answersInput = $request->input('answers', null); // { question_id: option_id|null }

    // ✅ normalize question_order into int array
    $orderIds = $attempt->question_order ?? [];
    if (!is_array($orderIds)) $orderIds = (array) $orderIds;
    $orderIds = array_values(array_map('intval', $orderIds));

    // Load all options for all questions in this attempt
    $optionsByQuestion = QuestionOption::whereIn('question_id', $orderIds)
        ->get()
        ->groupBy('question_id');

    $existing = ExamAttemptAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

    $score = 0;

    foreach ($orderIds as $qid) {
        $qid = (int) $qid;

        $selectedOptionId = null;

        if (is_array($answersInput) && array_key_exists($qid, $answersInput)) {
            $selectedOptionId = $answersInput[$qid];
        } elseif (isset($existing[$qid])) {
            $selectedOptionId = $existing[$qid]->selected_option_id;
        }

        if ($selectedOptionId === null || $selectedOptionId === '') {
            ExamAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qid],
                ['selected_option_id' => null, 'is_correct' => false]
            );
            continue;
        }

        $selectedOptionId = (int) $selectedOptionId;

        $opts = $optionsByQuestion[$qid] ?? collect();
        $opt = $opts->firstWhere('id', $selectedOptionId);

        // Invalid option id -> treat as empty
        if (!$opt) {
            ExamAttemptAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qid],
                ['selected_option_id' => null, 'is_correct' => false]
            );
            continue;
        }

        $isCorrect = (bool) $opt->is_correct;
        if ($isCorrect) $score++;

        ExamAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $qid],
            ['selected_option_id' => $selectedOptionId, 'is_correct' => $isCorrect]
        );
    }

    $auto = $attempt->ends_at && now()->greaterThanOrEqualTo($attempt->ends_at);
    if ($request->input('reason') === 'timeout') {
        $auto = true;
    }

    $attempt->score = $score;
    $attempt->submitted_at = now();
    $attempt->status = $auto ? 'auto_submitted' : 'submitted';
    $attempt->save();

    // ✅ popup payload (default + admin custom)
    $exam = $attempt->exam; // needs relation in ExamAttempt model

    // default messages
    $popupTitle = $auto ? '⏱️ Time is up!' : '✅ Exam submitted successfully!';
    $popupMessage = $auto
        ? 'Time is over. Your answers were saved and submitted automatically.'
        : 'Your answers were saved and submitted.';
    $popupLink = null;
    $popupShowCopy = false;

    // admin custom popup override (if enabled)
    if ($exam && !empty($exam->custom_success_popup_enabled)) {
        if (!empty($exam->custom_success_popup_title)) {
            $popupTitle = $exam->custom_success_popup_title;
        }

        if (!empty($exam->custom_success_popup_message)) {
            // If admin gives message, use it (works for both submit + timeout)
            $popupMessage = $exam->custom_success_popup_message;
        }

        if (!empty($exam->custom_success_popup_link)) {
            $popupLink = $exam->custom_success_popup_link;
            $popupShowCopy = (bool) ($exam->custom_success_popup_show_copy ?? true);
        }
    }

    return response()->json([
        'ok' => true,
        'redirect' => route('student.exams.index'),
        'popup' => [
            'title' => $popupTitle,
            'message' => $popupMessage,
            'link' => $popupLink,
            'show_copy' => $popupShowCopy,
            'is_timeout' => $auto,
        ],
    ]);
}

}
