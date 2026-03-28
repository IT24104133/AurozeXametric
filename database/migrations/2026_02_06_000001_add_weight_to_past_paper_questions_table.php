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
        Schema::table('past_paper_questions', function (Blueprint $table) {
            $table->string('weight', 1)->default('M')->after('explanation');
            $table->index(['past_paper_id', 'weight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_paper_questions', function (Blueprint $table) {
            $table->dropIndex(['past_paper_id', 'weight']);
            $table->dropColumn('weight');
        });
    }
};
