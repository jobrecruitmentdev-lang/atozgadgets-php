<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Jobs\SyncCategoryJob;

class DispatchCjSync extends Command
{
    protected $signature = 'cj:sync {--pages=2 : Pages to sync per category}';
    protected $description = 'Dispatches background jobs to sync CJ Dropshipping products securely and asynchronously.';

    public function handle()
    {
        $pages = $this->option('pages');
        $this->info("Dispatching CJ Sync Jobs ({$pages} pages per category)...");

        $categories = Category::whereNotNull('cj_keyword')->with('subcategories')->get();

        foreach ($categories as $index => $category) {
            // Delay each job by a few seconds to avoid completely slamming the API 
            // even though the HTTP client has internal rate limiting.
            SyncCategoryJob::dispatch($category, $pages)->delay(now()->addSeconds($index * 5));
            $this->line("Queued: {$category->name}");
        }

        $this->info("All categories dispatched to Queue!");
    }
}
