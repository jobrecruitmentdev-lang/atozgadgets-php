<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cat = \App\Models\Category::where('slug', 'earphone')->first();
if ($cat) {
    try {
        $cat->delete();
        echo "Deleted category\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Category not found\n";
}
