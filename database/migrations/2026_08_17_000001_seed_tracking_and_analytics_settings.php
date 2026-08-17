<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key', 255)->unique();
                $table->text('setting_value')->nullable();
                $table->timestamps();
            });
        }

        $now = now();
        $trackingSettings = [
            'google_gtm_id' => 'GTM-5FK7CHXW',
            'meta_pixel_id' => '1598500547854347',
            'meta_capi_token' => 'EAA1XmKnqNoQBSAVZA3fC3nkxQ31GdMlXZALxxgLGi1eSqZARoR3SrD7oS0TnvmIQej6q4xkNTeascZCKHONCsdCsyzuDuGLzBDzIZBY0TAeUB7e5kZCPh0fxoJ23GgonjZCEbrRbu337N8RnMIZBnKWBImdYswHb6sDvmqgj5TePg5loHgrhRjFe6Te4fee4yfKYkQZDZD',
            'meta_test_event_code' => '',
            'google_analytics_id' => '',
            'google_tag_id' => '',
            'google_ads_id' => '',
            'google_ads_conversion_id' => '',
            'google_ads_conversion_label' => '',
            'custom_header_scripts' => '',
            'custom_footer_scripts' => '',
        ];

        foreach ($trackingSettings as $key => $defaultValue) {
            $existing = DB::table('site_settings')->where('setting_key', $key)->first();
            if (! $existing) {
                DB::table('site_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $defaultValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } elseif (empty($existing->setting_value) && ! empty($defaultValue)) {
                DB::table('site_settings')->where('setting_key', $key)->update([
                    'setting_value' => $defaultValue,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep settings safe on rollback
    }
};
