<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['shipping_provider', 'tracking_number', 'tracking_url'] as $column) {
                if (! Schema::hasColumn('orders', $column)) {
                    $table->string($column, 500)->nullable();
                }
            }
            foreach (['shipped_at', 'estimated_delivery_at', 'delivered_at'] as $column) {
                if (! Schema::hasColumn('orders', $column)) {
                    $table->timestamp($column)->nullable();
                }
            }
        });

        foreach (['Pending' => 'awaiting', 'Confirmed' => 'processing', 'Processing' => 'processing', 'Shipped' => 'shipped', 'In Transit' => 'in_transit', 'Delivered' => 'delivered', 'Cancelled' => 'cancelled'] as $old => $new) {
            DB::table('orders')->where('order_status', $old)->update(['order_status' => $new]);
            DB::table('order_status_histories')->where('from_status', $old)->update(['from_status' => $new]);
            DB::table('order_status_histories')->where('to_status', $old)->update(['to_status' => $new]);
        }
        DB::table('orders')->where('payment_status', 'Pending')->update(['payment_status' => 'pending']);
        DB::table('orders')->where('payment_status', 'Paid')->update(['payment_status' => 'paid']);
    }

    public function down(): void {}
};
