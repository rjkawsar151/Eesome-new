<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 30);
            $table->string('label');
            $table->string('url', 1000);
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['location', 'is_active', 'sort_order']);
        });
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 30)->default('public');
            $table->string('path', 1000)->unique();
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('alt_text')->nullable();
            $table->foreignId('uploaded_by')->nullable();
            $table->timestamps();
        });
        DB::table('navigation_items')->insert([
            ['location' => 'header', 'label' => 'Shop', 'url' => '/products', 'sort_order' => 10, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'header', 'label' => 'About', 'url' => '/about', 'sort_order' => 20, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'footer', 'label' => 'All Products', 'url' => '/products', 'sort_order' => 10, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'footer', 'label' => 'Blog', 'url' => '/blog', 'sort_order' => 20, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('navigation_items');
    }
};
