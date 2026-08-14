<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = \App\Services\Cj\CjAuthService::getAccessToken();
$res = \Illuminate\Support\Facades\Http::withHeaders(['CJ-Access-Token' => $token])
    ->get('https://developers.cjdropshipping.com/api2.0/v1/product/listV2', [
        'keyWord' => 'watch',
        'page' => 1,
        'size' => 5
    ]);
    
$data = $res->json();
echo json_encode($data, JSON_PRETTY_PRINT);
