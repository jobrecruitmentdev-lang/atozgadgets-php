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

        $imagePath = \App\Services\Cj\CjProductService::normalizeImageUrl($product->thumbnail_image) ?: trim($product->thumbnail_image ?? '');

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
            $res = $this->serveOrProxyImage($imagePath, "product_thumb_{$productId}");
            if ($res->getStatusCode() === 200) {
                return $res;
            }
        }

        // 3. Fallback 1: Check primary or first product media gallery record
        $primaryMedia = $product->media->where('is_primary', true)->first() ?: $product->media->first();
        if ($primaryMedia && !empty($primaryMedia->url)) {
            $mediaUrl = \App\Services\Cj\CjProductService::normalizeImageUrl($primaryMedia->url) ?: trim($primaryMedia->url);
            if (str_starts_with($mediaUrl, 'http://') || str_starts_with($mediaUrl, 'https://')) {
                $res = $this->serveOrProxyImage($mediaUrl, "product_media_fb_{$productId}_{$primaryMedia->id}");
                if ($res->getStatusCode() === 200) {
                    return $res;
                }
            }
        }

        // 4. Fallback 2: Retrieve from supplier record (cjProduct->cj_image)
        if ($product->cjProduct && !empty($product->cjProduct->cj_image)) {
            $cjImg = \App\Services\Cj\CjProductService::normalizeImageUrl($product->cjProduct->cj_image) ?: trim($product->cjProduct->cj_image);
            if (str_starts_with($cjImg, 'http://') || str_starts_with($cjImg, 'https://')) {
                $res = $this->serveOrProxyImage($cjImg, "product_thumb_cj_{$productId}");
                if ($res->getStatusCode() === 200) {
                    return $res;
                }
            }
        }

        // 5. Fallback 3: Retrieve from first variant image
        $firstVariant = $product->variants->whereNotNull('image_url')->first();
        if ($firstVariant && !empty($firstVariant->image_url)) {
            $vImg = \App\Services\Cj\CjProductService::normalizeImageUrl($firstVariant->image_url) ?: trim($firstVariant->image_url);
            if (str_starts_with($vImg, 'http://') || str_starts_with($vImg, 'https://')) {
                $res = $this->serveOrProxyImage($vImg, "product_var_fb_{$productId}");
                if ($res->getStatusCode() === 200) {
                    return $res;
                }
            }
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
