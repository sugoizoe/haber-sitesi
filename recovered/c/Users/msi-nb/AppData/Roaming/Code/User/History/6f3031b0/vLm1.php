<?php
session_start();
require_once __DIR__ . '/../db.php';

unset($_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Try to authenticate against `admins` table if it exists
    $admin = false;
    try {
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
    } catch (PDOException $e) {
        // admins table might not exist yet — we'll fallback to config
        $admin = false;
    }

    if ($admin) {
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['is_admin'] = true;
            header('Location: index.php');
            exit;
        }
    }

    // Fallback: check optional admin credentials from config (config.php or config.sample.php)
    if (isset($admin_user, $admin_pass) && $username === ($admin_user ?? '') && $password === ($admin_pass ?? '')) {
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    }

    $_SESSION['error'] = 'Kullanıcı adı veya şifre yanlış';
    header('Location: ../login.php');
    exit;
}

header('Location: ../login.php');
exit;
?>