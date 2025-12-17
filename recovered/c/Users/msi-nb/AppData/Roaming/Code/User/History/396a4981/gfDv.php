<?php
// Usage: php scripts/add_admin.php username password
if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

require_once __DIR__ . '/../db.php';

$username = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$username || !$password) {
    echo "Usage: php scripts/add_admin.php username password\n";
    exit(1);
}

try {
    $stmt = $pdo->prepare('SELECT id FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "Admin user already exists: $username\n";
        exit(1);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $ins->execute([$username, $hash]);

    echo "Admin created: $username\n";
    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(2);
}
