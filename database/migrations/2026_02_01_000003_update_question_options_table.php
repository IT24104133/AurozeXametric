<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            if (!Schema::hasColumn('question_options', 'position')) {
                $table->unsignedSmallInteger('position')->default(1)->after('question_id');
            }
            if (!Schema::hasColumn('question_options', 'option_key')) {
                $table->string('option_key', 1)->nullable()->after('position'); // A..E
            }
            if (!Schema::hasColumn('question_options', 'option_image')) {
                $table->string('option_image')->nullable()->after('option_text');
            }
        });
    }

    public function down(): void
    {
        // optional
    }
};
