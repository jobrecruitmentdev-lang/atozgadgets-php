<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::orderBy('id', 'desc')->first();
echo "Latest User ID: " . $user->id . "\n";

// We don't know the plain text password for this user, but we can override it!
$plainPassword = 'password123';
$user->password = \Illuminate\Support\Facades\Hash::make($plainPassword);
$user->save();

echo "Saved new password hash: " . $user->password_hash . "\n";

$credentials = [
    'email' => $user->email,
    'password' => $plainPassword
];

$attempt = \Illuminate\Support\Facades\Auth::attempt($credentials);
echo "Auth::attempt result: " . ($attempt ? 'SUCCESS' : 'FAILED') . "\n";
