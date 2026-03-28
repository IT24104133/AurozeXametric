<?php

namespace App\Http\Controllers;

use App\Models\HomepageSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Display the public welcome page
     */
    public function index(): View
    {
        $homepageSetting = HomepageSetting::query()->first();

        if (!$homepageSetting) {
            $homepageSetting = HomepageSetting::create([
                'hero_title' => 'Practice smarter with ExamPortal',
                'hero_subtitle' => 'Ace your exams with real-time practice, feedback, and past paper mastery.',
                'hero_button_text' => 'Start Practicing',
                'hero_button_link' => '/login',
                'is_published' => true,
            ]);
        }

        $settings = [
            'hero_title' => $homepageSetting->hero_title,
            'hero_subtitle' => $homepageSetting->hero_subtitle,
            'hero_button_text' => $homepageSetting->hero_button_text,
            'hero_button_link' => $homepageSetting->hero_button_link,
            'hero_image_path' => $homepageSetting->hero_image_path,
            'show_leaderboard' => $homepageSetting->show_leaderboard ?? true,
        ];

        // Fetch top 10 students for public leaderboard
        $topLeaderboard = User::select(
            'users.id as user_id',
            'users.name',
            'users.full_name',
            DB::raw('COALESCE(student_wallets.total_coins, 0) as total_coins')
        )
            ->leftJoin('student_wallets', 'users.id', '=', 'student_wallets.user_id')
            ->where('users.role', 'student')
            ->orderByDesc('total_coins')
            ->orderBy('users.name')
            ->limit(10)
            ->get()
            ->toArray();

        return view('welcome', [
            'homepageSettings' => $settings,
            'topLeaderboard' => $topLeaderboard,
            'isPreview' => false,
        ]);
    }
}