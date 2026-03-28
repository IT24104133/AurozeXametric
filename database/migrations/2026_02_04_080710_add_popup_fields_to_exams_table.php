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
    Schema::table('exams', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->boolean('custom_success_popup_enabled')->default(false);
        $table->string('custom_success_popup_title')->nullable();
        $table->text('custom_success_popup_message')->nullable();
        $table->string('custom_success_popup_link')->nullable();
        $table->boolean('custom_success_popup_show_copy')->default(true);
    });
}

public function down(): void
{
    Schema::table('exams', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn([
            'custom_success_popup_enabled',
            'custom_success_popup_title',
            'custom_success_popup_message',
            'custom_success_popup_link',
            'custom_success_popup_show_copy',
        ]);
    });
}
};
