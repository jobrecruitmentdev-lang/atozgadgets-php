<?php
$token = \App\Services\Cj\CjAuthService::getAccessToken();
$res = \Illuminate\Support\Facades\Http::withHeaders(['CJ-Access-Token' => $token])->get('https://developers.cjdropshipping.com/api2.0/v1/product/listV2', ['keyWord' => 'watch', 'page' => 1, 'size' => 5]);
echo json_encode($res->json());
