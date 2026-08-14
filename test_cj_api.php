<?php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = Illuminate\Support\Facades\Http::withHeaders([
    'CJ-Access-Token' => 'FAKE_TOKEN',
    'Content-Type' => 'application/json'
])->get('https://developers.cjdropshipping.com/api2.0/v1/product/list', [
    'productNameEn' => 'watch', 
    'pageNum' => 1, 
    'pageSize' => 10
]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
