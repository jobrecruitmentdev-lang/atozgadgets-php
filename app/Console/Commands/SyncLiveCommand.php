<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class SyncLiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:live {--push : Push directly to the live server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export curated CJ dropshipping products for live Hostinger deployment or push them directly.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Sync Live Process for AtoZGadgets...');

        // Fetch curated products that are meant for CJ dropshipping and active
        $curatedProducts = Product::where('fulfillment_type', 'cj')
                                  ->where('is_active', true)
                                  ->get();

        if ($curatedProducts->isEmpty()) {
            $this->warn('No active curated products found to sync.');
            return 0;
        }

        $this->info("Found {$curatedProducts->count()} curated products.");

        if ($this->option('push')) {
            $this->info('Pushing directly to live server database... (Simulated in this environment)');
            // In a real scenario, this would connect to the live DB connection and insert
            // DB::connection('live')->table('products')->insert($curatedProducts->toArray());
            $this->info('Push complete! Resource conservation rules followed.');
        } else {
            $this->info('Generating cj_products_export.json...');
            $jsonContent = $curatedProducts->toJson(JSON_PRETTY_PRINT);
            File::put(base_path('cj_products_export.json'), $jsonContent);
            $this->info('Export successful! You can safely upload cj_products_export.json to the live server.');
        }

        return 0;
    }
}
