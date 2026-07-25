<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_images')) {
            Schema::table('product_images', function (Blueprint $table) {
                if (! Schema::hasColumn('product_images', 'alt_text')) {
                    $table->string('alt_text')->nullable();
                }

                if (! Schema::hasColumn('product_images', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }

                if (! Schema::hasColumn('product_images', 'is_primary')) {
                    $table->tinyInteger('is_primary')->default(0);
                }

                if (! Schema::hasColumn('product_images', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (! Schema::hasColumn('product_images', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (! Schema::hasColumn('testimonials', 'is_active')) {
                    $table->tinyInteger('is_active')->default(1);
                }

                if (! Schema::hasColumn('testimonials', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        // Compatibility columns are intentionally preserved.
    }
};
