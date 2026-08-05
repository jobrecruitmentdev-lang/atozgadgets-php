<?php
// Hostinger Emergency Process Killer
// Used to kill zombie processes if NPROC (Process limit) is reached.
$output = shell_exec('ps -u ' . get_current_user());
echo "<pre>$output</pre>";

// To kill a specific process, you can uncomment and modify:
// posix_kill((int)$_GET['pid'], 9);
echo "Use ?pid=1234 to kill a specific process if needed.";
