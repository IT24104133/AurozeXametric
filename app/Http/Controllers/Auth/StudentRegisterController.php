<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class StudentRegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function success(Request $request): View
    {
        // if someone opens success url directly without data
        abort_if(!session('new_student_id') || !session('new_temp_password'), 404);

        return view('auth.register_success', [
            'student_id' => session('new_student_id'),
            'temp_password' => session('new_temp_password'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name'       => ['required', 'string', 'max:255'],
            'school_name'     => ['nullable', 'string', 'max:255'],
            'contact_number'  => ['nullable', 'string', 'max:30'],
        ]);

        $tempPassword = $this->generateTempPassword(10);

        $user = null;

        for ($i = 0; $i < 5; $i++) {
            $studentId = $this->generateNextStudentId('NGI', 4);

            try {
                $user = User::create([
    'name'           => $validated['full_name'],
    'full_name'      => $validated['full_name'],
    'email'          => null, // ✅ now allowed
    'school_name'    => $validated['school_name'] ?? null,
    'contact_number' => $validated['contact_number'] ?? null,
    'role'           => 'student',
    'student_id'     => $studentId,
    'password'       => Hash::make($tempPassword),
    'password_changed_at' => null,
]);
                break;
            } catch (QueryException $e) {
                $user = null;
                continue;
            }
        }

        if (!$user) {
            return back()->withErrors(['full_name' => 'Registration failed. Please try again.'])->withInput();
        }

        // store in session and redirect to success page
        return redirect()
            ->route('register.success')
            ->with('new_student_id', $user->student_id)
            ->with('new_temp_password', $tempPassword);
    }

    private function generateTempPassword(int $length = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        return substr(str_shuffle(str_repeat($alphabet, 5)), 0, $length);
    }

    private function generateNextStudentId(string $prefix = 'NGI', int $pad = 4): string
    {
        $latest = User::where('student_id', 'like', $prefix . '%')
            ->orderBy('student_id', 'desc')
            ->value('student_id');

        if (!$latest) {
            return $prefix . str_pad('1', $pad, '0', STR_PAD_LEFT);
        }

        $numPart = preg_replace('/\D+/', '', $latest);
        $num = (int) ($numPart ?: 0);
        $next = $num + 1;

        $digits = max($pad, strlen((string) $next));
        return $prefix . str_pad((string) $next, $digits, '0', STR_PAD_LEFT);
    }
}
