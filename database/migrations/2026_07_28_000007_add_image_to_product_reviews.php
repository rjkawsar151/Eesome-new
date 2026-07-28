<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_reviews') && ! Schema::hasColumn('product_reviews', 'image_path')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                $table->string('image_path', 500)->nullable()->after('review_text');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_reviews') && Schema::hasColumn('product_reviews', 'image_path')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }
    }
};