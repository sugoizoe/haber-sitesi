<?php
// Yorumlar yönetimi sayfası
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';

require_once __DIR__ . '/../config.php';

$error = '';
$message = '';

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

// İşlemler: approve / reject / delete
if (!$error && isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE comments SET status='approved' WHERE id=?");
            $stmt->execute([$id]);
            $message = 'Yorum onaylandı';
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE comments SET status='rejected' WHERE id=?");
            $stmt->execute([$id]);
            $message = 'Yorum reddedildi';
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id=?");
            $stmt->execute([$id]);
            $message = 'Yorum silindi';
        }
    } catch (PDOException $e) {
        $error = 'İşlem hatası: ' . $e->getMessage();
    }
}

// Yorumları çek
$pending = $approved = $rejected = [];
if (!$error) {
    $pending = $pdo->query("SELECT c.id, c.author_name, c.content, c.created_at, n.title AS news_title FROM comments c JOIN news n ON n.id=c.news_id WHERE c.status='pending' ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $approved = $pdo->query("SELECT c.id, c.author_name, c.content, c.created_at, n.title AS news_title FROM comments c JOIN news n ON n.id=c.news_id WHERE c.status='approved' ORDER BY c.created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    $rejected = $pdo->query("SELECT c.id, c.author_name, c.content, c.created_at, n.title AS news_title FROM comments c JOIN news n ON n.id=c.news_id WHERE c.status='rejected' ORDER BY c.created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumlar - Admin Panel</title>
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
        .content-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
        th { background:#f8f9fa; }
        .badge { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; }
        .badge.pending { background:#fff7ed; color:#9a3412; }
        .badge.approved { background:#dcfce7; color:#166534; }
        .badge.rejected { background:#fee2e2; color:#991b1b; }
        .actions a { margin-right:8px; text-decoration:none; }
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
                <h1>Yorumlar</h1>
                <a href="logout.php" class="logout-btn">Çıkış</a>
            </div>
            
            <div class="content-box">
                <h2>Bekleyen Yorumlar</h2>
                <?php if($error): ?><div class="error" style="margin:10px 0; background:#fee2e2; color:#991b1b; padding:10px; border-radius:6px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if($message): ?><div class="message" style="margin:10px 0; background:#dcfce7; color:#166534; padding:10px; border-radius:6px;"><?= htmlspecialchars($message) ?></div><?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Haber</th>
                            <th>Yazar</th>
                            <th>Yorum</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending as $c): ?>
                        <tr>
                            <td><?= (int)$c['id'] ?></td>
                            <td><?= htmlspecialchars($c['news_title']) ?></td>
                            <td><?= htmlspecialchars($c['author_name']) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($c['content'],0,120,'…','UTF-8')) ?></td>
                            <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($c['created_at']))) ?></td>
                            <td><span class="badge pending">Bekliyor</span></td>
                            <td class="actions">
                                <a href="comments.php?action=approve&id=<?= (int)$c['id'] ?>">Onayla</a>
                                <a href="comments.php?action=reject&id=<?= (int)$c['id'] ?>">Reddet</a>
                                <a href="comments.php?action=delete&id=<?= (int)$c['id'] }" onclick="return confirm('Silinsin mi?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2 style="margin-top:24px;">Onaylanan Son Yorumlar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Haber</th>
                            <th>Yazar</th>
                            <th>Yorum</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($approved as $c): ?>
                        <tr>
                            <td><?= (int)$c['id'] ?></td>
                            <td><?= htmlspecialchars($c['news_title']) ?></td>
                            <td><?= htmlspecialchars($c['author_name']) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($c['content'],0,120,'…','UTF-8')) ?></td>
                            <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($c['created_at']))) ?></td>
                            <td><span class="badge approved">Onaylı</span></td>
                            <td class="actions">
                                <a href="comments.php?action=reject&id=<?= (int)$c['id'] ?>">Reddet</a>
                                <a href="comments.php?action=delete&id=<?= (int)$c['id'] }" onclick="return confirm('Silinsin mi?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2 style="margin-top:24px;">Reddedilen Son Yorumlar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Haber</th>
                            <th>Yazar</th>
                            <th>Yorum</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rejected as $c): ?>
                        <tr>
                            <td><?= (int)$c['id'] ?></td>
                            <td><?= htmlspecialchars($c['news_title']) ?></td>
                            <td><?= htmlspecialchars($c['author_name']) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($c['content'],0,120,'…','UTF-8')) ?></td>
                            <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($c['created_at']))) ?></td>
                            <td><span class="badge rejected">Reddedildi</span></td>
                            <td class="actions">
                                <a href="comments.php?action=approve&id=<?= (int)$c['id'] ?>">Onayla</a>
                                <a href="comments.php?action=delete&id=<?= (int)$c['id'] }" onclick="return confirm('Silinsin mi?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
