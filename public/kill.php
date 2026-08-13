<?php
// Hostinger Emergency Process Killer
// SECURITY: Localhost-only. On Hostinger, REMOTE_ADDR is the real TCP peer.
$allowed = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed)) {
    http_response_code(403);
    die('Forbidden');
}

$output = shell_exec('ps -u ' . escapeshellarg(get_current_user()));
echo "<pre>" . htmlspecialchars((string)$output) . "</pre>";

if (isset($_GET['pid']) && ctype_digit($_GET['pid'])) {
    posix_kill((int)$_GET['pid'], 9);
    echo "Killed PID " . htmlspecialchars($_GET['pid']);
} else {
    echo "Use ?pid=1234 to kill a specific process.";
}
