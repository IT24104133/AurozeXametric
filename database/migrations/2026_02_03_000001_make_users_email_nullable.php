<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Most projects here use MySQL (XAMPP)
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY email VARCHAR(255) NULL");
        } else {
            // If not mysql, you can handle later (sqlite needs table rebuild)
            // but in XAMPP you are mysql, so this is enough.
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL");
        }
    }
};
