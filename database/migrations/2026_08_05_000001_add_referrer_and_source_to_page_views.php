<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            if (! Schema::hasColumn('page_views', 'referrer')) {
                $table->string('referrer', 500)->nullable()->after('url');
            }
            if (! Schema::hasColumn('page_views', 'source')) {
                $table->string('source', 50)->nullable()->index()->after('referrer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            if (Schema::hasColumn('page_views', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('page_views', 'referrer')) {
                $table->dropColumn('referrer');
            }
        });
    }
};
