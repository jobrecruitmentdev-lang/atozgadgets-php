<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\Cj\CjProductService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DownloadProductImagesToDesktopCommand extends Command
{
    protected $signature = 'catalog:download-desktop 
                            {--from-live : Fetch products directly from live production site https://atozgadgetz.com}
                            {--decor-only : Only download decoration and holiday items}
                            {--include-drafts : Also download images for draft products (local DB mode)} 
                            {--path= : Custom destination path on desktop or disk}';

    protected $description = 'Downloads all product photos (thumbnails, gallery, and variants) for live CJ products directly to the user Desktop in organized folders.';

    public function handle(): int
    {
        $this->info("=================================================");
        $this->info(" AtoZGadgets - Live Product Images Exporter     ");
        $this->info("=================================================");

        // 1. Resolve Desktop Destination Path
        $customPath = $this->option('path');
        if (!empty($customPath)) {
            $baseDir = rtrim($customPath, '/\\');
        } else {
            $oneDriveDesktop = 'C:\\Users\\Dell\\OneDrive\\Desktop';
            $standardDesktop = 'C:\\Users\\Dell\\Desktop';
            $desktopRoot = is_dir($oneDriveDesktop) ? $oneDriveDesktop : $standardDesktop;
            $baseDir = $desktopRoot . DIRECTORY_SEPARATOR . 'AtoZ_Live_Products_Images';
        }

        if (!File::exists($baseDir)) {
            File::makeDirectory($baseDir, 0755, true);
        }

        $this->info("Target Directory: <fg=cyan>{$baseDir}</>");

        if ($this->option('from-live')) {
            return $this->handleLiveSiteExport($baseDir);
        }

        return $this->handleLocalDatabaseExport($baseDir);
    }

    /**
     * Export products directly from live production site atozgadgetz.com
     */
    private function handleLiveSiteExport(string $baseDir): int
    {
        $this->info("\n<fg=green;options=bold>Connecting to Live Production API (https://atozgadgetz.com)...</>");

        $liveBaseUrl = 'https://atozgadgetz.com';
        $page = 1;
        $allLiveProducts = [];

        do {
            $url = "{$liveBaseUrl}/api/products?page={$page}&limit=20";
            $resp = Http::timeout(15)->get($url);

            if (!$resp->successful()) {
                $this->error("Failed to fetch products from {$url} (Status: {$resp->status()})");
                break;
            }

            $json = $resp->json();
            $products = $json['data']['products'] ?? [];
            $pagination = $json['data']['pagination'] ?? [];
            $totalPages = $pagination['totalPages'] ?? 1;

            foreach ($products as $p) {
                $allLiveProducts[] = $p;
            }

            $page++;
        } while ($page <= $totalPages);

        $this->info("Discovered total <fg=yellow>" . count($allLiveProducts) . "</> products from live website.");

        // Filter if decor-only requested
        $decorKeywords = ['decor', 'decoration', 'lamp', 'light', 'night', 'tree', 'lantern', 'candle', 'tapestry', 'sculpture', 'bow', 'flower', 'garland', 'ornament', 'pendant', 'christmas', 'halloween'];
        if ($this->option('decor-only')) {
            $allLiveProducts = array_filter($allLiveProducts, function($item) use ($decorKeywords) {
                $titleLower = strtolower($item['name'] ?? '');
                foreach ($decorKeywords as $kw) {
                    if (str_contains($titleLower, $kw)) {
                        return true;
                    }
                }
                return false;
            });
            $this->info("Filtered to <fg=yellow>" . count($allLiveProducts) . "</> Decoration products.\n");
        }

        $totalImagesSaved = 0;
        $summary = [];

        foreach ($allLiveProducts as $item) {
            $id = $item['id'];
            $name = $item['name'] ?? 'Product';
            $slug = $item['slug'] ?? '';

            // Clean folder name for Windows OS
            $cleanTitle = preg_replace('/[\\\\\/:*?"<>|]/', '', $name);
            $cleanTitle = Str::slug(Str::limit($cleanTitle, 40, ''));
            $folderName = "{$id}_{$cleanTitle}";
            $productFolder = $baseDir . DIRECTORY_SEPARATOR . $folderName;

            if (!File::exists($productFolder)) {
                File::makeDirectory($productFolder, 0755, true);
            }

            $this->line("<fg=yellow>[Processing Live #{$id}]</> {$name}");

            // Fetch full product details including gallery and variants
            $detailImages = [];
            $detailResp = null;
            if (!empty($slug)) {
                try {
                    $detailResp = Http::timeout(10)->get("{$liveBaseUrl}/api/products/{$slug}");
                } catch (\Throwable $e) {
                    // non-fatal, fallback to list item
                }
            }

            $fullData = ($detailResp && $detailResp->successful()) ? ($detailResp->json()['data'] ?? $item) : $item;

            // A. Thumbnail
            $thumb = trim($fullData['thumbnail_image'] ?? ($item['thumbnail_image'] ?? ''));
            if (!empty($thumb)) {
                $thumbUrl = $thumb;
                if (!str_starts_with($thumbUrl, 'http://') && !str_starts_with($thumbUrl, 'https://')) {
                    $thumbUrl = "{$liveBaseUrl}/media/products/{$id}/thumbnail";
                }
                $detailImages[md5($thumbUrl)] = [
                    'source' => $thumbUrl,
                    'prefix' => '01_main_thumbnail',
                    'label' => 'Main Thumbnail'
                ];
            } else {
                // Media proxy fallback
                $fallbackUrl = "{$liveBaseUrl}/media/products/{$id}/thumbnail";
                $detailImages[md5($fallbackUrl)] = [
                    'source' => $fallbackUrl,
                    'prefix' => '01_main_thumbnail',
                    'label' => 'Main Thumbnail'
                ];
            }

            // B. Gallery Media
            $mediaList = $fullData['media'] ?? [];
            if (is_array($mediaList)) {
                foreach ($mediaList as $idx => $m) {
                    $mediaUrl = trim($m['url'] ?? '');
                    $mediaId = $m['id'] ?? null;
                    if (!empty($mediaUrl)) {
                        if (!str_starts_with($mediaUrl, 'http://') && !str_starts_with($mediaUrl, 'https://')) {
                            $mediaUrl = $mediaId ? "{$liveBaseUrl}/media/products/{$id}/image/{$mediaId}" : "{$liveBaseUrl}/" . ltrim($mediaUrl, '/');
                        }
                        $key = md5($mediaUrl);
                        if (!isset($detailImages[$key])) {
                            $order = str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT);
                            $detailImages[$key] = [
                                'source' => $mediaUrl,
                                'prefix' => "02_gallery_view_{$order}",
                                'label' => $m['alt_text'] ?? "Gallery {$order}"
                            ];
                        }
                    }
                }
            }

            // C. Variants
            $variants = $fullData['variants'] ?? [];
            if (is_array($variants)) {
                foreach ($variants as $vIdx => $v) {
                    $vUrl = trim($v['image_url'] ?? ($v['image'] ?? ''));
                    if (!empty($vUrl)) {
                        if (!str_starts_with($vUrl, 'http://') && !str_starts_with($vUrl, 'https://')) {
                            $vUrl = "{$liveBaseUrl}/" . ltrim($vUrl, '/');
                        }
                        $key = md5($vUrl);
                        if (!isset($detailImages[$key])) {
                            $vOrder = str_pad((string)($vIdx + 1), 2, '0', STR_PAD_LEFT);
                            $vTitle = !empty($v['name']) ? Str::slug(Str::limit($v['name'], 20, '')) : "var_{$vOrder}";
                            $detailImages[$key] = [
                                'source' => $vUrl,
                                'prefix' => "03_variant_{$vOrder}_{$vTitle}",
                                'label' => $v['name'] ?? "Variant {$vOrder}"
                            ];
                        }
                    }
                }
            }

            // Download Collected Images
            $savedThisProduct = 0;
            foreach ($detailImages as $imgItem) {
                $src = $imgItem['source'];
                $prefix = $imgItem['prefix'];

                $parsed = parse_url($src, PHP_URL_PATH) ?? '';
                $ext = strtolower(pathinfo($parsed, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }

                $destFile = "{$prefix}.{$ext}";
                $destPath = $productFolder . DIRECTORY_SEPARATOR . $destFile;

                if (File::exists($destPath) && filesize($destPath) > 500) {
                    $savedThisProduct++;
                    $totalImagesSaved++;
                    $this->line("  <fg=gray>•</> Exists: {$destFile}");
                    continue;
                }

                try {
                    $response = Http::timeout(20)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                            'Referer' => 'https://cjdropshipping.com/',
                            'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                        ])
                        ->get($src);

                    if ($response->successful() && !empty($response->body())) {
                        File::put($destPath, $response->body());
                        $savedThisProduct++;
                        $totalImagesSaved++;
                        $this->line("  <fg=green>✓</> Downloaded: {$destFile} (" . round(strlen($response->body()) / 1024, 1) . " KB)");
                    } else {
                        $this->line("  <fg=red>✗</> Failed ({$response->status()}): {$src}");
                    }
                } catch (\Throwable $e) {
                    $this->line("  <fg=red>✗</> Error: " . $e->getMessage());
                }
            }

            $summary[] = [
                'id' => $id,
                'name' => Str::limit($name, 45),
                'saved' => $savedThisProduct,
                'folder' => $folderName,
            ];
            $this->line("");
        }

        // Summary Table
        $this->info("=================================================");
        $this->info(" Live Download Summary                           ");
        $this->info("=================================================");
        $this->table(
            ['ID', 'Product Name', 'Images Saved', 'Folder Name'],
            array_map(fn($row) => [$row['id'], $row['name'], $row['saved'], $row['folder']], $summary)
        );

        $this->info("\n<fg=green;options=bold>SUCCESS:</> Saved total of <fg=yellow;options=bold>{$totalImagesSaved}</> images from Live Site.");
        $this->info("Desktop Location: <fg=cyan;options=bold>{$baseDir}</>\n");

        return 0;
    }

    /**
     * Export products from local MySQL database
     */
    private function handleLocalDatabaseExport(string $baseDir): int
    {
        $query = Product::with(['cjProduct', 'media', 'variants']);

        if (!$this->option('include-drafts')) {
            $query->where('status', 'active')->where('is_active', true);
        }

        // Target CJ products
        $query->where(function ($q) {
            $q->where('fulfillment_type', 'cj')
              ->orWhereHas('cjProduct');
        });

        $products = $query->orderBy('id', 'asc')->get();

        if ($products->isEmpty()) {
            $this->warn("No matching CJ products found in local database.");
            return 0;
        }

        $this->info("Found " . $products->count() . " products to process from local DB.\n");

        $totalImagesSaved = 0;
        $summary = [];

        foreach ($products as $product) {
            $cleanTitle = preg_replace('/[\\\\\/:*?"<>|]/', '', $product->name);
            $cleanTitle = Str::slug(Str::limit($cleanTitle, 40, ''));
            if (empty($cleanTitle)) {
                $cleanTitle = 'product';
            }
            $productDirName = "{$product->id}_{$cleanTitle}";
            $productFolder = $baseDir . DIRECTORY_SEPARATOR . $productDirName;

            if (!File::exists($productFolder)) {
                File::makeDirectory($productFolder, 0755, true);
            }

            $this->line("<fg=yellow>[Processing #{$product->id}]</> {$product->name}");

            $imagesToSave = [];

            // A. Primary Thumbnail
            $thumb = trim($product->thumbnail_image ?? '');
            if (!empty($thumb)) {
                $normThumb = CjProductService::normalizeImageUrl($thumb) ?: $thumb;
                $imagesToSave[md5($normThumb)] = [
                    'source' => $normThumb,
                    'prefix' => '01_main_thumbnail',
                    'label' => 'Main Thumbnail'
                ];
            }

            // Fallback: CJ Product original image
            if ($product->cjProduct && !empty($product->cjProduct->cj_image)) {
                $cjImg = CjProductService::normalizeImageUrl($product->cjProduct->cj_image);
                if (!empty($cjImg) && !isset($imagesToSave[md5($cjImg)])) {
                    $imagesToSave[md5($cjImg)] = [
                        'source' => $cjImg,
                        'prefix' => '01_cj_original_image',
                        'label' => 'CJ Original Thumbnail'
                    ];
                }
            }

            // B. Gallery Media
            foreach ($product->media as $idx => $m) {
                $mSource = trim($m->url ?? '');
                if (empty($mSource) && !empty($m->storage_path)) {
                    $mSource = '/storage/' . ltrim($m->storage_path, '/');
                }
                if (!empty($mSource)) {
                    $normMedia = CjProductService::normalizeImageUrl($mSource) ?: $mSource;
                    $key = md5($normMedia);
                    if (!isset($imagesToSave[$key])) {
                        $order = str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT);
                        $imagesToSave[$key] = [
                            'source' => $normMedia,
                            'prefix' => "02_gallery_view_{$order}",
                            'label' => $m->alt_text ?: "Gallery View {$order}"
                        ];
                    }
                }
            }

            // C. Product Variants
            foreach ($product->variants as $vIdx => $v) {
                $vSource = trim($v->image_url ?? '');
                if (!empty($vSource)) {
                    $normVar = CjProductService::normalizeImageUrl($vSource) ?: $vSource;
                    $key = md5($normVar);
                    if (!isset($imagesToSave[$key])) {
                        $vOrder = str_pad((string)($vIdx + 1), 2, '0', STR_PAD_LEFT);
                        $vNameSlug = !empty($v->name) ? Str::slug(Str::limit($v->name, 20, '')) : "var_{$vOrder}";
                        $imagesToSave[$key] = [
                            'source' => $normVar,
                            'prefix' => "03_variant_{$vOrder}_{$vNameSlug}",
                            'label' => $v->name ?: "Variant {$vOrder}"
                        ];
                    }
                }
            }

            // Download or Copy Collected Images
            $productSavedCount = 0;

            foreach ($imagesToSave as $item) {
                $source = $item['source'];
                $prefix = $item['prefix'];

                $parsedPath = parse_url($source, PHP_URL_PATH) ?? '';
                $ext = strtolower(pathinfo($parsedPath, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }

                $destinationFileName = "{$prefix}.{$ext}";
                $destinationPath = $productFolder . DIRECTORY_SEPARATOR . $destinationFileName;

                // Case 1: Local Storage File
                if (str_starts_with($source, '/storage/') || str_starts_with($source, 'storage/')) {
                    $subPath = substr(ltrim($source, '/'), 8);
                    if (Storage::disk('public')->exists($subPath)) {
                        $fullLocalPath = Storage::disk('public')->path($subPath);
                        if (File::copy($fullLocalPath, $destinationPath)) {
                            $productSavedCount++;
                            $totalImagesSaved++;
                            $this->line("  <fg=green>✓</> Copied local: {$destinationFileName}");
                            continue;
                        }
                    }
                    $pubLocal = public_path(ltrim($source, '/'));
                    if (File::exists($pubLocal)) {
                        if (File::copy($pubLocal, $destinationPath)) {
                            $productSavedCount++;
                            $totalImagesSaved++;
                            $this->line("  <fg=green>✓</> Copied local: {$destinationFileName}");
                            continue;
                        }
                    }
                }

                // Case 2: Remote URL
                if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
                    try {
                        $response = Http::timeout(20)
                            ->withHeaders([
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                                'Referer' => 'https://cjdropshipping.com/',
                                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                            ])
                            ->get($source);

                        if ($response->successful() && !empty($response->body())) {
                            File::put($destinationPath, $response->body());
                            $productSavedCount++;
                            $totalImagesSaved++;
                            $this->line("  <fg=green>✓</> Downloaded: {$destinationFileName} (" . round(strlen($response->body()) / 1024, 1) . " KB)");
                        } else {
                            $this->line("  <fg=red>✗</> Failed ({$response->status()}): {$source}");
                        }
                    } catch (\Throwable $e) {
                        $this->line("  <fg=red>✗</> Error downloading {$source}: " . $e->getMessage());
                    }
                }
            }

            $summary[] = [
                'id' => $product->id,
                'name' => Str::limit($product->name, 45),
                'saved' => $productSavedCount,
                'folder' => $productDirName,
            ];
            $this->line("");
        }

        // Summary Table
        $this->info("=================================================");
        $this->info(" Download Summary                                ");
        $this->info("=================================================");
        $this->table(
            ['ID', 'Product Name', 'Images Saved', 'Folder Name'],
            array_map(fn($row) => [$row['id'], $row['name'], $row['saved'], $row['folder']], $summary)
        );

        $this->info("\n<fg=green;options=bold>SUCCESS:</> Saved total of <fg=yellow;options=bold>{$totalImagesSaved}</> images.");
        $this->info("Desktop Location: <fg=cyan;options=bold>{$baseDir}</>\n");

        return 0;
    }
}
