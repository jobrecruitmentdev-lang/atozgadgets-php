<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Models\CjProduct;
use App\Services\Catalog\CategoryResolverService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OrganizeCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:organize-categories {--force : Apply category updates without prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Organize all catalog products into clean 1-Parent to Multiple-Subcategory hierarchy with zero data loss';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CategoryResolverService $resolver)
    {
        $this->info("🚀 Starting AtoZGadgets Catalog Category Organization...");
        $this->info("------------------------------------------------------------");

        // Step 1: Ensure Core Taxonomy (Root Categories + Standard Subcategories)
        $this->info("1/3 Ensuring authoritative Root and Subcategory hierarchy exists...");
        $resolver->ensureCoreTaxonomy();

        $totalCategories = Category::count();
        $rootCategories = Category::whereNull('parent_id')->count();
        $subCategories = Category::whereNotNull('parent_id')->count();
        $this->line("   ✓ Total Categories: {$totalCategories} ({$rootCategories} Root Parents, {$subCategories} Subcategories)");

        // Step 2: Query all products and categorize safely
        $totalProducts = Product::count();
        $this->info("2/3 Processing {$totalProducts} existing catalog products...");

        $products = Product::with('cjProduct')->get();
        $updatedCount = 0;
        $alreadyMappedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($products as $product) {
                $rawCjCategory = $product->cjProduct->category_name ?? null;
                $productTitle = $product->name;

                // Resolve exact subcategory using CategoryResolverService
                $resolvedCategory = $resolver->resolveOrCreateCategory(
                    $rawCjCategory,
                    $productTitle,
                    null // Don't enforce old category_id so we fix misplaced products
                );

                if ($product->category_id !== $resolvedCategory->id) {
                    $product->update([
                        'category_id' => $resolvedCategory->id
                    ]);
                    $updatedCount++;
                } else {
                    $alreadyMappedCount++;
                }
            }

            DB::commit();
            $this->info("   ✓ Re-categorized: {$updatedCount} products");
            $this->info("   ✓ Already in correct category: {$alreadyMappedCount} products");

            // Deactivate legacy or empty root categories that have 0 total products across all descendants
            Category::whereIn('slug', ['watch', 'electronics', 'tech', 'tech-test-slug', 'mobile-phones'])
                ->update(['status' => 'inactive']);

            foreach (Category::whereNull('parent_id')->get() as $rootCat) {
                if (\App\Models\Product::whereIn('category_id', $rootCat->getAllDescendantIds())->count() === 0) {
                    $rootCat->update(['status' => 'inactive']);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error organizing products: " . $e->getMessage());
            return 1;
        }

        // Step 3: Print Category Breakdown Table
        $this->info("3/3 Category Hierarchy & Product Distribution Summary:");
        $parents = Category::whereNull('parent_id')->with(['children.products', 'products'])->get();
        
        $tableData = [];
        foreach ($parents as $parent) {
            $totalDescendantCount = count($parent->getAllDescendantIds()) > 1 
                ? Product::whereIn('category_id', $parent->getAllDescendantIds())->count()
                : $parent->products->count();

            $tableData[] = [
                'Type' => 'ROOT (Parent)',
                'Name' => $parent->name,
                'Slug' => $parent->slug,
                'Subcategories' => $parent->children->count(),
                'Total Products' => $totalDescendantCount,
            ];

            foreach ($parent->children as $child) {
                $tableData[] = [
                    'Type' => '  ↳ Subcategory',
                    'Name' => "  ↳ {$child->name}",
                    'Slug' => $child->slug,
                    'Subcategories' => '-',
                    'Total Products' => $child->products->count(),
                ];
            }
        }

        $this->table(['Type', 'Category Name', 'Slug', 'Subcategories', 'Products'], $tableData);

        // Step 4: Clear all caches
        Artisan::call('optimize:clear');
        $this->info("✅ Catalog categories organized successfully and system caches purged!");

        return 0;
    }
}
