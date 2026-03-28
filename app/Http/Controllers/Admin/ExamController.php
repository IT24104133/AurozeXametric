<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::withCount([
                'attempts as attempted_count' => function ($q) {
                    $q->whereIn('status', ['in_progress', 'submitted', 'auto_submitted']);
                },
                'attempts as completed_count' => function ($q) {
                    $q->whereIn('status', ['submitted', 'auto_submitted']);
                },
            ])
            ->orderByDesc('id')
            ->get();

        \Log::info('📊 Exam Index Page Loaded', [
            'total_exams' => $exams->count(),
            'sql' => Exam::toBase()->toSql(),
            'exam_ids' => $exams->pluck('id')->toArray(),
        ]);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        \Log::info('📝 Exam Create Form Submitted', [
            'user_id' => auth()->id(),
            'payload' => $request->except(['_token'])
        ]);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'exam_code' => 'nullable|string|max:50|unique:exams,exam_code',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',

            // Order inside the selected question set
            'question_mode' => 'required|in:ordered,shuffled',
            
            // ✅ NEW: MCQ option count
            'option_count' => 'required|integer|in:3,4,5',

            // pool selection settings
            'question_limit' => 'nullable|integer|min:1',
            'selection_mode' => 'nullable|in:all,first_n,random_n,manual',

            // ✅ NEW: custom popup settings
            'custom_success_popup_title' => 'nullable|string|max:255',
            'custom_success_popup_message' => 'nullable|string',
            'custom_success_popup_link' => 'nullable|string|max:2048',
        ]);

        \Log::info('✓ Validation passed');

        // Defaults (if not sent)
        $data['question_limit'] = $data['question_limit'] ?? 40;
        $data['selection_mode'] = $data['selection_mode'] ?? 'all';
        $data['option_count'] = $data['option_count'] ?? 4; // Default to 4 options

        // ✅ checkbox booleans
        $data['custom_success_popup_enabled'] = $request->boolean('custom_success_popup_enabled');
        $data['custom_success_popup_show_copy'] = $request->boolean('custom_success_popup_show_copy');

        // ✅ if disabled, keep DB clean
        if (!$data['custom_success_popup_enabled']) {
            $data['custom_success_popup_title'] = null;
            $data['custom_success_popup_message'] = null;
            $data['custom_success_popup_link'] = null;
            $data['custom_success_popup_show_copy'] = true;
        } else {
            // if link empty -> no copy
            if (empty($data['custom_success_popup_link'])) {
                $data['custom_success_popup_show_copy'] = false;
            }
        }

        // Auto-generate exam code if not provided
        if (empty($data['exam_code'])) {
            $data['exam_code'] = $this->generateExamCode();
        }

        // Auto-generate exam UID
        $data['exam_uid'] = $this->generateExamUid();

        try {
            $exam = Exam::create(array_merge($data, [
                'status' => 'draft',
                'created_by' => auth()->id(),
                'results_published' => false,
            ]));

            \Log::info('✓ Exam created successfully', [
                'exam_id' => $exam->id,
                'title' => $exam->title,
                'exam_code' => $exam->exam_code,
                'exam_uid' => $exam->exam_uid,
            ]);

            return redirect()->route('admin.exams.index')
                ->with('success', 'Exam created successfully (Draft)');
        } catch (\Exception $e) {
            \Log::error('✗ Failed to create exam', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create exam: ' . $e->getMessage()]);
        }
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'exam_code' => 'nullable|string|max:50|unique:exams,exam_code,' . $exam->id,
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',

            // Order inside the selected question set
            'question_mode' => 'required|in:ordered,shuffled',
            
            // ✅ NEW: MCQ option count
            'option_count' => 'required|integer|in:3,4,5',

            // pool selection settings
            'question_limit' => 'nullable|integer|min:1',
            'selection_mode' => 'nullable|in:all,first_n,random_n,manual',

            // ✅ NEW: custom popup settings
            'custom_success_popup_title' => 'nullable|string|max:255',
            'custom_success_popup_message' => 'nullable|string',
            'custom_success_popup_link' => 'nullable|string|max:2048',
        ]);

        $data['question_limit'] = $data['question_limit'] ?? 40;
        $data['selection_mode'] = $data['selection_mode'] ?? 'all';
        $data['option_count'] = $data['option_count'] ?? 4; // Default to 4 options

        // ✅ checkbox booleans
        $data['custom_success_popup_enabled'] = $request->boolean('custom_success_popup_enabled');
        $data['custom_success_popup_show_copy'] = $request->boolean('custom_success_popup_show_copy');

        // ✅ if disabled, clean fields
        if (!$data['custom_success_popup_enabled']) {
            $data['custom_success_popup_title'] = null;
            $data['custom_success_popup_message'] = null;
            $data['custom_success_popup_link'] = null;
            $data['custom_success_popup_show_copy'] = true;
        } else {
            // if link empty -> no copy
            if (empty($data['custom_success_popup_link'])) {
                $data['custom_success_popup_show_copy'] = false;
            }
        }

        // Auto-generate exam code if not provided
        if (empty($data['exam_code'])) {
            $data['exam_code'] = $this->generateExamCode();
        }

        $exam->update($data);

        return back()->with('success', 'Exam updated');
    }

    public function publish(Exam $exam)
    {
        $exam->update([
            'status' => $exam->status === 'published' ? 'draft' : 'published'
        ]);

        return back()->with('success', 'Exam status updated');
    }

    // publish/hide results
    public function toggleResults(Exam $exam)
    {
        $exam->results_published = !$exam->results_published;
        $exam->save();

        return back()->with(
            'success',
            $exam->results_published ? 'Results published.' : 'Results hidden.'
        );
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return back()->with('success', 'Exam deleted successfully.');
    }

    /**
     * Generate sequential exam code like NGIE01, NGIE02, etc.
     */
    private function generateExamCode(): string
    {
        $prefix = 'NGIE';
        
        // Get the last exam code with this prefix
        $lastExam = Exam::where('exam_code', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(exam_code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();
        
        if ($lastExam && preg_match('/' . $prefix . '(\d+)/', $lastExam->exam_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Generate sequential exam UID like NGIE001, NGIE002, etc.
     */
    private function generateExamUid(): string
    {
        $prefix = 'NGIE';
        
        // Get the last exam UID with this prefix
        $lastExam = Exam::where('exam_uid', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(exam_uid, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();
        
        if ($lastExam && preg_match('/' . $prefix . '(\d+)/', $lastExam->exam_uid, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
