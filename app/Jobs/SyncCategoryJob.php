<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Category;
use App\Services\Cj\CjSyncService;
use Illuminate\Support\Facades\Log;

class SyncCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;
    protected $pagesPerCategory;

    /**
     * Create a new job instance.
     */
    public function __construct(Category $category, $pagesPerCategory = 2)
    {
        $this->category = $category;
        $this->pagesPerCategory = $pagesPerCategory;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->category->cj_keyword) return;

        $subcategoryId = $this->category->subcategories->first()?->id;
        if (!$subcategoryId) {
            Log::warning("[SyncCategoryJob] Skipping {$this->category->name}: no subcategories.");
            return;
        }

        Log::info("[SyncCategoryJob] Processing {$this->category->name}...");

        for ($page = 1; $page <= $this->pagesPerCategory; $page++) {
            $hasMore = CjSyncService::processCategoryPage($this->category, $subcategoryId, $page);
            if (!$hasMore) {
                break;
            }
        }
    }
}
