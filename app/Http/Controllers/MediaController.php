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
        $product = Product::with('cjProduct')->find($productId);
        if (!$product) {
            return $this->fallbackImage();
        }

        $imagePath = $product->thumbnail_image;

        // 1. If it's a local storage path (/storage/products/xyz.jpg), check if file exists directly on disk
        if (!empty($imagePath) && !str_starts_with($imagePath, 'http://') && !str_starts_with($imagePath, 'https://')) {
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

        // 2. If thumbnail_image is a remote URL, serve or proxy and cache it
        if (!empty($imagePath) && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))) {
            return $this->serveOrProxyImage($imagePath, "product_thumb_{$productId}");
        }

        // 3. Fallback: If local file not found on disk, retrieve from supplier record (cjProduct->cj_image)
        if ($product->cjProduct && !empty($product->cjProduct->cj_image)) {
            return $this->serveOrProxyImage($product->cjProduct->cj_image, "product_thumb_cj_{$productId}");
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
            if (!str_starts_with($media->url, 'http://') && !str_starts_with($media->url, 'https://')) {
                $cleaned = ltrim($media->url, '/');
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
                return $this->serveOrProxyImage($media->url, "product_media_{$productId}_{$mediaId}");
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
        $targetUrl = trim($url);
        if (str_starts_with($targetUrl, '//')) {
            $targetUrl = 'https:' . $targetUrl;
        }

        // Cache image binary for 7 days
        $cached = Cache::remember("media_bin_{$cacheKey}", 604800, function () use ($targetUrl) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                        'Referer' => 'https://cjdropshipping.com/'
                    ])
                    ->get($targetUrl);

                if ($response->successful() && !empty($response->body())) {
                    return [
                        'body' => base64_encode($response->body()),
                        'mime' => $response->header('Content-Type') ?: 'image/jpeg',
                    ];
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Media proxy fetch error for {$targetUrl}: " . $e->getMessage());
            }
            return null;
        });

        if ($cached && !empty($cached['body'])) {
            return response(base64_decode($cached['body']))
                ->header('Content-Type', $cached['mime'])
                ->header('Cache-Control', 'public, max-age=604800, immutable');
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
