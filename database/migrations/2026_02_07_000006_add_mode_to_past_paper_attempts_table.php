<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('past_paper_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('past_paper_attempts', 'mode')) {
                $table->string('mode')->default('normal')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_paper_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('past_paper_attempts', 'mode')) {
                $table->dropColumn('mode');
            }
        });
    }
};
