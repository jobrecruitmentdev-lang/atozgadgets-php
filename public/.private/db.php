<?php

// .private/db.php for AtoZGadgets-PHP
// Parses root .env to create PDO database handle with automatic retry logic

$envConfig = [];
$envPath = __DIR__ . '/../../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $envConfig[$name] = $value;
        }
    }
}

$connection = $envConfig['DB_CONNECTION'] ?? 'mysql';
$host   = $envConfig['DB_HOST'] ?? '127.0.0.1';
$dbname = $envConfig['DB_DATABASE'] ?? 'atoz_gadgets_db';
$user   = $envConfig['DB_USERNAME'] ?? 'root';
$pass   = $envConfig['DB_PASSWORD'] ?? '';

if ($connection === 'sqlite') {
    $dsn = "sqlite:" . database_path('database.sqlite');
} else {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
}

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_PERSISTENT         => false,
];

$pdo        = null;
$maxRetries = 3;
$lastError  = null;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    try {
        if ($connection === 'sqlite') {
            $pdo = new PDO("sqlite:" . __DIR__ . "/../../database/database.sqlite");
        } else {
            $pdo = new PDO($dsn, $user, $pass, $pdoOptions);
        }
        break;
    } catch (Throwable $e) {
        $lastError = $e;
        if ($attempt < $maxRetries) {
            usleep(200000 * $attempt);
        }
    }
}

if ($pdo === null) {
    // Return empty fallback PDO or log gracefully
    try {
        $pdo = new PDO("sqlite::memory:", null, null, $pdoOptions);
    } catch (Throwable $e) {
        // Fallback null handle
    }
}
