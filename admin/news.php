<?php
// Haber yönetimi sayfası
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    $error = 'Veritabanı bağlantı hatası: ' . $e->getMessage();
}

// Yeni haber ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? 1;
    $image_url = $_POST['image_url'] ?? '';
    
    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO news (title, content, category_id, image_url, created_at) 
                 VALUES (?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$title, $content, $category_id, $image_url]);
            $message = 'Haber başarıyla eklendi!';
        } catch (PDOException $e) {
            $error = 'Haber eklenirken hata oluştu: ' . $e->getMessage();
        }
    } else {
        $error = 'Başlık ve içerik zorunludur.';
    }
}

// Haberleri listele
$news = [];
try {
    $stmt = $pdo->query('SELECT * FROM news ORDER BY created_at DESC');
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Haberler yüklenirken hata: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haberler - Admin Panel</title>
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
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .content-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
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
                <h1>Haberler</h1>
                <a href="logout.php" class="logout-btn">Çıkış</a>
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <div class="content-box">
                <h2><?= $action === 'add' ? 'Yeni Haber Ekle' : 'Haberler' ?></h2>
                
                <?php if ($action === 'add'): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="title">Başlık:</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">İçerik:</label>
                            <textarea id="content" name="content" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Kategori:</label>
                            <select id="category_id" name="category_id">
                                <option value="1">Genel</option>
                                <option value="2">Spor</option>
                                <option value="3">Teknoloji</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url">Resim URL:</label>
                            <input type="url" id="image_url" name="image_url">
                        </div>
                        
                        <button type="submit" class="btn">Kaydet</button>
                        <a href="news.php" class="btn" style="background: #95a5a6;">İptal</a>
                    </form>
                <?php else: ?>
                    <a href="news.php?action=add" class="btn">+ Yeni Haber</a>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Başlık</th>
                                <th>Kategori</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['title']) ?></td>
                                    <td>Genel</td>
                                    <td><?= $item['created_at'] ?></td>
                                    <td>
                                        <a href="news.php?action=edit&id=<?= $item['id'] ?>" class="btn" style="background: #f39c12; padding: 5px 10px;">Düzenle</a>
                                        <a href="news.php?action=delete&id=<?= $item['id'] ?>" class="btn" style="background: #e74c3c; padding: 5px 10px;" onclick="return confirm('Silmek istediğinizden emin misiniz?');">Sil</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
