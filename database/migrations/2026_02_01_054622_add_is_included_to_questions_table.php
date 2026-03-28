<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {

            // ✅ new column for manual include/exclude
            if (!Schema::hasColumn('questions', 'is_included')) {
                $table->boolean('is_included')->default(true)->after('question_text');
            }

            // ✅ make old columns nullable (so DB won't crash)
            if (Schema::hasColumn('questions', 'option_a')) $table->string('option_a')->nullable()->change();
            if (Schema::hasColumn('questions', 'option_b')) $table->string('option_b')->nullable()->change();
            if (Schema::hasColumn('questions', 'option_c')) $table->string('option_c')->nullable()->change();
            if (Schema::hasColumn('questions', 'option_d')) $table->string('option_d')->nullable()->change();
            if (Schema::hasColumn('questions', 'correct_option')) $table->string('correct_option')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'is_included')) {
                $table->dropColumn('is_included');
            }
        });
    }
};
