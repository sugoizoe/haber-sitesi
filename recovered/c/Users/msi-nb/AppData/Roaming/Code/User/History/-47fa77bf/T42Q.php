<?php
// admin/login_process.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Hard-coded credentials for course project
if ($username === 'admin' && $password === '12345') {
    session_regenerate_id(true);
    $_SESSION['is_admin'] = true;
    header('Location: index.php');
    exit;
}

// On failure, set error and return to login
$_SESSION['error'] = 'Kullanıcı adı veya şifre yanlış';
header('Location: login.php');
exit;
