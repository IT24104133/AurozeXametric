<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'full_name' => ['required','string','min:3','max:100'],
            'contact_number' => ['required','string','max:20'],
        ];

        if ($user->role === 'student') {
            $rules['school_name'] = ['required','string','max:200'];
        }

        if ($user->role === 'teacher') {
            $rules['nic_number'] = ['required','string','max:20'];
        }

        try {
            $data = $request->validate($rules);

            $user->full_name = $data['full_name'];
            // Keep legacy `name` in sync for views that still use it
            $user->name = $data['full_name'];
            $user->contact_number = $data['contact_number'];
            $user->school_name = $data['school_name'] ?? null;
            $user->nic_number = $data['nic_number'] ?? null;

            $user->profile_completed_at = now();
            $user->save();

            // If AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Profile updated successfully.',
                    'user' => $user
                ], 200);
            }

            return redirect()->to($user->role === 'teacher' ? '/teacher' : '/student')
                ->with('status', 'Profile updated.');
        } catch (\Throwable $e) {
            \Log::error('Profile update failed: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'Server error while updating profile.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update profile.');
        }
    }
}
