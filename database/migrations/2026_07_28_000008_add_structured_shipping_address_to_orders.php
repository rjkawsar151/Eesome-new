<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        foreach (['district' => 255, 'thana' => 255, 'post_office' => 255, 'post_code' => 20] as $column => $length) {
            if (! Schema::hasColumn('orders', $column)) {
                Schema::table('orders', fn (Blueprint $table) => $table->string($column, $length)->nullable());
            }
        }
    }
    public function down(): void {
        $columns = array_values(array_filter(['district','thana','post_office','post_code'], fn ($column) => Schema::hasColumn('orders', $column)));
        if ($columns) Schema::table('orders', fn (Blueprint $table) => $table->dropColumn($columns));
    }
};