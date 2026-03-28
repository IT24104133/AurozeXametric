<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CoinsTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:test';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test coin award system with sample data (local/dev only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Safety check: only allow in local environment
        if (!app()->environment('local')) {
            $this->error('❌ This command can only be run in local environment!');
            $this->error('Current environment: ' . app()->environment());
            return 1;
        }

        $this->info('🪙 Starting Coin Test...');
        $this->newLine();

        try {
            // Run the seeder
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\CoinTestSeeder']);

            // Get output from seeder
            $seederOutput = Artisan::output();
            $this->line($seederOutput);

            $this->newLine();
            $this->info('✅ Coin test completed successfully!');
            $this->info('Test students created with email pattern: student{N}_coin@test.com');
            $this->info('Check student dashboard to see coins and leaderboard.');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during coin test:');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
