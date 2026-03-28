<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('popup_enabled')->default(false)->after('selection_mode');
            $table->string('popup_title')->nullable()->after('popup_enabled');
            $table->text('popup_message')->nullable()->after('popup_title');
            $table->string('popup_link')->nullable()->after('popup_message');
            $table->boolean('popup_show_copy')->default(false)->after('popup_link');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'popup_enabled',
                'popup_title',
                'popup_message',
                'popup_link',
                'popup_show_copy',
            ]);
        });
    }
};
