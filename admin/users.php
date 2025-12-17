<?php
// Kullanıcılar yönetimi sayfası
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
if ($admin_role !== 'admin') {
    http_response_code(403);
    echo 'Bu sayfaya sadece adminler erişebilir.';
    exit;
}

require_once __DIR__ . '/../config.php';

$error = '';
$message = '';
$admins = [];

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

// Yeni admin/editor ekle
if (!$error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] === 'editor' ? 'editor' : 'admin';
    if ($username === '' || $email === '' || $password === '') {
        $error = 'Tüm alanlar zorunludur';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM admins WHERE username=:u OR email=:e');
            $stmt->execute([':u'=>$username, ':e'=>$email]);
            if ($stmt->fetch()) {
                $error = 'Kullanıcı adı veya e-posta zaten var';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare('INSERT INTO admins (username, email, password, role) VALUES (:u,:e,:p,:r)');
                $ins->execute([':u'=>$username, ':e'=>$email, ':p'=>$hash, ':r'=>$role]);
                $message = 'Hesap oluşturuldu';
            }
        } catch (PDOException $e) {
            $error = 'Kayıt hatası: ' . $e->getMessage();
        }
    }
}

// Sil (sadece editor silinsin, admin silme basitçe engellenir)
if (!$error && isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare('SELECT role FROM admins WHERE id=?');
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && $r['role'] === 'editor') {
            $del = $pdo->prepare('DELETE FROM admins WHERE id=?');
            $del->execute([$id]);
            $message = 'Editor silindi';
        } else {
            $error = 'Admin hesaplarını silemezsiniz';
        }
    } catch (PDOException $e) {
        $error = 'Silme hatası: ' . $e->getMessage();
    }
}

// Liste
if (!$error) {
    $admins = $pdo->query('SELECT id, username, email, role, created_at FROM admins ORDER BY role, username')->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcılar - Admin Panel</title>
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
        .badge.admin { background:#e0e7ff; color:#3730a3; }
        .badge.editor { background:#dcfce7; color:#166534; }
        .error { background:#fee2e2; color:#991b1b; padding:10px; border-radius:6px; }
        .message { background:#dcfce7; color:#166534; padding:10px; border-radius:6px; }
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
                <h1>Kullanıcılar</h1>
                <a href="logout.php" class="logout-btn">Çıkış</a>
            </div>
            
            <div class="content-box">
                <h2>Admin / Editor Yönetimi</h2>
                <?php if($error): ?><div class="error" style="margin:10px 0;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if($message): ?><div class="message" style="margin:10px 0;"><?= htmlspecialchars($message) ?></div><?php endif; ?>

                <form method="post" style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; align-items:end;">
                    <div>
                        <label>Kullanıcı Adı</label>
                        <input name="username" required>
                    </div>
                    <div>
                        <label>E-posta</label>
                        <input type="email" name="email" required>
                    </div>
                    <div>
                        <label>Şifre</label>
                        <input type="password" name="password" required>
                    </div>
                    <div>
                        <label>Rol</label>
                        <select name="role">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div style="grid-column: 1 / -1; text-align:right;">
                        <button type="submit" class="btn" style="padding:10px 16px; background:#3498db; color:#fff; border:none; border-radius:6px;">Ekle</button>
                    </div>
                </form>

                <h3 style="margin-top:20px;">Hesaplar</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Kullanıcı Adı</th><th>E-posta</th><th>Rol</th><th>Oluşturma</th><th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($admins as $a): ?>
                        <tr>
                            <td><?= (int)$a['id'] ?></td>
                            <td><?= htmlspecialchars($a['username']) ?></td>
                            <td><?= htmlspecialchars($a['email']) ?></td>
                            <td><span class="badge <?= $a['role'] ?>"><?= htmlspecialchars(ucfirst($a['role'])) ?></span></td>
                            <td><?= htmlspecialchars($a['created_at']) ?></td>
                            <td>
                                <?php if($a['role']==='editor'): ?>
                                    <a href="users.php?delete=<?= (int)$a['id'] ?>" onclick="return confirm('Silinsin mi?')">Sil</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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
