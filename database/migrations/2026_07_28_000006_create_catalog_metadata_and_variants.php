<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id');
        });
        Schema::create('product_tag', function (Blueprint $table) {
            $table->foreignId('product_id');
            $table->foreignId('tag_id');
            $table->primary(['product_id', 'tag_id']);
        });
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('material')->nullable();
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_tag');
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('brand_id'));
        Schema::dropIfExists('tags');
        Schema::dropIfExists('brands');
    }
};
