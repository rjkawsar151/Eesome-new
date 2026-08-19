<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_views')) {
            Schema::table('page_views', function (Blueprint $table) {
                // Add index on created_at for fast 60-day cleanup and date filtering
                $table->index('created_at', 'page_views_created_at_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('page_views')) {
            Schema::table('page_views', function (Blueprint $table) {
                $table->dropIndex('page_views_created_at_index');
            });
        }
    }
};
