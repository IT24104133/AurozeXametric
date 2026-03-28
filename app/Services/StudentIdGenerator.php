<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentIdGenerator
{
    private const PREFIX = 'NGI';
    private const STARTING_NUMBER = 1000;

    /**
     * Create a batch of student users with sequential student IDs.
     * Returns the created users' student_id and plain temporary password
     * so admins can distribute credentials. Passwords are hashed in DB.
     *
     * @return array<int, array{student_id: string, email: string, temporary_password: string}>
     */
    public function createStudents(int $count): array
    {
        return DB::transaction(function () use ($count) {
            $lastStudentId = User::whereNotNull('student_id')
                ->lockForUpdate()
                ->orderByDesc('student_id')
                ->value('student_id');

            $nextNumber = $lastStudentId
                ? max(((int) substr($lastStudentId, 3)) + 1, self::STARTING_NUMBER)
                : self::STARTING_NUMBER;

            $created = [];

            for ($i = 0; $i < $count; $i++) {
                $number = $nextNumber + $i;
                $studentId = self::PREFIX . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
                $temporaryPassword = Str::random(12);

                $user = User::create([
                    'name' => 'Student ' . $studentId,
                    'email' => Str::lower($studentId) . '@students.local',
                    'role' => 'student',
                    'student_id' => $studentId,
                    'password' => Hash::make($temporaryPassword),
                ]);

                $created[] = [
                    'student_id' => $studentId,
                    'email' => $user->email,
                    'temporary_password' => $temporaryPassword,
                ];
            }

            return $created;
        }, 3);
    }
}
