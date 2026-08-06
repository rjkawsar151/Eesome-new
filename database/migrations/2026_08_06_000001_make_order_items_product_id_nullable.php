<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if (Schema::hasTable('order_items') && Schema::hasColumn('order_items','product_id')) Schema::table('order_items', fn(Blueprint $table) => $table->integer('product_id')->nullable()->change()); }
 public function down(): void { if (Schema::hasTable('order_items') && Schema::hasColumn('order_items','product_id')) Schema::table('order_items', fn(Blueprint $table) => $table->integer('product_id')->nullable(false)->change()); }
};
