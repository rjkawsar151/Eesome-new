<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('charge_type')->default('flat');
            $table->decimal('base_charge', 12, 2)->default(0);
            $table->decimal('minimum_order_amount', 12, 2)->nullable();
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();
            $table->unsignedSmallInteger('estimated_delivery_days')->nullable();
            $table->boolean('cod_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('requires_transaction_id')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('shipping_address');
            }
        });

        DB::table('shipping_methods')->insert([
            'name' => 'Standard delivery',
            'code' => 'standard_delivery',
            'description' => 'Reliable doorstep delivery.',
            'charge_type' => 'flat',
            'base_charge' => 80,
            'free_shipping_threshold' => 999,
            'estimated_delivery_days' => 5,
            'cod_available' => true,
            'is_active' => true,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payment_methods')->insert([
            'name' => 'Cash on delivery', 'code' => 'COD', 'requires_transaction_id' => false,
            'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('payment_methods')->insert([
            'name' => 'bKash', 'code' => 'bKash',
            'instructions' => 'Complete payment using the account details supplied by the store.',
            'requires_transaction_id' => true, 'is_active' => true, 'sort_order' => 20,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('shipping_method'));
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('shipping_methods');
    }
};
