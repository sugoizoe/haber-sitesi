<?php
// Processes admin login POST and redirects accordingly
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Hard-coded credentials for course project (change for production)
if ($username === 'admin' && $password === '12345') {
    // Regenerate session id to be safer
    session_regenerate_id(true);
    $_SESSION['is_admin'] = true;
    header('Location: index.php');
    exit;
} else {
    $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
    header('Location: login.php');
    exit;
}
