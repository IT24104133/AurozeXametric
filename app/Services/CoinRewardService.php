<?php

namespace App\Services;

use App\Models\CoinTransaction;
use App\Models\PastPaperAttempt;
use App\Models\StudentWallet;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;

class CoinRewardService
{
    const DAILY_COIN_CAP = 50;
    const COINS_ULTRA_EASY_THRESHOLD_80_99 = 0;
    const COINS_ULTRA_EASY_THRESHOLD_100 = 0;
    const COINS_NORMAL_THRESHOLD_80_99 = 5;
    const COINS_NORMAL_THRESHOLD_100 = 10;
    const COINS_ULTRA_MEDIUM_THRESHOLD_80_99 = 3;
    const COINS_ULTRA_MEDIUM_THRESHOLD_100 = 5;
    const COINS_ULTRA_HARD_THRESHOLD_80_99 = 5;
    const COINS_ULTRA_HARD_THRESHOLD_100 = 10;

    /**
     * Award coins to student for attempt completion
     *
     * @param PastPaperAttempt $attempt
     * @return array ['coins_awarded' => int, 'total_today' => int, 'message' => string]
     */
    public function awardCoins(PastPaperAttempt $attempt): array
    {
        $today = now()->toDateString();
        $userId = $attempt->student_id;
        $paperId = $attempt->past_paper_id;
        $score = $attempt->percentage ?? 0;
        $mode = $attempt->mode ?? 'normal';

        // Step 1: Calculate eligible coins based on score and mode
        $eligibleCoins = $this->calculateEligibleCoins($score, $mode);

        if ($eligibleCoins == 0) {
            return [
                'coins_awarded' => 0,
                'total_today' => $this->getTodaysTotalCoins($userId),
                'message' => null,
            ];
        }

        // Step 2: Check if already earned coins for this paper today (anti-farming)
        $existingTransaction = CoinTransaction::where('user_id', $userId)
            ->where('paper_id', $paperId)
            ->whereDate('earned_on', $today)
            ->first();

        if ($existingTransaction) {
            return [
                'coins_awarded' => 0,
                'total_today' => $this->getTodaysTotalCoins($userId),
                'message' => 'You already earned coins for this paper today!',
            ];
        }

        // Step 3: Check today's total coins and apply daily cap
        $todaysTotalCoins = $this->getTodaysTotalCoins($userId);
        $remainingCapacity = self::DAILY_COIN_CAP - $todaysTotalCoins;

        if ($remainingCapacity <= 0) {
            return [
                'coins_awarded' => 0,
                'total_today' => $todaysTotalCoins,
                'message' => 'You have reached your daily coin limit of ' . self::DAILY_COIN_CAP . '!',
            ];
        }

        // Cap coins to not exceed daily limit
        $finalCoins = min($eligibleCoins, $remainingCapacity);

        // Step 4: Create transaction and update wallet (with duplicate prevention)
        try {
            $transaction = CoinTransaction::create([
                'user_id' => $userId,
                'subject_id' => $attempt->pastPaper->subject_id,
                'paper_id' => $paperId,
                'attempt_id' => $attempt->id,
                'coins' => $finalCoins,
                'mode' => $mode,
                'earned_on' => $today,
                'reason' => $this->generateReason($score, $mode),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // Duplicate award attempt on same paper same day - return 0 coins
            return [
                'coins_awarded' => 0,
                'total_today' => $this->getTodaysTotalCoins($userId),
                'message' => 'Coins already awarded for this paper today.',
            ];
        }

        // Update or create wallet
        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $userId],
            ['total_coins' => 0]
        );
        $wallet->increment('total_coins', $finalCoins);

        return [
            'coins_awarded' => $finalCoins,
            'total_today' => $todaysTotalCoins + $finalCoins,
            'message' => "You earned {$finalCoins} coin" . ($finalCoins !== 1 ? 's' : '') . " today!",
        ];
    }

    /**
     * Calculate eligible coins based on score and mode
     */
    private function calculateEligibleCoins(float $score, string $mode): int
    {
        // Score below 80 => no coins
        if ($score < 80) {
            return 0;
        }

        // Ultra easy always gives 0
        if ($mode === 'ultra_easy') {
            return 0;
        }

        // Perfect score (100)
        if ($score == 100) {
            return match ($mode) {
                'normal' => self::COINS_NORMAL_THRESHOLD_100,
                'ultra_medium' => self::COINS_ULTRA_MEDIUM_THRESHOLD_100,
                'ultra_hard' => self::COINS_ULTRA_HARD_THRESHOLD_100,
                default => 0,
            };
        }

        // Score 80-99
        if ($score >= 80 && $score < 100) {
            return match ($mode) {
                'normal' => self::COINS_NORMAL_THRESHOLD_80_99,
                'ultra_medium' => self::COINS_ULTRA_MEDIUM_THRESHOLD_80_99,
                'ultra_hard' => self::COINS_ULTRA_HARD_THRESHOLD_80_99,
                default => 0,
            };
        }

        return 0;
    }

    /**
     * Get today's total coins earned by user
     */
    private function getTodaysTotalCoins(int $userId): int
    {
        return CoinTransaction::where('user_id', $userId)
            ->whereDate('earned_on', now()->toDateString())
            ->sum('coins');
    }

    /**
     * Generate reason string for transaction
     */
    private function generateReason(float $score, string $mode): string
    {
        $modeLabel = match ($mode) {
            'normal' => 'Normal Mode',
            'ultra_easy' => 'Ultra Easy Mode',
            'ultra_medium' => 'Ultra Medium Mode',
            'ultra_hard' => 'Ultra Hard Mode',
            default => 'Unknown Mode',
        };

        if ($score == 100) {
            return "Perfect score ({$score}%) in {$modeLabel}";
        }

        return "Score {$score}% in {$modeLabel}";
    }
}
