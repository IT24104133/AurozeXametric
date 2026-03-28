<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('contact_number')->nullable()->after('last_name');

            $table->string('school_name')->nullable()->after('contact_number'); // student
            $table->string('nic_number')->nullable()->after('school_name');    // teacher

            $table->timestamp('profile_completed_at')->nullable()->after('password_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'contact_number',
                'school_name',
                'nic_number',
                'profile_completed_at',
            ]);
        });
    }
};
