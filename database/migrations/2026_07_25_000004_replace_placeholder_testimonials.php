<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('testimonials')) {
            return;
        }

        DB::table('testimonials')->where('id', 3)->update([
            'content' => 'The bag feels beautifully made, with neat stitching and a premium finish. It arrived carefully packaged and looked even better in person.',
            'sort_order' => 1,
        ]);

        DB::table('testimonials')->where('id', 4)->update([
            'content' => 'My order arrived quickly and the colour matched the photos perfectly. The team was helpful, responsive, and thoughtful throughout.',
            'sort_order' => 2,
        ]);

        DB::table('testimonials')->updateOrInsert(
            ['name' => 'NUSRAT J.'],
            [
                'content' => 'The material is soft yet structured, and the hardware has a lovely polished finish. I have already received so many compliments.',
                'image' => null,
                'rating' => 5,
                'is_active' => 1,
                'sort_order' => 3,
            ]
        );
    }

    public function down(): void
    {
        // Customer-facing content changes are intentionally preserved.
    }
};
