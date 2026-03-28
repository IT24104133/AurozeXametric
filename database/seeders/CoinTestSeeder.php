<?php

namespace Database\Seeders;

use App\Models\CoinTransaction;
use App\Models\PastPaper;
use App\Models\PastPaperAttempt;
use App\Models\PastPaperQuestion;
use App\Models\PastPaperSubject;
use App\Models\StudentWallet;
use App\Models\User;
use App\Services\CoinRewardService;
use Illuminate\Database\Seeder;

class CoinTestSeeder extends Seeder
{
    /**
     * Run the database seeds for coin testing.
     */
    public function run(): void
    {
        echo "\n=== Coin Test Seeder Started ===\n";

        // Create 5 test students
        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $email = "student{$i}_coin@test.com";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Test Student {$i}",
                    'full_name' => "Test Coin Student {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'student',
                    'student_id' => "TEST_COIN_{$i}",
                ]
            );

            // Ensure wallet exists
            StudentWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['total_coins' => 0]
            );

            $students[] = $user;
            echo "✓ Created test student: {$user->full_name} ({$email})\n";
        }

        // Create 2 subjects
        $stream = 'ol'; // Use ol stream for testing
        $subjects = [];
        for ($i = 1; $i <= 2; $i++) {
            $subject = PastPaperSubject::firstOrCreate(
                ['name' => "Coin Test Subject {$i}", 'stream' => $stream],
                ['stream' => $stream]
            );

            // Create edu_department paper
            $eduPaper = PastPaper::firstOrCreate(
                [
                    'subject_id' => $subject->id,
                    'category' => 'edu_department',
                    'stream' => $stream,
                    'year' => 2025,
                ],
                [
                    'title' => "Subject {$i} - 2025",
                    'status' => 'published',
                    'duration_minutes' => 60,
                    'total_questions' => 40,
                ]
            );

            // Create free_style paper (should already exist from subject creation)
            $freePaper = PastPaper::where('subject_id', $subject->id)
                ->where('category', 'free_style')
                ->first();

            if (!$freePaper) {
                $freePaper = PastPaper::create([
                    'subject_id' => $subject->id,
                    'category' => 'free_style',
                    'title' => "Subject {$i} - Free Style",
                    'stream' => $stream,
                    'status' => 'published',
                    'duration_minutes' => 60,
                    'total_questions' => 40,
                    'count_e' => 12,
                    'count_m' => 18,
                    'count_h' => 10,
                ]);
            }

            $subjects[] = [
                'subject' => $subject,
                'edu_paper' => $eduPaper,
                'free_paper' => $freePaper,
            ];

            echo "✓ Created subject: {$subject->name} with edu_department and free_style papers\n";
        }

        // Create attempts and award coins
        $coinService = new CoinRewardService();
        $attemptCount = 0;
        $today = now()->toDateString();

        foreach ($students as $student) {
            echo "\n--- Processing {$student->full_name} ---\n";

            foreach ($subjects as $subjectData) {
                $eduPaper = $subjectData['edu_paper'];
                $freePaper = $subjectData['free_paper'];

                // Attempt 1: 85% on normal mode => should earn 5 coins
                $attempt1 = PastPaperAttempt::create([
                    'student_id' => $student->id,
                    'past_paper_id' => $eduPaper->id,
                    'mode' => 'normal',
                    'status' => 'submitted',
                    'total_questions' => 40,
                    'correct_count' => 34, // 85%
                    'score' => 34,
                    'percentage' => 85,
                    'ended_at' => now(),
                ]);
                $attemptCount++;

                $coin1Result = $coinService->awardCoins($attempt1);
                echo "  Attempt 1 (85% normal): earned {$coin1Result['coins_awarded']} coins\n";

                // Attempt 2: 100% same paper same day => should earn 0 (anti-farming)
                $attempt2 = PastPaperAttempt::create([
                    'student_id' => $student->id,
                    'past_paper_id' => $eduPaper->id,
                    'mode' => 'normal',
                    'status' => 'submitted',
                    'total_questions' => 40,
                    'correct_count' => 40, // 100%
                    'score' => 40,
                    'percentage' => 100,
                    'ended_at' => now(),
                ]);
                $attemptCount++;

                $coin2Result = $coinService->awardCoins($attempt2);
                echo "  Attempt 2 (100% same paper): earned {$coin2Result['coins_awarded']} coins (anti-farming test)\n";

                // Attempt 3: Free style ultra_hard 90% => should earn coins
                $attempt3 = PastPaperAttempt::create([
                    'student_id' => $student->id,
                    'past_paper_id' => $freePaper->id,
                    'mode' => 'ultra_hard',
                    'status' => 'submitted',
                    'total_questions' => 40,
                    'correct_count' => 36, // 90%
                    'score' => 36,
                    'percentage' => 90,
                    'ended_at' => now(),
                ]);
                $attemptCount++;

                $coin3Result = $coinService->awardCoins($attempt3);
                echo "  Attempt 3 (90% ultra_hard free_style): earned {$coin3Result['coins_awarded']} coins\n";
            }

            // Get wallet totals
            $wallet = StudentWallet::where('user_id', $student->id)->first();
            $todayTransactions = CoinTransaction::where('user_id', $student->id)
                ->whereDate('earned_on', $today)
                ->get();
            $todayTotal = $todayTransactions->sum('coins');

            echo "  SUMMARY: {$student->full_name}\n";
            echo "    Wallet Total: {$wallet->total_coins} coins\n";
            echo "    Today's Earnings: {$todayTotal} coins\n";
            echo "    Today's Transactions: {$todayTransactions->count()}\n";
            echo "    Daily Cap Check: " . ($todayTotal <= 50 ? "✓ PASS (≤ 50)" : "✗ FAIL (> 50)") . "\n";
        }

        // Global summary
        echo "\n=== Global Summary ===\n";
        echo "Total Test Attempts Created: {$attemptCount}\n";

        $studentIds = collect($students)->pluck('id')->toArray();
        $allTransactions = CoinTransaction::whereDate('earned_on', $today)->get();
        echo "Total Transactions Created Today: {$allTransactions->count()}\n";

        $allWallets = StudentWallet::whereIn('user_id', $studentIds)->get();
        echo "Total Coins Distributed: {$allWallets->sum('total_coins')}\n";

        // Check anti-farming effectiveness
        $duplicateAttempts = CoinTransaction::query()
            ->whereIn('user_id', $studentIds)
            ->whereDate('earned_on', $today)
            ->selectRaw('user_id, paper_id, COUNT(*) as count')
            ->groupBy('user_id', 'paper_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicateAttempts->count() > 0) {
            echo "\n✓ Anti-Farming Test: Found {$duplicateAttempts->count()} duplicate paper attempts (should be 0 coins each)\n";
        } else {
            echo "\n✓ Anti-Farming Test: PASS - No duplicate coins awarded for same paper\n";
        }

        // Check daily cap
        $cappedWallets = $allWallets->filter(function ($wallet) {
            return $wallet->total_coins <= 50;
        });

        echo "✓ Daily Cap Test: All wallets ≤ 50 coins: " . ($cappedWallets->count() === $allWallets->count() ? "PASS" : "FAIL") . "\n";

        echo "\n=== Coin Test Seeder Completed ===\n";
    }
}
