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
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('product_id');
                $table->integer('user_id')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('email')->nullable();
                $table->integer('rating');
                $table->text('review_text');
                $table->string('status', 20)->default('Pending');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
