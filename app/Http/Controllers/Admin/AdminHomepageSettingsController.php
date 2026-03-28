<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageSettingsRequest;
use App\Models\HomepageSetting;
use App\Models\PastPaper;
use App\Models\StudentWallet;
use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminHomepageSettingsController extends Controller
{
    public function index()
    {
        $homepageSetting = $this->getOrCreateSettings();
        $draft = $homepageSetting->draft_json ?? [];

        $formData = array_merge($this->getDefaults(), [
            'hero_title' => $homepageSetting->hero_title,
            'hero_subtitle' => $homepageSetting->hero_subtitle,
            'hero_button_text' => $homepageSetting->hero_button_text,
            'hero_button_link' => $homepageSetting->hero_button_link,
            'hero_image_path' => $homepageSetting->hero_image_path,
            'show_platform_stats' => $homepageSetting->show_platform_stats,
            'show_growth_stats_section' => $homepageSetting->show_growth_stats_section,
            'show_growth_chart' => $homepageSetting->show_growth_chart,
            'growth_section_title' => $homepageSetting->growth_section_title,
            'growth_section_subtitle' => $homepageSetting->growth_section_subtitle,
            'stats_section_title' => $homepageSetting->stats_section_title,
            'stats_section_subtitle' => $homepageSetting->stats_section_subtitle,
            'stats_cards' => $homepageSetting->stats_cards ?? $this->defaultStatsCards(),
            'show_leaderboard' => $homepageSetting->show_leaderboard,
            'show_features' => $homepageSetting->show_features,
            'show_testimonials' => $homepageSetting->show_testimonials,
            'show_growth_widget' => $homepageSetting->show_growth_widget,
            'growth_start_date' => optional($homepageSetting->growth_start_date)->format('Y-m-d'),
            'growth_end_date' => optional($homepageSetting->growth_end_date)->format('Y-m-d'),
        ]);

        if (!empty($draft)) {
            $formData = array_merge($formData, $draft);
        }

        return view('admin.homepage_settings.index', [
            'homepageSetting' => $homepageSetting,
            'formData' => $formData,
            'hasDraft' => !empty($draft),
        ]);
    }

    public function saveDraft(HomepageSettingsRequest $request)
    {
        $homepageSetting = $this->getOrCreateSettings();
        $payload = $this->buildPayload($request, $homepageSetting);

        $homepageSetting->draft_json = $payload;
        $homepageSetting->updated_by = $request->user()->id;
        $homepageSetting->save();

        return redirect()
            ->route('admin.homepage.settings')
            ->with('success', 'Draft saved successfully. Preview before publishing.');
    }

    public function publish(HomepageSettingsRequest $request)
    {
        $homepageSetting = $this->getOrCreateSettings();

        $payload = $homepageSetting->draft_json;
        if (empty($payload)) {
            $payload = $this->buildPayload($request, $homepageSetting);
        }

        $homepageSetting->fill($this->filterPublishable($payload));
        $homepageSetting->is_published = true;
        $homepageSetting->draft_json = null;
        $homepageSetting->updated_by = $request->user()->id;
        $homepageSetting->save();

        return redirect()
            ->route('admin.homepage.settings')
            ->with('success', 'Homepage published successfully.');
    }

    public function preview()
    {
        $homepageSetting = $this->getOrCreateSettings();
        $draft = $homepageSetting->draft_json ?? [];

        $settings = array_merge($this->getDefaults(), [
            'hero_title' => $homepageSetting->hero_title,
            'hero_subtitle' => $homepageSetting->hero_subtitle,
            'hero_button_text' => $homepageSetting->hero_button_text,
            'hero_button_link' => $homepageSetting->hero_button_link,
            'hero_image_path' => $homepageSetting->hero_image_path,
            'show_platform_stats' => $homepageSetting->show_platform_stats,
            'show_growth_stats_section' => $homepageSetting->show_growth_stats_section,
            'show_growth_chart' => $homepageSetting->show_growth_chart,
            'growth_section_title' => $homepageSetting->growth_section_title,
            'growth_section_subtitle' => $homepageSetting->growth_section_subtitle,
            'stats_section_title' => $homepageSetting->stats_section_title,
            'stats_section_subtitle' => $homepageSetting->stats_section_subtitle,
            'stats_cards' => $homepageSetting->stats_cards ?? $this->defaultStatsCards(),
            'show_leaderboard' => $homepageSetting->show_leaderboard,
            'show_features' => $homepageSetting->show_features,
            'show_testimonials' => $homepageSetting->show_testimonials,
            'show_growth_widget' => $homepageSetting->show_growth_widget,
            'growth_start_date' => optional($homepageSetting->growth_start_date)->format('Y-m-d'),
            'growth_end_date' => optional($homepageSetting->growth_end_date)->format('Y-m-d'),
        ]);

        if (!empty($draft)) {
            $settings = array_merge($settings, $draft);
        }

        return view('admin.homepage_preview', array_merge(
            $this->buildHomepageViewData($settings, true),
            ['isPreview' => true]
        ));
    }

    private function getOrCreateSettings(): HomepageSetting
    {
        return HomepageSetting::query()->firstOrCreate([], $this->getDefaults());
    }

    private function getDefaults(): array
    {
        return [
            'hero_title' => 'Practice smarter with ExamPortal',
            'hero_subtitle' => 'Ace your exams with real-time practice, feedback, and past paper mastery.',
            'hero_button_text' => 'Start Practicing',
            'hero_button_link' => '/login',
            'hero_image_path' => null,
            'show_platform_stats' => true,
            'show_growth_stats_section' => true,
            'show_growth_chart' => true,
            'growth_section_title' => 'Growth & Statistics',
            'growth_section_subtitle' => null,
            'stats_section_title' => 'By The Numbers',
            'stats_section_subtitle' => null,
            'stats_cards' => $this->defaultStatsCards(),
            'show_leaderboard' => true,
            'show_features' => true,
            'show_testimonials' => false,
            'show_growth_widget' => true,
            'growth_start_date' => null,
            'growth_end_date' => null,
            'is_published' => true,
        ];
    }

    private function buildPayload(HomepageSettingsRequest $request, HomepageSetting $homepageSetting): array
    {
        $data = $request->validated();
        $data = Arr::except($data, ['hero_image']);

        $imagePath = $homepageSetting->hero_image_path;
        $currentDraft = $homepageSetting->draft_json ?? [];
        if (!empty($currentDraft['hero_image_path'])) {
            $imagePath = $currentDraft['hero_image_path'];
        }

        if ($request->hasFile('hero_image')) {
            $imagePath = $request->file('hero_image')->store('homepage', 'public');
        }

        $timezone = config('app.timezone');
        $startDate = $request->input('growth_start_date');
        $endDate = $request->input('growth_end_date');

        $data['growth_start_date'] = $startDate
            ? Carbon::parse($startDate, $timezone)->format('Y-m-d')
            : null;
        $data['growth_end_date'] = $endDate
            ? Carbon::parse($endDate, $timezone)->format('Y-m-d')
            : null;

        $data['hero_image_path'] = $imagePath;

        $data['stats_cards'] = $this->normalizeStatsCards($request->input('stats_cards', []));

        return $data;
    }

    private function filterPublishable(array $payload): array
    {
        return Arr::only($payload, [
            'hero_title',
            'hero_subtitle',
            'hero_button_text',
            'hero_button_link',
            'hero_image_path',
            'show_platform_stats',
            'show_growth_stats_section',
            'show_growth_chart',
            'growth_section_title',
            'growth_section_subtitle',
            'stats_section_title',
            'stats_section_subtitle',
            'stats_cards',
            'show_leaderboard',
            'show_features',
            'show_testimonials',
            'show_growth_widget',
            'growth_start_date',
            'growth_end_date',
        ]);
    }

    private function buildHomepageViewData(array $settings, bool $isPreview = false): array
    {
        $showGrowthStatsSection = !empty($settings['show_growth_stats_section']);
        $showGrowthChart = $showGrowthStatsSection && !empty($settings['show_growth_chart']);
        $showPlatformStats = $showGrowthStatsSection && !empty($settings['show_platform_stats']);

        $stats = null;
        if ($showPlatformStats) {
            $stats = Cache::remember('homepage_stats', 300, function () {
                return [
                    'students' => User::where('role', 'student')->count(),
                    'teachers' => User::where('role', 'teacher')->count(),
                    'exams' => Exam::count(),
                    'past_papers' => PastPaper::where('category', '!=', 'free_style')->count(),
                ];
            });
        }

        $topLeaderboard = [];
        if (!empty($settings['show_leaderboard'])) {
            $topLeaderboard = StudentWallet::query()
                ->join('users', 'student_wallets.user_id', '=', 'users.id')
                ->where('users.role', '!=', 'admin')
                ->select('users.id as user_id', 'users.name', 'users.full_name', 'student_wallets.total_coins')
                ->orderByDesc('student_wallets.total_coins')
                ->take(10)
                ->get()
                ->toArray();
        }

        $growthData = $showGrowthChart
            ? $this->calculateGrowthData(
                $settings['growth_start_date'] ?? null,
                $settings['growth_end_date'] ?? null
            )
            : [
                'labels' => [],
                'values' => [],
                'startDate' => now(),
                'endDate' => now(),
                'total' => 0,
                'max' => 1,
            ];

        $statsCards = $this->buildStatsCards($settings, $stats ?? []);

        return [
            'homepageSettings' => $settings,
            'stats' => $stats,
            'statsCards' => $statsCards,
            'topLeaderboard' => $topLeaderboard,
            'growthLabels' => $growthData['labels'],
            'growthValues' => $growthData['values'],
            'growthStartDate' => $growthData['startDate'],
            'growthEndDate' => $growthData['endDate'],
            'totalNewUsers' => $growthData['total'],
            'maxGrowthValue' => $growthData['max'],
            'growthChartId' => $isPreview ? 'dailyGrowthChartPreview' : 'dailyGrowthChart',
            'isPreview' => $isPreview,
        ];
    }

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

    private function normalizeStatsCards(array $cards): array
    {
        $normalized = [];
        foreach ($cards as $card) {
            $normalized[] = [
                'key' => $card['key'] ?? '',
                'label' => $card['label'] ?? '',
                'description' => $card['description'] ?? null,
                'icon' => $card['icon'] ?? 'circle',
                'enabled' => filter_var($card['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'order' => (int) ($card['order'] ?? 0),
            ];
        }

        return $normalized;
    }

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
}
