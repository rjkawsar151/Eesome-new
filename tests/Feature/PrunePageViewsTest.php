<?php

namespace Tests\Feature;

use App\Jobs\PruneOldPageViewsJob;
use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrunePageViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prunes_page_views_older_than_60_days_in_fifo_order(): void
    {
        // 1. Create a page view 70 days ago (should be pruned)
        $oldestId = \Illuminate\Support\Facades\DB::table('page_views')->insertGetId([
            'ip_address' => '1.1.1.1',
            'url'        => 'products/old-bag',
            'created_at' => now()->subDays(70)->toDateTimeString(),
            'updated_at' => now()->subDays(70)->toDateTimeString(),
        ]);

        // 2. Create a page view 61 days ago (should be pruned)
        $olderId = \Illuminate\Support\Facades\DB::table('page_views')->insertGetId([
            'ip_address' => '2.2.2.2',
            'url'        => 'products/vintage-bag',
            'created_at' => now()->subDays(61)->toDateTimeString(),
            'updated_at' => now()->subDays(61)->toDateTimeString(),
        ]);

        // 3. Create a page view 30 days ago (within 60 days: should be retained)
        $recentId = \Illuminate\Support\Facades\DB::table('page_views')->insertGetId([
            'ip_address' => '3.3.3.3',
            'url'        => 'products/new-tote',
            'created_at' => now()->subDays(30)->toDateTimeString(),
            'updated_at' => now()->subDays(30)->toDateTimeString(),
        ]);

        // 4. Create a page view today (should be retained)
        $todayId = \Illuminate\Support\Facades\DB::table('page_views')->insertGetId([
            'ip_address' => '4.4.4.4',
            'url'        => 'cart',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Run the pruning command
        $this->artisan('pageviews:prune --days=60')
            ->assertSuccessful();

        // Verify that only the recent records (within 60 days) remain
        $this->assertDatabaseMissing('page_views', ['id' => $oldestId]);
        $this->assertDatabaseMissing('page_views', ['id' => $olderId]);
        $this->assertDatabaseHas('page_views', ['id' => $recentId]);
        $this->assertDatabaseHas('page_views', ['id' => $todayId]);
    }

    public function test_job_prunes_in_batches(): void
    {
        for ($i = 0; $i < 5; $i++) {
            \Illuminate\Support\Facades\DB::table('page_views')->insert([
                'ip_address' => "10.0.0.{$i}",
                'url'        => 'test-url',
                'created_at' => now()->subDays(65)->toDateTimeString(),
                'updated_at' => now()->subDays(65)->toDateTimeString(),
            ]);
        }

        $freshId = \Illuminate\Support\Facades\DB::table('page_views')->insertGetId([
            'ip_address' => '10.0.0.99',
            'url'        => 'fresh-url',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        $job = new PruneOldPageViewsJob(60);
        $job->handle();

        $this->assertSame(1, PageView::count());
        $this->assertDatabaseHas('page_views', ['id' => $freshId]);
    }
}
