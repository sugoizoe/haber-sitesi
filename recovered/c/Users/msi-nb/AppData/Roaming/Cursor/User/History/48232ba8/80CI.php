<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$page = $_GET['page'] ?? 'home';

// Sayfa yönlendirmeleri
switch ($page) {
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    case 'news':
        include 'pages/news_detail.php';
        break;
    case 'category':
        include 'pages/category.php';
        break;
    case 'admin':
        requireRole('editor');
        include 'pages/admin/index.php';
        break;
    case 'profile':
        requireLogin();
        include 'pages/profile.php';
        break;
    case 'home':
    default:
        include 'pages/home.php';
        break;
}
?>

