<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductMedia;
use App\Services\Cj\CjProductService;
use App\Services\Catalog\ProductContentService;
use Illuminate\Support\Facades\DB;

class RepairCorruptedProductMediaCommand extends Command
{
    protected $signature = 'media:repair-corrupted';
    protected $description = 'Repairs corrupted JSON-encoded ProductMedia strings into individual rows with local storage downloads.';

    public function handle()
    {
        $this->info('Scanning for corrupted ProductMedia entries...');

        $corrupted = ProductMedia::where('url', 'LIKE', '[%')
            ->orWhere('url', 'LIKE', '%,%')
            ->get();

        $this->info("Found {$corrupted->count()} corrupted records to repair.");

        $repairedCount = 0;
        foreach ($corrupted as $item) {
            $extracted = CjProductService::extractImageList($item->url);
            if (empty($extracted)) {
                continue;
            }

            $productId = $item->product_id;
            $altBase = $item->alt_text ?: 'Product View';

            DB::transaction(function () use ($item, $extracted, $productId, $altBase, &$repairedCount) {
                // Delete the single corrupted entry
                $item->delete();

                // Insert all unpacked images
                foreach ($extracted as $idx => $remoteUrl) {
                    $localUrl = ProductContentService::downloadAndStoreMedia($remoteUrl);

                    // Check if already exists for this product
                    $exists = ProductMedia::where('product_id', $productId)
                        ->where(function($q) use ($localUrl, $remoteUrl) {
                            $q->where('url', $localUrl)->orWhere('url', $remoteUrl);
                        })
                        ->exists();

                    if (!$exists) {
                        ProductMedia::create([
                            'product_id' => $productId,
                            'type' => 'image',
                            'url' => $localUrl,
                            'alt_text' => "{$altBase} - Gallery " . ($idx + 1),
                            'sort_order' => $idx + 1,
                            'is_primary' => false,
                        ]);
                        $repairedCount++;
                    }
                }
            });
        }

        $this->info("Successfully created {$repairedCount} clean ProductMedia rows.");
        return 0;
    }
}
