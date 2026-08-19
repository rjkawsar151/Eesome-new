<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Normalize any existing Enum values to lowercase strings first
            DB::statement("UPDATE orders SET payment_status = LOWER(payment_status)");

            // Change the column from ENUM('Pending','Paid') to VARCHAR(50)
            // supporting: pending, unpaid, paid, failed, refunded, partially_refunded
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Best-effort revert — values not in the enum will be truncated
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('Pending','Paid') DEFAULT 'Pending'");
        }
    }
};
