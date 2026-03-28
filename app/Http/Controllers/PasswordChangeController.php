<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            $errors = ['current_password' => 'Current password is incorrect.'];
            
            if ($request->expectsJson()) {
                return response()->json(['errors' => $errors], 422);
            }
            
            return back()->withErrors($errors);
        }

        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        // If AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Password updated successfully.',
                'user' => $user
            ]);
        }

        return redirect()->to('/student')->with('status', 'Password updated.');
    }
}
