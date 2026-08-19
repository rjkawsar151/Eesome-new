<?php

namespace App\Jobs;

use App\Models\PageView;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PruneOldPageViewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of days of history to retain (default: 60).
     */
    public int $retentionDays;

    /**
     * Create a new job instance.
     */
    public function __construct(int $retentionDays = 60)
    {
        $this->retentionDays = $retentionDays;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->retentionDays);
        $totalDeleted = 0;

        // Process in chunks (FIFO: oldest entries deleted first) to prevent table locks
        do {
            $ids = PageView::where('created_at', '<=', $cutoffDate)
                ->orderBy('id', 'asc')
                ->limit(1000)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deleted = PageView::whereIn('id', $ids)->delete();
            $totalDeleted += $deleted;
        } while ($ids->count() === 1000);

        if ($totalDeleted > 0) {
            Log::info("Optimized visitor database: pruned {$totalDeleted} pageview record(s) older than {$this->retentionDays} days.");
        }
    }
}
