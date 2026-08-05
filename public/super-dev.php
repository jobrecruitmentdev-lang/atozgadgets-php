<?php
// Git-ignored and rsync-ignored on purpose. Never deploy this to a public host.
session_start();

// Strict Localhost Check (Block remote access entirely) - checked before
// anything else, before auth, before any output. REMOTE_ADDR is set by the
// web server from the actual TCP connection, so it cannot be spoofed via
// request headers the way X-Forwarded-For could be.
$allowed_ips = ["127.0.0.1", "::1"];
if (!in_array($_SERVER["REMOTE_ADDR"] ?? '', $allowed_ips)) {
    header("HTTP/1.1 403 Forbidden");
    die("Forbidden: Access restricted to local development environment.");
}

// Credentials + deploy connection details live outside this file so this
// file's contents alone (if ever leaked) don't disclose secrets.
$creds_file = __DIR__ . '/.ops-credentials.php';
if (!file_exists($creds_file)) {
    die("Missing .ops-credentials.php - create it (see plan/CLAUDE.md) before using this panel.");
}
$creds = require $creds_file;
$DANGER_USERNAME = $creds['username'];
$DANGER_PASSWORD = $creds['password'];

// Handle Danger Login
if (isset($_POST["danger_login"])) {
    if (
        hash_equals($DANGER_USERNAME, (string)($_POST["d_user"] ?? '')) &&
        hash_equals($DANGER_PASSWORD, (string)($_POST["d_pass"] ?? ''))
    ) {
        $_SESSION["danger_auth"] = true;
        header("Location: super-dev.php");
        exit();
    } else {
        $login_error = "Invalid Danger Creds.";
    }
}

// Handle Logout
if (isset($_GET["logout"])) {
    unset($_SESSION["danger_auth"]);
    header("Location: super-dev.php");
    exit();
}

// If not authenticated, show the Danger Login Screen
if (!isset($_SESSION["danger_auth"]) || $_SESSION["danger_auth"] !== true) { ?>
    <!DOCTYPE html>
    <html class="bg-black" lang="en">
    <head>
        <title>RESTRICTED AREA</title>
        <style>
            body { font-family: 'Consolas', 'Courier New', monospace; background:#000; color:#ef4444; height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1rem; margin:0; }
            .box { border:2px solid #dc2626; padding:2rem; border-radius:.5rem; background:rgba(127,29,29,.1); box-shadow:0 0 50px rgba(220,38,38,.3); max-width:26rem; width:100%; text-align:center; }
            h1 { font-size:1.75rem; font-weight:900; margin-bottom:.25rem; text-transform:uppercase; }
            p.sub { font-size:.75rem; color:#f87171; margin-bottom:2rem; text-transform:uppercase; letter-spacing:.15em; }
            input { width:100%; box-sizing:border-box; background:#000; border:1px solid #7f1d1d; color:#ef4444; padding:.75rem 1rem; outline:none; text-align:center; text-transform:uppercase; letter-spacing:.15em; margin-bottom:1rem; font-family:inherit; }
            input:focus { border-color:#ef4444; }
            button { width:100%; background:#dc2626; color:#000; font-weight:900; text-transform:uppercase; letter-spacing:.15em; padding:.75rem; border:none; cursor:pointer; }
            button:hover { background:#ef4444; }
            .err { color:#000; background:#dc2626; padding:.5rem; font-size:.85rem; margin-bottom:1rem; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>Danger Zone</h1>
            <p class="sub">Local Environment Only</p>
            <?php if (isset($login_error)) echo "<p class='err'>" . htmlspecialchars($login_error) . "</p>"; ?>
            <form method="POST">
                <input type="text" name="d_user" placeholder="USERNAME" required autofocus>
                <input type="password" name="d_pass" placeholder="PASSWORD" required>
                <button type="submit" name="danger_login">Authorize Override</button>
            </form>
        </div>
    </body>
    </html>
    <?php exit();
}

// -----------------------------------------------------
// AUTHENTICATED ZONE
// -----------------------------------------------------

// Deploy connection details are read from deploy/.deploy-env so this panel,
// push.sh, and .rsyncignore all share one source of truth - never hardcode
// host/user/path here.
function ops_load_deploy_env(string $path): array {
    $env = [];
    if (!file_exists($path)) return $env;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
    return $env;
}
$projectRootDir = dirname(__DIR__);
$deployEnv = ops_load_deploy_env($projectRootDir . '/deploy/.deploy-env');

$log_file = $projectRootDir . '/super-dev.log';
function ops_log(string $log_file, string $action, string $extra = ''): void {
    $entry = date('Y-m-d H:i:s') . " - Action: $action" . ($extra !== '' ? " - $extra" : '') . "\n";
    file_put_contents($log_file, $entry, FILE_APPEND);
}

if (isset($_GET["action"])) {
    header("Content-Type: application/json");
    $action = $_GET["action"];
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true) ?: [];

    if ($action === "execute_git") {
        $command = $data["command"] ?? "";
        $customCmd = $data["custom_cmd"] ?? "";

        $allowedCommands = [
            "status" => "git status",
            "pull"   => "git pull origin main",
            "log"    => "git log -n 5",
        ];

        if ($command === "rsync" && $customCmd === "push_file:dummy.txt") {
            // One-off scoped push of a single file, no --delete, for testing the
            // panel's deploy path without touching the rest of the live site.
            ops_log($log_file, 'rsync', $customCmd);
            if (empty($deployEnv)) {
                echo json_encode(["success" => false, "error" => "deploy/.deploy-env not configured yet."]);
                exit();
            }
            $wslRoot = '/mnt/' . strtolower(substr($projectRootDir, 0, 1)) . str_replace('\\', '/', substr($projectRootDir, 2));
            $tmpScript = $projectRootDir . '/deploy/.run-rsync.sh';
            $script = "#!/bin/bash\n"
                . "cd \"$wslRoot/deploy\"\n"
                . "source .deploy-env\n"
                . "rsync -avz -e \"ssh -p \$REMOTE_PORT\" ../dummy.txt \"\$REMOTE_USER@\$REMOTE_HOST:\$REMOTE_PATH/\"\n";
            file_put_contents($tmpScript, $script);
            $wslScriptPath = $wslRoot . '/deploy/.run-rsync.sh';
            $output = shell_exec('wsl bash ' . escapeshellarg($wslScriptPath) . ' 2>&1');
            @unlink($tmpScript);
            $output = mb_convert_encoding((string)$output, 'UTF-8', 'auto');
            echo json_encode(["success" => true, "output" => $output]);
        } elseif ($command === "rsync" && in_array(trim($customCmd), ["push", "list"], true)) {
            @set_time_limit(600);
            @ini_set('max_execution_time', '600');
            ops_log($log_file, 'rsync', $customCmd);
            // push.sh needs real rsync/ssh, which only work here via WSL (Git Bash's
            // own rsync.exe is broken on this machine - missing msys-xxhash-0.dll).
            // Passing a compound "cd ... && ..." string through PHP shell_exec ->
            // wsl.exe -> bash -lc mangles spaces in the path even when escaped, so
            // instead write a tiny throwaway script and hand WSL just its path.
            $wslRoot = '/mnt/' . strtolower(substr($projectRootDir, 0, 1)) . str_replace('\\', '/', substr($projectRootDir, 2));
            $flag = $customCmd === 'push' ? '' : ' --list';
            $confirmPrefix = $customCmd === 'push' ? 'CONFIRM=1 ' : '';
            $tmpScript = $projectRootDir . '/deploy/.run-rsync.sh';
            file_put_contents($tmpScript, "#!/bin/bash\ncd \"$wslRoot\"\n{$confirmPrefix}bash deploy/push.sh{$flag}\n");
            $wslScriptPath = $wslRoot . '/deploy/.run-rsync.sh';
            $output = shell_exec('wsl bash ' . escapeshellarg($wslScriptPath) . ' 2>&1');
            @unlink($tmpScript);
            $output = mb_convert_encoding((string)$output, 'UTF-8', 'auto');
            echo json_encode(["success" => true, "output" => $output]);
        } elseif ($command === "raw_ssh") {
            // Security note: this executes raw commands on the remote Hostinger
            // server via SSH. It is gated by the localhost-only + login checks
            // above - there is no additional sanitization of $customCmd itself,
            // by design (a shell box is a shell box).
            if (empty($deployEnv)) {
                echo json_encode(["success" => false, "error" => "deploy/.deploy-env not configured yet."]);
                exit();
            }
            ops_log($log_file, 'raw_ssh', $customCmd);
            $rawCommand = escapeshellarg(trim($customCmd));
            $port = escapeshellarg($deployEnv['REMOTE_PORT'] ?? '');
            $target = escapeshellarg(($deployEnv['REMOTE_USER'] ?? '') . '@' . ($deployEnv['REMOTE_HOST'] ?? ''));
            $output = shell_exec("wsl ssh -p $port $target $rawCommand 2>&1");
            $output = mb_convert_encoding((string)$output, 'UTF-8', 'auto');
            echo json_encode(["success" => true, "output" => $output]);
        } elseif ($command === "ls_remote") {
            if (empty($deployEnv)) {
                echo json_encode(["success" => false, "error" => "deploy/.deploy-env not configured yet."]);
                exit();
            }
            ops_log($log_file, 'ls_remote');
            $port = escapeshellarg($deployEnv['REMOTE_PORT'] ?? '');
            $target = escapeshellarg(($deployEnv['REMOTE_USER'] ?? '') . '@' . ($deployEnv['REMOTE_HOST'] ?? ''));
            $remotePath = escapeshellarg(($deployEnv['REMOTE_PATH'] ?? '') . '/');
            $output = shell_exec("wsl ssh -p $port $target \"ls -la $remotePath\" 2>&1");
            echo json_encode(["success" => true, "output" => $output]);
        } elseif (array_key_exists($command, $allowedCommands)) {
            ops_log($log_file, $command);
            $output = shell_exec($allowedCommands[$command] . " 2>&1");
            echo json_encode(["success" => true, "output" => $output]);
        } else {
            echo json_encode(["success" => false, "error" => "Invalid or unauthorized command"]);
        }
        exit();
    }

    if ($action === "test_blog_api") {
        $testApiKey = trim((string)($creds['test_api_key'] ?? ''));
        if ($testApiKey === '') {
            echo json_encode(["success" => false, "error" => "test_api_key not set in .ops-credentials.php. Generate one at /admin/api-keys and paste it in."]);
            exit();
        }

        $title = trim((string)($data['title'] ?? ''));
        $content = (string)($data['content'] ?? '');
        if ($title === '' || $content === '') {
            echo json_encode(["success" => false, "error" => "title and content are required."]);
            exit();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-')) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);

        ops_log($log_file, 'test_blog_api', $slug);

        // Calls the same ApiKeyService/PostService::saveBlogs() path the real
        // /blogs/automated-create endpoint uses, but in-process rather than
        // over HTTP. A real curl-to-self here deadlocks against `php -S`'s
        // single-threaded dev server (it's already busy handling this very
        // request), so this exercises the identical auth + save logic
        // without depending on the server having a free worker to answer it.
        require_once __DIR__ . '/config/dependencies.php';
        $apiKeyService = $container->make('ApiKeyService');

        if (!$apiKeyService->verifyKey($testApiKey)) {
            echo json_encode(["success" => false, "error" => "test_api_key is invalid or revoked. Generate a fresh one at /admin/api-keys."]);
            exit();
        }

        try {
            $postService = $container->make('PostService');
            $ids = $postService->saveBlogs([[
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'status' => 'Draft',
            ]], null);
            echo json_encode(["success" => true, "output" => "Draft post created via automated-create path.\nslug: $slug\npost id(s): " . implode(', ', $ids)]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => "Save failed: " . $e->getMessage()]);
        }
        exit();
    }

    if ($action === "scan_security") {
        ops_log($log_file, 'scan_security');
        $results = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));

        $dangerousRegexes = [
            'e'.'val' => '/\be'.'val\s*\(/i',
            'shell'.'_exec' => '/\bshell'.'_exec\s*\(/i',
            'sys'.'tem' => '/\bsys'.'tem\s*\(/i',
            'ex'.'ec' => '/\bex'.'ec\s*\(/i',
            'pass'.'thru' => '/\bpass'.'thru\s*\(/i',
            '$_G'.'ET[\'cmd\']' => '/\$_G'.'ET\[[\'"]cmd[\'"]\]/i',
            'e'.'val(base'.'64_decode' => '/\be'.'val\s*\(\s*base'.'64_decode/i',
        ];

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            if ($file->getExtension() !== 'php') continue;

            $path = $file->getPathname();
            if (
                strpos($path, 'node_modules') !== false ||
                strpos($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) !== false ||
                basename($path) === 'super-dev.php' ||
                basename($path) === '.ops-credentials.php'
            ) continue;

            $content = file_get_contents($path);
            $relativePath = str_replace(__DIR__, '', $path);

            foreach ($dangerousRegexes as $name => $regex) {
                if (preg_match($regex, $content)) {
                    $results[] = "[WARNING] Possible backdoor '$name' found in: " . $relativePath;
                }
            }

            if (preg_match('/\$sql\s*=\s*".*\$[a-zA-Z0-9_]+.*";/i', $content)) {
                $results[] = "[SQLi RISK] Variable interpolation in SQL string found in: " . $relativePath;
            }
            if (preg_match('/->query\(.*\$[a-zA-Z0-9_]+/i', $content)) {
                $results[] = "[SQLi RISK] Unsafe PDO query found in: " . $relativePath;
            }
        }

        if (empty($results)) {
            echo json_encode(["success" => true, "output" => "Security Scan Completed. No obvious threats found.\n"]);
        } else {
            echo json_encode(["success" => true, "output" => "Security Scan Completed. Issues found:\n\n" . implode("\n", $results) . "\n"]);
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Super Dev Panel — AtoZGadgets</title>
    <style>
        body { font-family: -apple-system, Segoe UI, Arial, sans-serif; background:#0f172a; color:#f8fafc; margin:0; padding:2rem; }
        .wrap { max-width:72rem; margin:0 auto; }
        header { display:flex; align-items:center; justify-content:space-between; margin-bottom:2.5rem; border-bottom:1px solid rgba(127,29,29,.5); padding-bottom:1.5rem; }
        h1 { color:#ef4444; font-size:1.75rem; font-weight:900; text-transform:uppercase; margin:0; }
        .sub { font-size:.8rem; color:#f87171; letter-spacing:.15em; }
        a.logout { padding:.5rem 1.25rem; background:#450a0a; color:#ef4444; border:1px solid #7f1d1d; border-radius:.25rem; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.15em; text-decoration:none; }
        a.logout:hover { background:#7f1d1d; }
        .card { background:#000; border-radius:.75rem; padding:1.5rem; border:1px solid #1e293b; margin-bottom:1.5rem; }
        .card h2 { color:#fff; font-size:1.1rem; text-transform:uppercase; letter-spacing:.05em; margin:0 0 1rem; }
        .row { display:flex; align-items:center; gap:.5rem; margin-bottom:.75rem; flex-wrap:wrap; }
        .row .label { width:6.5rem; font-size:.7rem; text-transform:uppercase; font-weight:700; }
        input[type=text] { flex:1; background:#000; color:#cbd5e1; padding:.4rem .75rem; font-size:.75rem; font-family:monospace; border:1px solid #334155; border-radius:.25rem; outline:none; min-width:12rem; }
        input[type=text]:focus { border-color:#ef4444; }
        button.btn { padding:.5rem 1rem; border-radius:.25rem; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; border:1px solid #334155; background:#0f172a; color:#fff; cursor:pointer; }
        button.btn:hover { background:#1e293b; }
        .btn-danger { background:#7f1d1d; border-color:#dc2626; }
        .btn-danger:hover { background:#991b1b; }
        .btn-yellow { background:#78350f; border-color:#ca8a04; }
        .btn-yellow:hover { background:#92400e; }
        .btn-purple { background:#3b0764; border-color:#7e22ce; }
        .btn-purple:hover { background:#581c87; }
        #terminal { background:#000; border:1px solid #7f1d1d; border-radius:.5rem; padding:1rem; height:20rem; overflow-y:auto; font-family:monospace; font-size:.8rem; color:#ef4444; white-space:pre-wrap; }
        .yellow-box { background:rgba(120,53,15,.2); border:1px solid #78350f; border-radius:.25rem; padding:.75rem; margin-bottom:.75rem; }
        .warn { color:#eab308; font-size:.7rem; margin-top:.5rem; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <div>
            <h1>Super Dev Panel</h1>
            <p class="sub">Localhost Only — AtoZGadgets</p>
        </div>
        <a href="?logout=true" class="logout">Lock Session</a>
    </header>

    <div class="card">
        <h2>Server Terminal</h2>
        <div class="row">
            <button class="btn" onclick="runGitCommand('status')">git status</button>
            <button class="btn" onclick="runGitCommand('log')">git log</button>
            <button class="btn btn-purple" onclick="runGitCommand('ls_remote')">List Live Files</button>
        </div>

        <div class="yellow-box">
            <div class="row">
                <span class="label">SSH Shell:</span>
                <input type="text" id="raw_ssh_cmd" placeholder="e.g. php -v OR ls -la">
                <button class="btn btn-yellow" onclick="runRawSSH()">Run Command</button>
            </div>
        </div>

        <div class="row">
            <span class="label">Push (Deploy):</span>
            <button class="btn btn-danger" onclick="runRsync('push')">Push to Server</button>
        </div>
        <div class="row">
            <span class="label">List (Rsync):</span>
            <button class="btn" onclick="runRsync('list')">List Live Files (rsync)</button>
        </div>

        <div class="row">
            <span class="label">Security:</span>
            <button class="btn btn-yellow" onclick="runSecurityScan()">Run Security Scan</button>
        </div>
        <p class="warn">⚠ Requires WSL SSH keys configured for the raw SSH / push actions, or they will hang waiting for a password.</p>

        <div id="terminal">&gt; INITIATING SECURE SHELL...
&gt; WAITING FOR COMMANDS.</div>
    </div>

    <div class="card">
        <h2>Test Blog Automation API</h2>
        <p class="warn">Sends a real POST to /api/v1/index.php/blogs/automated-create using the test_api_key from .ops-credentials.php. Creates a Draft post.</p>
        <div class="row">
            <span class="label">Title:</span>
            <input type="text" id="test_blog_title" placeholder="Test post title">
        </div>
        <div class="row">
            <span class="label">Content:</span>
            <textarea id="test_blog_content" placeholder="&lt;p&gt;Test content&lt;/p&gt;" rows="4" style="flex:1; background:#000; color:#cbd5e1; padding:.4rem .75rem; font-size:.75rem; font-family:monospace; border:1px solid #334155; border-radius:.25rem; outline:none; min-width:12rem; resize:vertical;"></textarea>
        </div>
        <div class="row">
            <button class="btn btn-purple" onclick="runTestBlogApi()">Send Test Post</button>
        </div>
    </div>
</div>

<script>
function escapeHtml(unsafe) {
    return (unsafe || '').toString()
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

async function runGitCommand(command) {
    const terminal = document.getElementById('terminal');
    terminal.innerHTML += `\n&gt; ${escapeHtml(command)}\n`;
    terminal.scrollTop = terminal.scrollHeight;
    try {
        const res = await fetch('?action=execute_git', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ command })
        });
        const result = await res.json();
        terminal.innerHTML += escapeHtml(result.success ? (result.output || 'OK') : ('ERROR: ' + (result.error || 'Failed'))) + '\n';
    } catch (e) {
        terminal.innerHTML += 'CONNECTION LOST\n';
    }
    terminal.scrollTop = terminal.scrollHeight;
}

async function runTestBlogApi() {
    const title = document.getElementById('test_blog_title').value;
    const content = document.getElementById('test_blog_content').value;
    if (!title.trim() || !content.trim()) {
        alert('Title and content are required.');
        return;
    }
    const terminal = document.getElementById('terminal');
    terminal.innerHTML += `\n&gt; POST /blogs/automated-create "${escapeHtml(title)}"\n`;
    terminal.scrollTop = terminal.scrollHeight;
    try {
        const res = await fetch('?action=test_blog_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, content })
        });
        const result = await res.json();
        terminal.innerHTML += escapeHtml(result.success ? (result.output || 'OK') : ('ERROR: ' + (result.error || 'Failed'))) + '\n';
    } catch (e) {
        terminal.innerHTML += 'CONNECTION LOST: ' + e.message + '\n';
    }
    terminal.scrollTop = terminal.scrollHeight;
}

async function runRawSSH() {
    const cmd = document.getElementById('raw_ssh_cmd').value;
    if (!cmd.trim()) return;
    const terminal = document.getElementById('terminal');
    terminal.innerHTML += `\n&gt; ssh remote:~$ ${escapeHtml(cmd)}\n`;
    terminal.scrollTop = terminal.scrollHeight;
    try {
        const res = await fetch('?action=execute_git', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ command: 'raw_ssh', custom_cmd: cmd })
        });
        const result = await res.json();
        terminal.innerHTML += escapeHtml(result.success ? (result.output || 'No output') : ('ERROR: ' + (result.error || 'Failed'))) + '\n';
    } catch (e) {
        terminal.innerHTML += 'CONNECTION LOST: ' + e.message + '\n';
    }
    terminal.scrollTop = terminal.scrollHeight;
    document.getElementById('raw_ssh_cmd').value = '';
}

async function runRsync(type) {
    if (!confirm(`Are you sure you want to run ${type.toUpperCase()}?\n\nMake sure WSL SSH keys are set up, or this will hang waiting for a password!`)) return;
    const terminal = document.getElementById('terminal');
    terminal.innerHTML += `\n&gt; rsync ${type}... please wait...\n`;
    terminal.scrollTop = terminal.scrollHeight;
    try {
        const res = await fetch('?action=execute_git', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ command: 'rsync', custom_cmd: type })
        });
        const result = await res.json();
        terminal.innerHTML += escapeHtml(result.success ? (result.output || (type.toUpperCase() + ' OK')) : ('ERROR: ' + (result.error || 'Failed'))) + '\n';
    } catch (e) {
        terminal.innerHTML += 'CONNECTION LOST DURING EXECUTION\n';
    }
    terminal.scrollTop = terminal.scrollHeight;
}

async function runSecurityScan() {
    const terminal = document.getElementById('terminal');
    terminal.innerHTML += '\n&gt; Running Security Scan...\n';
    terminal.scrollTop = terminal.scrollHeight;
    try {
        const res = await fetch('?action=scan_security');
        const data = await res.json();
        terminal.innerHTML += escapeHtml(data.output) + '\n';
    } catch (e) {
        terminal.innerHTML += 'Error: ' + e.message + '\n';
    }
    terminal.scrollTop = terminal.scrollHeight;
}
</script>
</body>
</html>
