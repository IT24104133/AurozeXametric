<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\User;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $identifier = trim($validated['identifier']);
        $password   = $validated['password'];
        $remember   = (bool) $request->boolean('remember');

        // Identify email vs student_id
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        // ✅ IMPORTANT:
        // For email: normal Auth attempt
        // For student_id: try to find user and attempt with that user’s email OR direct attempt by student_id
        // (Laravel default Auth::attempt checks a "username field" only if that field exists in users table.
        // You DO have student_id field, so attempt works fine.)
        $credentials = $isEmail
            ? ['email' => $identifier, 'password' => $password]
            : ['student_id' => $identifier, 'password' => $password];

        Log::debug('Login attempt', ['identifier' => $identifier, 'is_email' => $isEmail]);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Role-based redirect (force correct dashboards)
            if ($user && $user->role === 'admin') {
                // if you have named route:
                // return redirect()->route('admin.dashboard');
                return redirect()->intended('/admin/dashboard');
            }

            if ($user && $user->role === 'student') {
                // if you have named route:
                // return redirect()->route('student.dashboard');
                return redirect()->intended('/student/dashboard');
            }

            // fallback
            return redirect()->intended('/');
        }

        // Optional helpful log without password checks
        try {
            $lookUpUser = $isEmail
                ? User::where('email', $identifier)->first()
                : User::where('student_id', $identifier)->first();

            Log::info('Login failed', [
                'identifier' => $identifier,
                'found'      => (bool) $lookUpUser,
                'user_id'    => $lookUpUser->id ?? null,
                'role'       => $lookUpUser->role ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Login diagnostics error', ['error' => $e->getMessage()]);
        }

        return back()->withErrors([
            'identifier' => 'These credentials do not match our records.',
        ])->onlyInput('identifier');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
