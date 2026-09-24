<?php

namespace App\Services\Catalog;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryResolverService
{
    /**
     * Authoritative Root (Parent) Categories and their default subcategory classifications.
     */
    protected array $taxonomyMap = [
        'Electronics & Gadgets' => [
            'slug' => 'electronics-gadgets',
            'keywords' => ['drone', 'rc', 'camera', 'console', 'projector', 'game', 'controller', 'gadget', 'electronic', 'display'],
            'subcategories' => [
                'Drones & RC' => ['camera drone', 'gps drone', 'quadcopter', 'rc car', 'remote control', 'aircraft', 'uav', 'fpv', 'drone', 'copter'],
                'Action & Security Cameras' => ['action camera', 'security camera', 'dash cam', 'dashcam', 'cctv', 'surveillance', 'webcam', 'camcorder', 'gimbal', 'camera'],
                'Gaming & Office Gear' => ['gaming', 'gamepad', 'keyboard', 'mouse', 'pad', 'monitor', 'office', 'desk lamp', 'usb hub'],
                'Smart Tools & DIY' => ['laser', 'screwdriver', 'soldering', 'multimeter', 'tool', 'detector', 'diy', 'gauge'],
            ]
        ],
        'Smart Wearables' => [
            'slug' => 'smart-wearables',
            'keywords' => ['watch', 'smartwatch', 'band', 'tracker', 'wearable', 'ring', 'glasses', 'wrist'],
            'subcategories' => [
                'Smart Watches' => ['smart watch', 'smartwatch', 'apple watch', 'reloj', 'wrist watch', 'chronograph'],
                'Fitness Bands & Trackers' => ['fitness band', 'fitness tracker', 'pedometer', 'activity tracker', 'heart rate band'],
                'Smart Rings & Eyewear' => ['smart ring', 'smart glasses', 'audio glasses', 'vr headset', 'ar glasses'],
            ]
        ],
        'Home & Kitchen' => [
            'slug' => 'home-kitchen',
            'keywords' => ['home', 'kitchen', 'lamp', 'light', 'cleaner', 'vacuum', 'blender', 'purifier', 'aroma', 'diffuser', 'cook', 'baking', 'bedroom', 'living', 'decor', 'decoration', 'christmas'],
            'subcategories' => [
                'Smart Lighting & Lamps' => ['lamp', 'light', 'led', 'night light', 'rgb', 'bulb', 'strip', 'chandelier', 'projector light', 'lantern', 'tree lamp', 'atmosphere lamp', 'desk lamp'],
                'Kitchen Gadgets & Small Appliances' => ['kitchen', 'blender', 'mixer', 'juicer', 'cutter', 'grinder', 'peeler', 'cook', 'baking', 'scale', 'opener', 'dispenser'],
                'Home Cleaning & Robot Vacuums' => ['vacuum', 'cleaner', 'mop', 'sweeper', 'scrubber', 'trash can', 'dust', 'lint remover'],
                'Aroma & Air Purifiers' => ['purifier', 'humidifier', 'diffuser', 'aroma', 'essential oil', 'air quality', 'deodorizer'],
            ]
        ],
        'Mobile & Audio Accessories' => [
            'slug' => 'mobile-audio-accessories',
            'keywords' => ['phone', 'mobile', 'audio', 'earbud', 'headphone', 'speaker', 'charger', 'cable', 'power bank', 'case', 'bluetooth'],
            'subcategories' => [
                'Wireless Earbuds & Headphones' => ['earbud', 'earbuds', 'headphone', 'headphones', 'earphone', 'headset', 'airpod', 'tws'],
                'Bluetooth Speakers' => ['speaker', 'speakers', 'soundbar', 'loudspeaker', 'subwoofer', 'boombox'],
                'Wireless Chargers & Power Banks' => ['power bank', 'charger', 'wireless charger', 'fast charging', 'magsafe', 'battery pack', 'cable'],
                'Phone Mounts & Stands' => ['mount', 'stand', 'phone holder', 'car holder', 'tripod mount', 'dock'],
            ]
        ],
        'Lifestyle & Personal Care' => [
            'slug' => 'lifestyle-personal-care',
            'keywords' => ['massage', 'massager', 'beauty', 'travel', 'outdoor', 'shaving', 'trimmer', 'grooming', 'personal', 'novelty', 'gift', 'relax', 'decor', 'decoration'],
            'subcategories' => [
                'Health & Massage Gadgets' => ['massager', 'massage', 'neck massager', 'fascia gun', 'foot massager', 'cervical', 'therapy', 'relaxation'],
                'Travel & Outdoor Gear' => ['travel', 'outdoor', 'camping', 'flashlight', 'bottle', 'backpack', 'pocket', 'compass', 'survival'],
                'Novelty & Creative Gifts' => ['gift', 'toy', 'fidget', 'novelty', 'creative', 'puzzle', 'decor', 'desk gadget', 'christmas', 'decoration', 'pendant', 'ornament'],
            ]
        ]
    ];

    /**
     * Ensure core root categories and standard subcategories exist in the database.
     * Idempotent & safe: will not overwrite or alter existing records.
     */
    public function ensureCoreTaxonomy(): void
    {
        foreach ($this->taxonomyMap as $parentName => $parentData) {
            $parent = Category::firstOrCreate(
                ['slug' => $parentData['slug']],
                [
                    'name' => $parentName,
                    'parent_id' => null,
                    'description' => "Explore premium {$parentName} at AtoZGadgets.",
                    'status' => 'active'
                ]
            );

            // If name differs slightly, update name and ensure parent_id is null
            if ($parent->parent_id !== null) {
                $parent->update(['parent_id' => null]);
            }

            foreach ($parentData['subcategories'] as $subName => $keywords) {
                $subSlug = Str::slug($subName);
                Category::firstOrCreate(
                    ['slug' => $subSlug],
                    [
                        'name' => $subName,
                        'parent_id' => $parent->id,
                        'description' => "Shop trending {$subName} online.",
                        'status' => 'active'
                    ]
                );
            }
        }
    }

    /**
     * Resolve or dynamically create the appropriate subcategory for an incoming product.
     *
     * @param string|null $incomingCategory Raw category from CJ or user input (e.g. "Consumer Electronics > Smart Watches" or "Watches")
     * @param string|null $productTitle Product title / name for fallback keyword classification
     * @param int|null $explicitCategoryId User explicitly selected category ID in admin
     * @return Category
     */
    public function resolveOrCreateCategory(?string $incomingCategory, ?string $productTitle = null, ?int $explicitCategoryId = null): Category
    {
        // 1. If explicit category provided, verify there is no egregious category mismatch before returning
        if (!empty($explicitCategoryId)) {
            $explicit = Category::find($explicitCategoryId);
            if ($explicit) {
                $explicitNameLower = strtolower($explicit->name . ' ' . $explicit->slug);
                $titleLower = strtolower((string)$productTitle);

                // Sanity guard: Do not force lamps, lights, Christmas items, or home decor into watches or wearables
                $isWatchCategory = Str::contains($explicitNameLower, ['watch', 'wearable', 'smartwatch']);
                $hasDecorKeywords = Str::contains($titleLower, ['christmas', 'decor', 'decoration', 'lamp', 'light', 'night light', 'tree', 'lantern', 'candle', 'curtain', 'garland', 'ornament', 'pendant']);

                if (!($isWatchCategory && $hasDecorKeywords)) {
                    return $explicit;
                }
                Log::warning("[CategoryResolver] Overriding mismatched watch category '{$explicit->name}' for decor item '{$productTitle}'");
            }
        }

        $cleanIncoming = trim((string)$incomingCategory);
        $cleanTitle = trim((string)$productTitle);

        // 2. If CJ provides hierarchical breadcrumb string like "Consumer Electronics > Smart Electronics > Smart Watches"
        if (str_contains($cleanIncoming, '>')) {
            $parts = array_map('trim', explode('>', $cleanIncoming));
            $parts = array_filter($parts);
            
            if (!empty($parts)) {
                $rawSub = end($parts);
                $rawParent = count($parts) > 1 ? reset($parts) : null;

                return $this->resolveSubcategoryUnderParent($rawSub, $rawParent, $cleanTitle);
            }
        }

        // 3. Direct match on existing subcategories or categories
        if (!empty($cleanIncoming) && !in_array(strtolower($cleanIncoming), ['general', 'other', 'uncategorized', 'gadget', 'gadgets'])) {
            $existing = Category::where('name', $cleanIncoming)
                ->orWhere('slug', Str::slug($cleanIncoming))
                ->first();

            if ($existing) {
                // If it's already a subcategory (has parent_id), return it directly
                if ($existing->parent_id !== null) {
                    return $existing;
                }
                
                // If it's a parent category, find or create a general subcategory under it
                $defaultChild = $existing->children()->first();
                if ($defaultChild) {
                    return $defaultChild;
                }
            }

            // It's a new category name from CJ! Find appropriate parent and auto-create
            return $this->resolveSubcategoryUnderParent($cleanIncoming, null, $cleanTitle);
        }

        // 4. Keyword-based matching from Title and Description
        return $this->resolveByKeywords($cleanTitle);
    }

    /**
     * Given a subcategory name and optional parent name, resolve existing or auto-create hierarchy.
     */
    public function resolveSubcategoryUnderParent(string $subName, ?string $parentName = null, ?string $titleContext = null): Category
    {
        $subSlug = Str::slug($subName);
        if (empty($subSlug)) {
            return $this->resolveByKeywords($titleContext);
        }

        // Check if this subcategory already exists
        $existingSub = Category::where('slug', $subSlug)
            ->orWhere('name', 'LIKE', $subName)
            ->first();

        if ($existingSub) {
            // Ensure it has a parent if not currently set
            if ($existingSub->parent_id === null) {
                $parent = $this->determineParentCategory($parentName, $subName, $titleContext);
                if ($parent && $parent->id !== $existingSub->id) {
                    $existingSub->update(['parent_id' => $parent->id]);
                }
            }
            return $existingSub;
        }

        // Find or create the parent category
        $parent = $this->determineParentCategory($parentName, $subName, $titleContext);

        // Auto-create the subcategory under this parent
        $slug = $subSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $subSlug . '-' . $counter++;
        }

        $newCategory = Category::create([
            'name' => Str::title(str_replace(['-', '_'], ' ', $subName)),
            'slug' => $slug,
            'parent_id' => $parent ? $parent->id : null,
            'description' => "Shop curated " . Str::title($subName) . " at AtoZGadgets.",
            'status' => 'active'
        ]);

        Log::info("[CategoryResolver] Dynamically auto-created subcategory '{$newCategory->name}' (ID: {$newCategory->id}) under parent '" . ($parent ? $parent->name : 'ROOT') . "'");

        return $newCategory;
    }

    /**
     * Determine the best parent category for a subcategory or keyword context.
     */
    public function determineParentCategory(?string $parentName, ?string $subName, ?string $titleContext): Category
    {
        $textToAnalyze = strtolower(implode(' ', array_filter([$parentName, $subName, $titleContext])));

        // Check each parent taxonomy keywords
        $bestParentName = 'Electronics & Gadgets'; // autoritative default
        $highestScore = 0;

        foreach ($this->taxonomyMap as $pName => $pData) {
            $score = 0;
            
            // Check direct parent name match
            if ($parentName && Str::contains(strtolower($parentName), strtolower($pName))) {
                $score += 10;
            }

            // Check parent keywords
            foreach ($pData['keywords'] as $kw) {
                if (Str::contains($textToAnalyze, $kw)) {
                    $score += 2;
                }
            }

            // Check subcategory keyword matches
            foreach ($pData['subcategories'] as $sName => $sKeywords) {
                if ($subName && Str::contains(strtolower($subName), strtolower($sName))) {
                    $score += 8;
                }
                foreach ($sKeywords as $skw) {
                    if (Str::contains($textToAnalyze, $skw)) {
                        $score += 3;
                    }
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestParentName = $pName;
            }
        }

        $parentData = $this->taxonomyMap[$bestParentName] ?? reset($this->taxonomyMap);
        $parentSlug = $parentData['slug'] ?? Str::slug($bestParentName);

        return Category::firstOrCreate(
            ['slug' => $parentSlug],
            [
                'name' => $bestParentName,
                'parent_id' => null,
                'description' => "Explore {$bestParentName} at AtoZGadgets.",
                'status' => 'active'
            ]
        );
    }

    /**
     * Resolve category strictly through title keyword scoring.
     */
    public function resolveByKeywords(?string $title): Category
    {
        $haystack = strtolower((string)$title);

        $bestSubName = null;
        $bestParentName = null;
        $highestScore = 0;

        foreach ($this->taxonomyMap as $pName => $pData) {
            foreach ($pData['subcategories'] as $sName => $keywords) {
                $score = 0;
                foreach ($keywords as $kw) {
                    if (str_contains($kw, ' ')) {
                        // Multi-word phrase match (e.g. "camera drone", "smart watch") gets massive boost
                        if (str_contains($haystack, $kw)) {
                            $score += (strlen($kw) * 5) + 30;
                        }
                    } else {
                        // Single-word match with word boundary check
                        if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $haystack)) {
                            $score += (strlen($kw) * 2) + 10;
                        } elseif (str_contains($haystack, $kw)) {
                            $score += strlen($kw);
                        }
                    }
                }

                if ($score > $highestScore) {
                    $highestScore = $score;
                    $bestSubName = $sName;
                    $bestParentName = $pName;
                }
            }
        }

        // If no strong keyword match found, default to Novelty & Creative Gifts or Gadgets
        if (!$bestSubName || $highestScore === 0) {
            $bestParentName = 'Electronics & Gadgets';
            $bestSubName = 'Novelty & Creative Gifts';
        }

        return $this->resolveSubcategoryUnderParent($bestSubName, $bestParentName, $title);
    }
}
