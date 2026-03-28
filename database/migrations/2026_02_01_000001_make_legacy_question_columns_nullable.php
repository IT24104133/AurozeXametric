<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // make old columns nullable so new system can insert without them
            if (Schema::hasColumn('questions', 'option_a')) $table->string('option_a', 255)->nullable()->change();
            if (Schema::hasColumn('questions', 'option_b')) $table->string('option_b', 255)->nullable()->change();
            if (Schema::hasColumn('questions', 'option_c')) $table->string('option_c', 255)->nullable()->change();
            if (Schema::hasColumn('questions', 'option_d')) $table->string('option_d', 255)->nullable()->change();
            if (Schema::hasColumn('questions', 'correct_option')) $table->string('correct_option', 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        // no down (safe)
    }
};
