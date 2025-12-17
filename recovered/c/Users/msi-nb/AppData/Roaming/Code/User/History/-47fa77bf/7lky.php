<?php
// admin/login_process.php
// Clean, all-PHP login processor — no stray HTML tags
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

// Hard-coded credentials for course project (replace with DB later)
if ($username === 'admin' && $password === '12345') {
    // Safer session handling
    session_regenerate_id(true);
    $_SESSION['is_admin'] = true; // matches auth.php and login.php
    header('Location: index.php');
    exit;
}

// Authentication failed
$_SESSION['error'] = 'Kullanıcı adı veya şifre yanlış';
header('Location: login.php');
exit;
