<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('customer','user','admin','manager','content editor','super admin') NOT NULL DEFAULT 'customer'");
        }

        if (! Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('admin_id')->nullable()->index();
                $table->string('action', 120);
                $table->string('subject_type')->nullable();
                $table->string('subject_id')->nullable();
                $table->text('description')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
