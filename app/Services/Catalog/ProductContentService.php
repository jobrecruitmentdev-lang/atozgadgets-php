<?php

namespace App\Services\Catalog;

use Illuminate\Support\Str;

class ProductContentService
{
    private static array $prohibitedKeywords = [
        'cjdropshipping',
        'cj dropshipping',
        'dropshipping',
        'supplier',
        'alibaba',
        'aliexpress',
        'taobao',
        '1688',
        'factory direct',
        'wholesale',
        'oem/odm',
        'cjpacket',
    ];

    private static array $categoryKeywords = [
        'Music & Instruments' => ['guitar', 'pick', 'string', 'instrument', 'acoustic', 'electric', 'tuner', 'capo', 'pedal', 'audio'],
        'Smart Home' => ['smart', 'wifi', 'sensor', 'light', 'lamp', 'plug', 'automation', 'socket', 'alexa', 'google home'],
        'Wearable Tech' => ['watch', 'smartwatch', 'band', 'tracker', 'fitness', 'heart rate', 'amoled', 'wearable'],
        'Audio & Sound' => ['speaker', 'headphone', 'earphone', 'earbuds', 'bluetooth', 'sound', 'microphone', 'bass'],
        'Electronics & Gadgets' => ['charger', 'usb', 'cable', 'adapter', 'projector', 'gadget', 'led', 'power bank', 'drone'],
        'Home & Kitchen' => ['bottle', 'blender', 'flask', 'kitchen', 'vacuum', 'cup', 'mug', 'dispenser'],
    ];

    /**
     * Clean and normalize product title for customer storefront.
     */
    public static function normalizeTitle(string $title, ?string $categoryName = null): string
    {
        $clean = trim($title);

        // Remove Chinese characters or non-Latin garbage if present
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $clean) && strlen(preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $clean)) > 5) {
            $clean = preg_replace('/[\x{4e00}-\x{9fa5}]/u', '', $clean);
        }

        // Strip prohibited supplier keywords
        foreach (self::$prohibitedKeywords as $prohibited) {
            $clean = preg_replace('/\b' . preg_quote($prohibited, '/') . '\b/i', '', $clean);
        }

        // Remove redundant SKU codes (e.g. CJZN2877369, CJ-PROJ-1080P) from title
        $clean = preg_replace('/\bCJ[A-Z0-9_-]+\b/i', '', $clean);
        $clean = preg_replace('/\bSKU\s*[:#-]?\s*[A-Z0-9_-]+\b/i', '', $clean);

        // Normalize multiple spaces and punctuation
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean, " \t\n\r\0\x0B-_|,:");

        if (empty($clean) || strlen($clean) < 3) {
            $clean = 'AtoZ Premium ' . ($categoryName ? $categoryName . ' Gadget' : 'Lifestyle Item');
        }

        return Str::limit($clean, 200, '');
    }

    /**
     * Clean and normalize description, eliminating supplier references, URLs, and mismatching copy.
     */
    public static function normalizeDescription(string $description, string $title, ?string $categoryName = null): string
    {
        $clean = trim($description);

        // 1. Strip raw script, style, and external links
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $clean);
        $clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $clean);

        // 2. Strip supplier URLs and emails
        $clean = preg_replace('/https?:\/\/[^\s<>"]+/i', '', $clean);
        $clean = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '', $clean);

        // 3. Strip prohibited keywords
        foreach (self::$prohibitedKeywords as $prohibited) {
            $clean = preg_replace('/\b' . preg_quote($prohibited, '/') . '\b/i', '', $clean);
        }

        // 4. Strip raw HTML except standard formatting tags
        $clean = strip_tags($clean, '<p><br><ul><ol><li><strong><b><em><h3><h4>');

        // 5. Category-to-description affinity check (prevents guitar picks with smartwatch copy)
        if ($categoryName && isset(self::$categoryKeywords[$categoryName])) {
            $descLower = strtolower($clean);

            // Check if description mentions completely disjoint category keywords
            $conflictingCategories = [
                'Wearable Tech' => ['smartwatch', 'heart rate', 'spo2', 'pedometer', 'sleep tracking', 'amoled watch', 'wearables'],
                'Music & Instruments' => ['guitar', 'plectrum', 'acoustic guitar', 'electric guitar', 'capo'],
            ];

            if ($categoryName === 'Music & Instruments' || str_contains(strtolower($title), 'guitar')) {
                foreach ($conflictingCategories['Wearable Tech'] as $conflictKw) {
                    if (str_contains($descLower, $conflictKw)) {
                        // Conflict detected! Replace with generated high quality category description
                        $clean = "<p>The <strong>{$title}</strong> is precision-engineered for musicians and enthusiasts. Crafted with premium, durable materials to deliver exceptional resonance, tactile feel, and long-lasting performance for practice, recording, and live sessions.</p>";
                        break;
                    }
                }
            }
        }

        if (empty(strip_tags($clean)) || strlen(trim(strip_tags($clean))) < 20) {
            $clean = "<p>The <strong>{$title}</strong> is designed to offer premium build quality, modern aesthetics, and dependable performance. Thoroughly inspected to ensure high customer satisfaction and daily convenience.</p>";
        }

        return $clean;
    }

    /**
     * Generate customer-safe Merchant SKU (e.g. AZG-GDT-4821 or AZG-GTR-102).
     */
    public static function generateMerchantSku(?string $categoryName = null, ?int $id = null): string
    {
        $catCode = 'GDT';
        if ($categoryName) {
            $words = preg_split('/\s+/', strtoupper(preg_replace('/[^A-Za-z0-9 ]/', '', $categoryName)));
            $catCode = substr($words[0] ?? 'GDT', 0, 3);
        }

        $uniqueSuffix = $id ? str_pad((string)$id, 4, '0', STR_PAD_LEFT) : strtoupper(substr(md5(uniqid('', true)), 0, 4));
        return "AZG-{$catCode}-{$uniqueSuffix}";
    }

    /**
     * Validate an imported product against the strict Launch Gate (Pillar 8).
     */
    public static function validateForPublish(array $productData): array
    {
        $errors = [];
        $warnings = [];

        // Check required fields
        if (empty($productData['name']) || strlen(trim($productData['name'])) < 3) {
            $errors[] = 'Product name is missing or too short.';
        }

        if (empty($productData['description']) || strlen(trim(strip_tags($productData['description']))) < 10) {
            $errors[] = 'Product description is missing or insufficient.';
        }

        if (empty($productData['category_id']) && empty($productData['category'])) {
            $errors[] = 'Product category must be specified.';
        }

        if (empty($productData['thumbnail_image']) && empty($productData['image'])) {
            $errors[] = 'Product must have at least one primary image.';
        }

        $price = (float)($productData['price'] ?? 0);
        if ($price <= 0) {
            $errors[] = 'Product retail price must be greater than zero.';
        }

        // Supplier Leakage Guards
        $allText = json_encode($productData);
        foreach (self::$prohibitedKeywords as $keyword) {
            if (stripos($allText, $keyword) !== false) {
                $warnings[] = "Prohibited supplier keyword '{$keyword}' detected in product data.";
            }
        }

        if (isset($productData['sku']) && str_starts_with(strtoupper($productData['sku']), 'CJ')) {
            $warnings[] = 'Supplier-derived SKU detected. Customer-facing merchant SKU will be auto-generated.';
        }

        $canPublish = empty($errors);

        return [
            'can_publish' => $canPublish,
            'status' => $canPublish ? 'active' : 'draft',
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Download a remote image and store it to public storage to eliminate runtime proxy latency.
     * Returns the local storage public URL or the remote URL fallback.
     */
    public static function downloadAndStoreMedia(?string $remoteUrl, string $folder = 'products'): string
    {
        if (empty($remoteUrl) || !filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
            return $remoteUrl ?: '';
        }

        $parsed = parse_url($remoteUrl);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host = strtolower($parsed['host'] ?? '');

        // 1. Strict Scheme Validation
        if (!in_array($scheme, ['http', 'https']) || empty($host)) {
            return $remoteUrl;
        }

        // 2. SSRF Protection: Reject localhost, loopback, and cloud metadata names
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '169.254.169.254', 'metadata.google.internal'])) {
            \Illuminate\Support\Facades\Log::warning("SSRF Protection: Blocked loopback/metadata host '{$host}' in media import.");
            return '';
        }

        // 3. DNS Resolution and Private/Reserved IP Filtering
        $ip = gethostbyname($host);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            \Illuminate\Support\Facades\Log::warning("SSRF Protection: Host '{$host}' resolved to non-public/private IP '{$ip}'. Request aborted.");
            return '';
        }

        // Reject link-local / cloud metadata IP range (169.254.0.0/16)
        if (str_starts_with($ip, '169.254.') || str_starts_with($ip, '127.') || str_starts_with($ip, '10.') || str_starts_with($ip, '192.168.')) {
            \Illuminate\Support\Facades\Log::warning("SSRF Protection: Blocked private IP '{$ip}' in media import.");
            return '';
        }

        $ext = pathinfo($parsed['path'] ?? '', PATHINFO_EXTENSION);
        $ext = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? strtolower($ext) : 'jpg';
        $filename = md5($remoteUrl) . '.' . $ext;
        $storagePath = "{$folder}/{$filename}";

        // Fast path: avoid redundant HTTP downloads if media already stored
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
            return '/storage/' . $storagePath;
        }

        try {
            // Disable arbitrary redirect chasing to prevent DNS rebinding redirects
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withOptions(['allow_redirects' => false])
                ->get($remoteUrl);

            if ($response->successful() && !empty($response->body())) {
                $contentType = strtolower($response->header('Content-Type') ?? '');
                
                // Validate that response is genuinely an image
                if (!empty($contentType) && !str_starts_with($contentType, 'image/')) {
                    \Illuminate\Support\Facades\Log::warning("Media import rejected non-image Content-Type '{$contentType}' from {$remoteUrl}");
                    return '';
                }

                \Illuminate\Support\Facades\Storage::disk('public')->put($storagePath, $response->body());
                return '/storage/' . $storagePath;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Media download failed for {$remoteUrl}: " . $e->getMessage());
        }

        // Non-blocking fallback
        return $remoteUrl;
    }
}