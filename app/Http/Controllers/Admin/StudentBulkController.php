<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentBulkController extends Controller
{
    public function create()
    {
        return view('admin.students.bulk-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'count' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $count = (int) $data['count'];
        $created = [];

        DB::transaction(function () use ($count, &$created) {
            // Lock the "last student_id" row so two admins can't generate duplicates at the same time
            $lastId = User::whereNotNull('student_id')
                ->where('student_id', 'like', 'NGI%')
                ->orderByRaw("CAST(SUBSTRING(student_id, 4) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->value('student_id');

            $nextNumber = 1000;
            if ($lastId) {
                $nextNumber = ((int) substr($lastId, 3)) + 1;
            }

            for ($i = 0; $i < $count; $i++) {
                $studentId = 'NGI' . str_pad((string) ($nextNumber + $i), 4, '0', STR_PAD_LEFT);

                // temp password for first login
                $tempPassword = 'Stu@' . Str::upper(Str::random(6)) . rand(10, 99);

                $user = User::create([
                    'name' => $studentId,
                    // Email is required by default Laravel auth; use a unique placeholder
                    'email' => $studentId . '@example.local',
                    'password' => Hash::make($tempPassword),
                    'role' => 'student',
                    'student_id' => $studentId,
                ]);

                $created[] = [
                    'student_id' => $user->student_id,
                    'temp_password' => $tempPassword,
                ];
            }
        });

        return view('admin.students.bulk-created', compact('created'));
    }
}
