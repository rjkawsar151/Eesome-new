<?php

namespace App\Console\Commands;

use App\Jobs\PruneOldPageViewsJob;
use App\Models\PageView;
use Illuminate\Console\Command;

class PrunePageViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pageviews:prune {--days=60 : Maximum number of days of data to retain} {--queue : Dispatch the pruning task to the queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune visitor and page view records older than the retention period (default 60 days) using FIFO deletion';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        if ($days <= 0) {
            $days = 60;
        }

        if ($this->option('queue')) {
            PruneOldPageViewsJob::dispatch($days);
            $this->info("Dispatched queued job to prune visitor page views older than {$days} days.");
            return self::SUCCESS;
        }

        $cutoffDate = now()->subDays($days);
        $this->info("Scanning page views older than {$days} days ({$cutoffDate->toDateTimeString()})...");

        $job = new PruneOldPageViewsJob($days);
        $job->handle();

        $remainingCount = PageView::count();
        $this->info("Page views database optimized. Current total records: {$remainingCount}.");

        return self::SUCCESS;
    }
}
