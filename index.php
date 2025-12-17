<?php
session_start();
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user, $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('DB: ' . htmlspecialchars($e->getMessage()));
}

// Process login/register BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $username_or_email = trim($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user_type = $_POST['user_type'] ?? 'user';
        
        if (empty($username_or_email) || empty($password)) {
            $_SESSION['flash_error'] = 'Kullanıcı adı/email ve şifre gerekli.';
        } else {
            if ($user_type === 'admin') {
                // Admin/Editor login
                try {
                    $stmt = $pdo->prepare('SELECT id, username, role, password FROM admin_users WHERE username = ? OR email = ? LIMIT 1');
                    $stmt->execute([$username_or_email, $username_or_email]);
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($admin && password_verify($password, $admin['password']) && $admin['role'] !== 'User') {
                        $_SESSION['user_id'] = $admin['id'];
                        $_SESSION['user_name'] = $admin['username'];
                        $_SESSION['user_role'] = $admin['role'];
                        header('Location: ?page=home');
                        exit;
                    } else {
                        $_SESSION['flash_error'] = 'Yanlış kullanıcı adı/email veya şifre.';
                    }
                } catch (PDOException $e) {
                    $_SESSION['flash_error'] = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
                }
            } else {
                // User login
                try {
                    $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1');
                    $stmt->execute([$username_or_email, $username_or_email]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['username'];
                        $_SESSION['user_role'] = 'User';
                        header('Location: ?page=home');
                        exit;
                    } else {
                        $_SESSION['flash_error'] = 'Yanlış kullanıcı adı/email veya şifre.';
                    }
                } catch (PDOException $e) {
                    $_SESSION['flash_error'] = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
                }
            }
        }
        header('Location: ?page=home');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        $errors = [];
        
        if (empty($username)) $errors[] = 'Kullanıcı adı gerekli.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli email gerekli.';
        if (empty($password)) $errors[] = 'Şifre gerekli.';
        if ($password !== $password_confirm) $errors[] = 'Şifreler eşleşmiyor.';
        
        if (empty($errors)) {
            try {
                // Check if username or email already exists
                $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    $errors[] = 'Bu kullanıcı adı veya email zaten kayıtlı.';
                } else {
                    // Insert new user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                    $stmt->execute([$username, $email, $hashed_password]);
                    
                    // Auto-login
                    $user_id = $pdo->lastInsertId();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $username;
                    $_SESSION['user_role'] = 'User';
                    $_SESSION['flash_success'] = 'Başarıyla kaydoldunuz! Hoş geldiniz.';
                    
                    header('Location: ?page=home');
                    exit;
                }
            } catch (PDOException $e) {
                $errors[] = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' | ', $errors);
        }
        header('Location: ?page=home');
        exit;
    }
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? 'Guest';
$user_name = $_SESSION['user_name'] ?? '';
$page = $_GET['page'] ?? 'home';
$page = preg_replace('/[^a-z_]/', '', $page);

// Display messages
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? 'Başarıyla kaydoldunuz. Hoş geldiniz!' : '';

// Get flash messages from session
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// Erişim kontrolleri
$protected = ['admin' => ['Admin'], 'editor' => ['Admin', 'Editor'], 'profile' => ['User', 'Admin', 'Editor']];
if (isset($protected[$page]) && !in_array($user_role, $protected[$page])) {
    $page = 'home';
    $error = 'Erişim reddedildi.';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Sitesi - Güncel Haberler</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto; background:#f6f7fb; color:#222; }
        header { background:#111827; color:#fff; padding:14px 0; position:sticky; top:0; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,.15); }
        nav { max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 16px; }
        .brand { font-weight:700; font-size:20px; cursor:pointer; transition:all 0.2s; }
        .brand:hover { opacity:0.8; }
        .nav-center { display:flex; gap:20px; flex:1; justify-content:center; }
        .nav-center a { color:#e5e7eb; text-decoration:none; padding:8px 12px; border-radius:6px; transition:all 0.2s; font-weight:500; }
        .nav-center a:hover { background:#1f2937; }
        .nav-right { display:flex; gap:12px; align-items:center; }
        .user-info { color:#e5e7eb; font-size:13px; display:flex; align-items:center; gap:8px; }
        .btn-modal { background:#6366f1; color:#fff; padding:8px 16px; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.2s; }
        .btn-modal:hover { background:#4f46e5; transform:translateY(-2px); }
        .btn-logout { background:#ef4444; color:#fff; padding:8px 16px; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.2s; }
        .btn-logout:hover { background:#dc2626; }
        
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.6); z-index:1000; align-items:center; justify-content:center; }
        .modal.show { display:flex; }
        .modal-content { background:#fff; border-radius:12px; padding:32px; max-width:400px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.3); position:relative; animation:slideUp 0.3s ease; }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        .modal-close { position:absolute; top:12px; right:16px; background:none; border:none; font-size:24px; cursor:pointer; color:#9ca3af; }
        .modal-close:hover { color:#222; }
        .modal h2 { margin-bottom:20px; color:#111827; }
        .modal-tabs { display:flex; gap:12px; margin-bottom:20px; }
        .modal-tab { flex:1; padding:10px; background:#f3f4f6; border:none; border-radius:8px; cursor:pointer; font-weight:600; color:#6b7280; transition:all 0.2s; }
        .modal-tab.active { background:#6366f1; color:#fff; }
        .modal-form { display:none; }
        .modal-form.show { display:block; }
        
        main { max-width:1200px; margin:0 auto; padding:24px 16px; }
        .container { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:24px; }
        h1 { margin-bottom:20px; color:#111827; font-size:28px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:18px; }
        .card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); transition:all 0.3s; border:1px solid #e5e7eb; }
        .card:hover { transform:translateY(-4px); box-shadow:0 8px 20px rgba(0,0,0,.12); }
        .card img { width:100%; height:180px; object-fit:cover; background:#eef2ff; }
        .card-body { padding:16px; }
        .card h3 { margin:0 0 8px; font-size:16px; color:#111827; font-weight:600; }
        .card p { margin:0; color:#6b7280; font-size:13px; line-height:1.5; }
        .card .meta { color:#9ca3af; font-size:12px; margin-top:10px; }
        .btn { display:inline-block; padding:10px 16px; background:#6366f1; color:#fff; text-decoration:none; border-radius:8px; border:none; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s; }
        .btn:hover { background:#4f46e5; transform:translateY(-2px); }
        .btn-sm { padding:6px 12px; font-size:12px; margin-top:10px; }
        .error { background:#fee2e2; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; border-left:4px solid #dc2626; }
        .success { background:#dcfce7; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; border-left:4px solid #22c55e; }
        footer { background:#111827; color:#e5e7eb; text-align:center; padding:28px 16px; margin-top:48px; font-size:13px; }
        
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#333; font-size:13px; }
        input[type="text"], input[type="email"], input[type="password"], textarea, select { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; transition:all 0.2s; }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, textarea:focus, select:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99, 102, 241, 0.1); }
        textarea { resize:vertical; min-height:100px; }
        
        @media (max-width:768px) {
            .nav-center { display:none; }
            .container { padding:16px; }
            .grid { grid-template-columns:1fr; }
            .modal-content { padding:20px; }
        }
        
        table { width:100%; border-collapse:collapse; margin-top:16px; }
        th, td { padding:12px; border-bottom:1px solid #e5e7eb; text-align:left; font-size:13px; }
        th { background:#f8f9fa; font-weight:600; }
        .badge { display:inline-block; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; }
        .badge.admin { background:#e0e7ff; color:#3730a3; }
        .badge.editor { background:#dcfce7; color:#166534; }
        .badge.user { background:#f3e8ff; color:#6b21a8; }
        .empty { text-align:center; color:#9ca3af; padding:48px 16px; }
    </style>
</head>
<body>
<header>
    <nav>
        <div class="brand">📰 Haber Sitesi</div>
        <div class="nav-center">
            <a href="?page=home">Anasayfa</a>
            <a href="?page=categories">Kategoriler</a>
            <a href="?page=latest">Son Haberler</a>
        </div>
        <div class="nav-right">
            <?php if($is_logged_in && !empty($user_name)): ?>
                <div class="user-info">
                    <?= htmlspecialchars($user_name) ?> 
                    <span class="badge badge-<?= strtolower($user_role === 'Guest' ? 'user' : $user_role) ?>"><?= $user_role ?></span>
                </div>
                <?php if($user_role === 'Admin'): ?>
                    <a href="?page=admin" class="btn-modal">Yönet</a>
                <?php elseif($user_role === 'Editor'): ?>
                    <a href="?page=editor" class="btn-modal">İçerik</a>
                <?php endif; ?>
                <a href="?page=logout" class="btn-logout">Çıkış</a>
            <?php else: ?>
                <button class="btn-modal" onclick="openAuthModal('login')">Giriş Yap</button>
                <button class="btn-modal" onclick="openAuthModal('register')">Kayıt Ol</button>
            <?php endif; ?>
        </div>
    </nav>
</header>

<!-- Auth Modal -->
<div class="modal" id="authModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAuthModal()">✕</button>
        
        <div class="modal-tabs">
            <button class="modal-tab active" onclick="switchAuthTab('login')">Giriş Yap</button>
            <button class="modal-tab" onclick="switchAuthTab('register')">Kayıt Ol</button>
        </div>
        
        <!-- Login Form -->
        <form class="modal-form show" id="loginForm" method="POST">
            <h2>Giriş Yap</h2>
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Kullanıcı Adı / Email:</label>
                <input type="text" name="username_or_email" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Giriş Türü:</label>
                <select name="user_type">
                    <option value="admin">Yönetici / Editör</option>
                    <option value="user">Üye</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width:100%">Giriş Yap</button>
        </form>
        
        <!-- Register Form -->
        <form class="modal-form" id="registerForm" method="POST">
            <h2>Kayıt Ol</h2>
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label>Kullanıcı Adı:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Şifre Tekrar:</label>
                <input type="password" name="password_confirm" required>
            </div>
            <button type="submit" class="btn" style="width:100%">Kayıt Ol</button>
        </form>
    </div>
</div>

<script>
function openAuthModal(tab) {
    document.getElementById('authModal').classList.add('show');
    switchAuthTab(tab);
}
function closeAuthModal() {
    document.getElementById('authModal').classList.remove('show');
}
function switchAuthTab(tab) {
    document.querySelectorAll('.modal-form').forEach(f => f.classList.remove('show'));
    document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
    
    if(tab === 'login') {
        document.getElementById('loginForm').classList.add('show');
        document.querySelectorAll('.modal-tab')[0].classList.add('active');
    } else {
        document.getElementById('registerForm').classList.add('show');
        document.querySelectorAll('.modal-tab')[1].classList.add('active');
    }
}
document.getElementById('authModal').addEventListener('click', function(e) {
    if(e.target === this) closeAuthModal();
});
</script>

<main>
    <?php if (!empty($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="success"><?= $success ?></div><?php endif; ?>
    <?php
    switch ($page) {
        case 'home': include 'pages/home.php'; break;
        case 'categories': include 'pages/categories.php'; break;
        case 'latest': include 'pages/latest.php'; break;
        case 'logout': include 'pages/logout.php'; exit;
        case 'admin': if ($user_role === 'Admin') include 'pages/admin.php'; break;
        case 'editor': if (in_array($user_role, ['Admin', 'Editor'])) include 'pages/editor.php'; break;
        default: include 'pages/home.php';
    }
    ?>
</main>

<footer>© 2025 Haber Sitesi - Tüm Hakları Saklıdır</footer>
</body>
</html>
