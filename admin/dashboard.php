<?php
// Admin paneli - Session kontrol
session_start();

// Oturum açılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Zaman aşımı kontrolü (1 saat)
if (time() - $_SESSION['login_time'] > 3600) {
    session_destroy();
    header('Location: ../login.php?expired=1');
    exit;
}

// Login zamanını güncelle
$_SESSION['login_time'] = time();

require_once __DIR__ . '/../config.php';

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Haber Sitesi</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar h2 {
            padding: 20px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar ul li {
            border-bottom: 1px solid #34495e;
        }
        .sidebar ul li a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover {
            background: #34495e;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #2c3e50;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .card .number {
            font-size: 32px;
            color: #3498db;
            font-weight: bold;
        }
        .content-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="news.php">📰 Haberler</a></li>
                <li><a href="categories.php">📁 Kategoriler</a></li>
                <li><a href="comments.php">💬 Yorumlar</a></li>
                <li><a href="users.php">👥 Kullanıcılar</a></li>
                <li><a href="settings.php">⚙️ Ayarlar</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Hoş geldiniz, <?= htmlspecialchars($admin_username) ?></span>
                    <a href="logout.php" class="logout-btn">Çıkış</a>
                </div>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Toplam Haberler</h3>
                    <div class="number">0</div>
                </div>
                <div class="card">
                    <h3>Kategoriler</h3>
                    <div class="number">0</div>
                </div>
                <div class="card">
                    <h3>Yorumlar</h3>
                    <div class="number">0</div>
                </div>
                <div class="card">
                    <h3>Kullanıcılar</h3>
                    <div class="number">0</div>
                </div>
            </div>
            
            <div class="content-box">
                <h2>Hoş Geldiniz Admin Paneline!</h2>
                <p style="margin-top: 10px; color: #666;">
                    Soldaki menüden haber yönetimi, kategori yönetimi ve diğer işlemleri gerçekleştirebilirsiniz.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
