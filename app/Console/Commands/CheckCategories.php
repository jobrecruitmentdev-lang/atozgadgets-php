<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;

class CheckCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:check-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect categories, parent-child hierarchy, and product counts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $parents = Category::whereNull('parent_id')->with(['children.products', 'products'])->get();
        $this->info("Total Categories: " . Category::count() . " | Total Products: " . Product::count());

        $tableData = [];
        foreach ($parents as $p) {
            $descendantCount = Product::whereIn('category_id', $p->getAllDescendantIds())->count();
            $tableData[] = [
                'ID' => $p->id,
                'Level' => 'ROOT',
                'Name' => $p->name,
                'Slug' => $p->slug,
                'Direct Products' => $p->products->count(),
                'Total with Children' => $descendantCount
            ];

            foreach ($p->children as $c) {
                $tableData[] = [
                    'ID' => $c->id,
                    'Level' => '  ↳ SUB',
                    'Name' => "  ↳ {$c->name}",
                    'Slug' => $c->slug,
                    'Direct Products' => $c->products->count(),
                    'Total with Children' => $c->products->count()
                ];
            }
        }

        // Also check if any orphaned categories exist with invalid parent_id
        $orphaned = Category::whereNotNull('parent_id')
            ->whereNotIn('parent_id', Category::pluck('id'))
            ->get();

        if ($orphaned->count() > 0) {
            $this->warn("Found {$orphaned->count()} orphaned categories!");
        }

        $this->table(['ID', 'Level', 'Name', 'Slug', 'Direct Products', 'Total with Children'], $tableData);

        return 0;
    }
}
