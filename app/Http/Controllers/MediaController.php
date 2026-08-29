<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * Serve a product's primary thumbnail securely without exposing supplier CDN domains.
     */
    public function thumbnail($productId)
    {
        $product = Product::with(['cjProduct', 'media', 'variants'])->find($productId);
        if (!$product) {
            return $this->fallbackImage();
        }

        $imagePath = trim($product->thumbnail_image ?? '');

        // 1. If it's a local storage path (/storage/products/xyz.jpg), check if file exists directly on disk
        if (!empty($imagePath) && !str_starts_with($imagePath, 'http://') && !str_starts_with($imagePath, 'https://') && !str_starts_with($imagePath, '//')) {
            $cleaned = ltrim($imagePath, '/');
            if (str_starts_with($cleaned, 'storage/')) {
                $storageSub = substr($cleaned, 8);
                if (Storage::disk('public')->exists($storageSub)) {
                    return response()->file(Storage::disk('public')->path($storageSub), [
                        'Cache-Control' => 'public, max-age=604800, immutable'
                    ]);
                }
            }
            $localPath = public_path($cleaned);
            if (file_exists($localPath) && !is_dir($localPath)) {
                return response()->file($localPath, [
                    'Cache-Control' => 'public, max-age=604800, immutable'
                ]);
            }
        }

        // 2. Resolve remote image URL via multi-tier fallback
        $targetUrl = '';
        if (!empty($imagePath) && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, '//'))) {
            $targetUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($imagePath);
        }

        // Fallback 1: Supplier record (cjProduct->cj_image)
        if (empty($targetUrl) && $product->cjProduct && !empty($product->cjProduct->cj_image)) {
            $targetUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($product->cjProduct->cj_image);
        }

        // Fallback 2: Product media gallery
        if (empty($targetUrl)) {
            foreach ($product->media as $m) {
                if (!empty($m->url) && (str_starts_with($m->url, 'http://') || str_starts_with($m->url, 'https://') || str_starts_with($m->url, '//'))) {
                    $targetUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($m->url);
                    if (!empty($targetUrl)) break;
                }
            }
        }

        // Fallback 3: Product variant image
        if (empty($targetUrl)) {
            foreach ($product->variants as $v) {
                if (!empty($v->image_url) && (str_starts_with($v->image_url, 'http://') || str_starts_with($v->image_url, 'https://') || str_starts_with($v->image_url, '//'))) {
                    $targetUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($v->image_url);
                    if (!empty($targetUrl)) break;
                }
            }
        }

        if (!empty($targetUrl)) {
            return $this->serveOrProxyImage($targetUrl, "product_thumb_{$productId}");
        }

        return $this->fallbackImage();
    }

    /**
     * Serve a specific gallery image securely.
     */
    public function image($productId, $mediaId)
    {
        $media = ProductMedia::where('product_id', $productId)->where('id', $mediaId)->first();
        if (!$media) {
            return $this->fallbackImage();
        }

        if (!empty($media->storage_path) && Storage::disk('public')->exists($media->storage_path)) {
            return response()->file(Storage::disk('public')->path($media->storage_path), [
                'Cache-Control' => 'public, max-age=604800, immutable'
            ]);
        }

        if (!empty($media->url)) {
            $normUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($media->url) ?: trim($media->url);
            if (!str_starts_with($normUrl, 'http://') && !str_starts_with($normUrl, 'https://')) {
                $cleaned = ltrim($normUrl, '/');
                if (str_starts_with($cleaned, 'storage/')) {
                    $storageSub = substr($cleaned, 8);
                    if (Storage::disk('public')->exists($storageSub)) {
                        return response()->file(Storage::disk('public')->path($storageSub), [
                            'Cache-Control' => 'public, max-age=604800, immutable'
                        ]);
                    }
                }
                $localPath = public_path($cleaned);
                if (file_exists($localPath) && !is_dir($localPath)) {
                    return response()->file($localPath, [
                        'Cache-Control' => 'public, max-age=604800, immutable'
                    ]);
                }
            } else {
                return $this->serveOrProxyImage($normUrl, "product_media_{$productId}_{$mediaId}");
            }
        }

        // Fallback to product thumbnail
        return $this->thumbnail($productId);
    }

    /**
     * Download or retrieve cached image and serve with aggressive HTTP caching.
     */
    private function serveOrProxyImage(string $url, string $cacheKey): Response
    {
        $targetUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($url) ?: trim($url);
        if (empty($targetUrl)) {
            return $this->fallbackImage();
        }

        // If cached in memory/file, serve immediately
        if (Cache::has("media_bin_{$cacheKey}")) {
            $cached = Cache::get("media_bin_{$cacheKey}");
            if ($cached && !empty($cached['body'])) {
                return response(base64_decode($cached['body']))
                    ->header('Content-Type', $cached['mime'] ?? 'image/jpeg')
                    ->header('Cache-Control', 'public, max-age=604800, immutable');
            }
        }

        try {
            $response = Http::timeout(4)
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => false,
                        'referer' => true,
                        'protocols' => ['http', 'https']
                    ]
                ])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    'Referer' => 'https://cjdropshipping.com/'
                ])
                ->get($targetUrl);

            if ($response->successful() && !empty($response->body())) {
                $mime = $response->header('Content-Type') ?: 'image/jpeg';
                Cache::put("media_bin_{$cacheKey}", [
                    'body' => base64_encode($response->body()),
                    'mime' => $mime,
                ], 604800);

                return response($response->body())
                    ->header('Content-Type', $mime)
                    ->header('Cache-Control', 'public, max-age=604800, immutable');
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Media proxy fetch error for {$targetUrl}: " . $e->getMessage());
        }

        // Bulletproof Fallback: Redirect browser directly to the image URL so it ALWAYS displays real product photo
        if (str_starts_with($targetUrl, 'http://') || str_starts_with($targetUrl, 'https://')) {
            return redirect()->away($targetUrl, 302, [
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        return $this->fallbackImage();
    }

    private function fallbackImage(): Response
    {
        $path = public_path('favicon.png');
        if (file_exists($path)) {
            return response()->file($path, [
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }
        return response('', 404);
    }
}
