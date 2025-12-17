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

$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
$user_role = $_SESSION['user_role'] ?? $_SESSION['admin_role'] ?? 'Guest';
$user_name = $_SESSION['user_username'] ?? $_SESSION['admin_username'] ?? '';
$page = $_GET['page'] ?? 'home';
$page = preg_replace('/[^a-z_]/', '', $page);

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
    <title>Haber Sitesi</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto; background:#f6f7fb; color:#222; }
        header { background:#111827; color:#fff; padding:12px 0; sticky:true; top:0; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,.1); }
        nav { max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 16px; }
        .brand { font-weight:700; font-size:18px; }
        .nav-links { display:flex; gap:16px; align-items:center; }
        .nav-links a { color:#e5e7eb; text-decoration:none; padding:8px 12px; border-radius:6px; }
        .nav-links a:hover { background:#1f2937; }
        .btn-login { background:#6366f1; color:#fff !important; }
        .btn-login:hover { background:#4f46e5 !important; }
        .btn-logout { background:#ef4444; color:#fff !important; }
        main { max-width:1200px; margin:0 auto; padding:20px 16px; }
        .container { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:20px; }
        h1 { margin-bottom:16px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:16px; }
        .card { background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 5px rgba(0,0,0,.06); }
        .card img { width:100%; height:160px; object-fit:cover; }
        .card-body { padding:12px; }
        .card h3 { margin:0 0 6px; font-size:15px; }
        .card p { margin:0; color:#6b7280; font-size:12px; }
        .card .meta { color:#9ca3af; font-size:11px; margin-top:6px; }
        .btn { display:inline-block; padding:10px 14px; background:#6366f1; color:#fff; text-decoration:none; border-radius:8px; border:none; cursor:pointer; font-size:14px; }
        .btn:hover { background:#4f46e5; }
        .btn-sm { padding:6px 10px; font-size:12px; }
        .error { background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; }
        .success { background:#dcfce7; color:#166534; padding:12px; border-radius:8px; margin-bottom:16px; }
        footer { background:#111827; color:#e5e7eb; text-align:center; padding:24px 16px; margin-top:40px; font-size:13px; }
        .form-group { margin-bottom:12px; }
        input, textarea, select { width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; font-family:inherit; }
        textarea { resize:vertical; min-height:100px; }
        @media (max-width:768px) {
            .nav-links { gap:8px; flex-wrap:wrap; }
            .grid { grid-template-columns:1fr; }
            nav { flex-direction:column; gap:12px; }
        }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; font-size:13px; }
        th { background:#f8f9fa; }
        .badge { display:inline-block; padding:3px 6px; border-radius:4px; font-size:11px; }
        .badge.admin { background:#e0e7ff; color:#3730a3; }
        .badge.editor { background:#dcfce7; color:#166534; }
        .badge.user { background:#f3e8ff; color:#6b21a8; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="brand">🗞️ HaberSitesi</div>
            <div class="nav-links">
                <a href="index.php">Anasayfa</a>
                <?php if ($is_logged_in): ?>
                    <?php if ($user_role === 'Admin'): ?><a href="index.php?page=admin">Admin</a><?php elseif ($user_role === 'Editor'): ?><a href="index.php?page=editor">İçerik</a><?php endif; ?>
                    <span style="font-size:12px;"><?= htmlspecialchars($user_name) ?> <span class="badge <?= strtolower($user_role) ?>"><?= $user_role ?></span></span>
                    <a href="index.php?page=logout" class="btn-logout">Çıkış</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-login">Giriş</a>
                    <a href="index.php?page=register" class="btn-login">Kayıt</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <?php if (isset($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php
        switch ($page) {
            case 'home': include 'pages/home.php'; break;
            case 'login': include 'pages/login.php'; break;
            case 'register': include 'pages/register.php'; break;
            case 'logout': include 'pages/logout.php'; exit;
            case 'admin': if ($user_role === 'Admin') include 'pages/admin.php'; break;
            case 'editor': if (in_array($user_role, ['Admin', 'Editor'])) include 'pages/editor.php'; break;
            default: include 'pages/home.php';
        }
        ?>
    </main>

    <footer>© 2025 HaberSitesi</footer>
</body>
</html>
