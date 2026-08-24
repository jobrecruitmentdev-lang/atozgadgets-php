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
        $product = Product::find($productId);
        if (!$product || empty($product->thumbnail_image)) {
            return $this->fallbackImage();
        }

        return $this->serveOrProxyImage($product->thumbnail_image, "product_thumb_{$productId}");
    }

    /**
     * Serve a specific gallery image securely.
     */
    public function image($productId, $mediaId)
    {
        $media = ProductMedia::where('product_id', $productId)->where('id', $mediaId)->first();
        if (!$media || empty($media->url)) {
            return $this->fallbackImage();
        }

        if (!empty($media->storage_path) && Storage::disk('public')->exists($media->storage_path)) {
            return response()->file(Storage::disk('public')->path($media->storage_path));
        }

        return $this->serveOrProxyImage($media->url, "product_media_{$productId}_{$mediaId}");
    }

    /**
     * Download or retrieve cached image and serve with aggressive HTTP caching.
     */
    private function serveOrProxyImage(string $url, string $cacheKey): Response
    {
        // If it's already a local storage path
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $localPath = public_path(ltrim($url, '/'));
            if (file_exists($localPath)) {
                return response()->file($localPath);
            }
            return $this->fallbackImage();
        }

        // Cache image binary for 7 days
        $cached = Cache::remember("media_bin_{$cacheKey}", 604800, function () use ($url) {
            try {
                $response = Http::timeout(8)->get($url);
                if ($response->successful()) {
                    return [
                        'body' => base64_encode($response->body()),
                        'mime' => $response->header('Content-Type') ?: 'image/jpeg',
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore fetch error
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
            return response()->file($path);
        }
        return response('', 404);
    }
}
