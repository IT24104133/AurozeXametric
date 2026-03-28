<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE past_papers MODIFY total_questions INT NULL DEFAULT 40");
            DB::statement("ALTER TABLE past_papers MODIFY count_s INT NULL DEFAULT 12");
            DB::statement("ALTER TABLE past_papers MODIFY count_m INT NULL DEFAULT 18");
            DB::statement("ALTER TABLE past_papers MODIFY count_h INT NULL DEFAULT 10");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE past_papers MODIFY total_questions INT NULL DEFAULT NULL");
            DB::statement("ALTER TABLE past_papers MODIFY count_s INT NULL DEFAULT NULL");
            DB::statement("ALTER TABLE past_papers MODIFY count_m INT NULL DEFAULT NULL");
            DB::statement("ALTER TABLE past_papers MODIFY count_h INT NULL DEFAULT NULL");
        }
    }
};
