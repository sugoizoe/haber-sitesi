<?php
session_start();
require_once '../db.php'; 


unset($_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === '12345') {
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Kullanıcı adı veya şifre yanlış';
        header('Location: ../login.php');
        exit;
    }
}

header('Location: ../login.php');
exit;
?>