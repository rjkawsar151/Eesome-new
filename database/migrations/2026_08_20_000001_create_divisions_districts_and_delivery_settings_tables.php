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
        if (! Schema::hasTable('divisions')) {
            Schema::create('divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('status')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
                $table->string('name');
                $table->decimal('delivery_charge', 10, 2)->default(130.00);
                $table->boolean('status')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('delivery_settings')) {
            Schema::create('delivery_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('free_delivery_enabled')->default(true);
                $table->decimal('free_delivery_threshold', 10, 2)->default(2000.00);
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'division')) {
                $table->string('division', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('orders', 'division_id')) {
                $table->foreignId('division_id')->nullable()->after('division');
            }
            if (! Schema::hasColumn('orders', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('district');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('orders', 'district_id')) $columnsToDrop[] = 'district_id';
            if (Schema::hasColumn('orders', 'division_id')) $columnsToDrop[] = 'division_id';
            if (Schema::hasColumn('orders', 'division')) $columnsToDrop[] = 'division';
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::dropIfExists('delivery_settings');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
