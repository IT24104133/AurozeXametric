<?php

namespace App\Http\Traits;

use App\Models\Exam;
use App\Models\PastPaper;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait BuildsGrowthStatsData
{
    /**
     * Build growth stats data (chart + cards) for homepage or dashboard
     * Returns: growthLabels, growthValues, statsCards, growthStartDate, growthEndDate, totalNewUsers, maxGrowthValue
     */
    protected function buildGrowthStatsData(?string $startDate, ?string $endDate, array $settings = []): array
    {
        // Calculate growth chart data
        $growthData = $this->calculateGrowthData($startDate, $endDate);

        // Get stats counts
        $stats = Cache::remember('homepage_stats', 300, function () {
            return [
                'students' => User::where('role', 'student')->count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'exams' => Exam::count(),
                'past_papers' => PastPaper::where('category', '!=', 'free_style')->count(),
            ];
        });

        // Build stats cards
        $statsCards = $this->buildStatsCards($settings, $stats);

        return [
            'growthLabels' => $growthData['labels'],
            'growthValues' => $growthData['values'],
            'statsCards' => $statsCards,
            'growthStartDate' => $growthData['startDate'],
            'growthEndDate' => $growthData['endDate'],
            'totalNewUsers' => $growthData['total'],
            'maxGrowthValue' => $growthData['max'],
        ];
    }

    /**
     * Calculate daily growth data for the specified date range
     */
    private function calculateGrowthData(?string $startDate, ?string $endDate): array
    {
        $timezone = config('app.timezone');

        $end = $endDate
            ? Carbon::parse($endDate, $timezone)
            : now($timezone);
        $start = $startDate
            ? Carbon::parse($startDate, $timezone)
            : $end->copy()->subDays(13);

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->subDays(13), $end];
        }

        $queryStart = $start->copy()->startOfDay();
        $queryEnd = $end->copy()->endOfDay();

        $raw = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$queryStart, $queryEnd])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $period = CarbonPeriod::create($start, $end);
        $labels = [];
        $values = [];
        $total = 0;
        foreach ($period as $day) {
            $dateKey = $day->format('Y-m-d');
            $count = (int) ($raw[$dateKey]->count ?? 0);
            $labels[] = $day->format('M d');
            $values[] = $count;
            $total += $count;
        }

        return [
            'startDate' => $start,
            'endDate' => $end,
            'labels' => $labels,
            'values' => $values,
            'total' => $total,
            'max' => max(1, max($values ?: [0])),
        ];
    }

    /**
     * Build stats cards from settings and stat values
     */
    private function buildStatsCards(array $settings, array $stats): array
    {
        $cards = $settings['stats_cards'] ?? $this->defaultStatsCards();

        $values = [
            'students' => $stats['students'] ?? 0,
            'teachers' => $stats['teachers'] ?? 0,
            'exams' => $stats['exams'] ?? 0,
            'past_papers' => $stats['past_papers'] ?? 0,
            'freestyle' => '∞',
        ];

        $enabledCards = array_filter($cards, fn ($card) => !empty($card['enabled']));
        usort($enabledCards, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return array_map(function ($card) use ($values) {
            $key = $card['key'] ?? '';
            $card['value'] = $values[$key] ?? 0;
            return $card;
        }, $enabledCards);
    }

    /**
     * Get default stats cards configuration
     */
    private function defaultStatsCards(): array
    {
        return [
            ['key' => 'students', 'label' => 'Students', 'description' => 'Registered learners', 'icon' => 'graduation-cap', 'enabled' => true, 'order' => 1],
            ['key' => 'teachers', 'label' => 'Teachers', 'description' => 'Expert educators', 'icon' => 'user', 'enabled' => false, 'order' => 2],
            ['key' => 'exams', 'label' => 'Exams', 'description' => 'Available assessments', 'icon' => 'file-text', 'enabled' => true, 'order' => 3],
            ['key' => 'past_papers', 'label' => 'Past Papers', 'description' => 'Practice materials', 'icon' => 'book-open', 'enabled' => true, 'order' => 4],
            ['key' => 'freestyle', 'label' => 'Free Style Papers', 'description' => 'Unlimited practice', 'icon' => 'infinity', 'enabled' => true, 'order' => 5],
        ];
    }
}
