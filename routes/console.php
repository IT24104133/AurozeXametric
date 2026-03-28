<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pastpapers:reset', function () {
    $confirmation = strtolower(trim($this->ask('This will permanently delete all past paper data. Continue? (yes/no)')));

    if ($confirmation !== 'yes') {
        $this->info('Cancelled. No data was deleted.');
        return 0;
    }

    $tables = [
        'past_paper_attempt_answers',
        'past_paper_attempts',
        'past_paper_options',
        'past_paper_questions',
        'past_papers',
        'past_paper_subjects',
    ];

    $driver = DB::getDriverName();

    try {
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    } finally {
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    $this->info('Past paper data has been reset.');
})->purpose('Wipe all past paper module data');
