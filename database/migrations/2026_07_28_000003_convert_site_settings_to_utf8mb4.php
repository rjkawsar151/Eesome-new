<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE site_settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: converting back to latin1 could corrupt Unicode settings.
    }
};