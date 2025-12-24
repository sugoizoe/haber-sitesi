<?php
// Temporary script: reset Admin password to 12345
// WARNING: Delete this file after use.
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB bağlantı hatası: " . $e->getMessage();
    exit;
}

$username = 'admin';
$email = 'admin@example.com';
$newPass = '12345';
$hash = password_hash($newPass, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    // Check if admin user exists
    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $upd = $pdo->prepare('UPDATE admin_users SET password = :p, is_active = 1 WHERE id = :id');
        $upd->execute([':p' => $hash, ':id' => $existing['id']]);
        $message = "Güncellendi: $username / 12345";
    } else {
        $ins = $pdo->prepare('INSERT INTO admin_users (name, username, email, password, role, is_active) VALUES (:n, :u, :e, :p, "Admin", 1)');
        $ins->execute([
            ':n' => 'Admin',
            ':u' => $username,
            ':e' => $email,
            ':p' => $hash,
        ]);
        $message = "Oluşturuldu: $username / 12345";
    }

    $pdo->commit();
    echo $message . "\nLütfen girişte Yönetici/Editor seçeneğini kullanın.\n";
    echo "İşlem bittikten sonra reset-admin.php dosyasını silin.";
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Hata: " . $e->getMessage();
}
