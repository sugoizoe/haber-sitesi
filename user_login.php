<?php
session_start();
require_once __DIR__ . '/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Kullanıcı adı ve şifre zorunludur.';
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = :u OR email = :u');
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Bilgiler hatalı.';
            }
        } catch (PDOException $e) {
            $error = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kullanıcı Girişi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f6f7fb; }
    .navbar { background:#111827; color:#fff; padding:12px 20px; }
    .navbar a { color:#e5e7eb; text-decoration:none; margin-right:12px; }
    .container { max-width:420px; margin:40px auto; background:#fff; padding:24px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
    h1 { margin:0 0 16px; }
    .form-group{ margin-bottom:12px; }
    input { width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:8px; }
    button{ width:100%; padding:12px; background:#6366f1; color:#fff; border:none; border-radius:8px; font-weight:600; }
    .error{ background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="navbar">
    <a href="/habersitesi/">HaberSitesi</a>
  </div>
  <div class="container">
    <h1>Giriş Yap</h1>
    <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Kullanıcı Adı veya E-posta</label>
        <input name="username" required>
      </div>
      <div class="form-group">
        <label>Şifre</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Giriş Yap</button>
      <div style="margin-top:10px; text-align:center;">
        Hesabın yok mu? <a href="register.php">Kayıt Ol</a>
      </div>
    </form>
  </div>
</body>
</html>
