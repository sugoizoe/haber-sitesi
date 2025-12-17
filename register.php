<?php
session_start();
require_once __DIR__ . '/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Tüm alanlar zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta giriniz.';
    } elseif (mb_strlen($username) < 3 || mb_strlen($password) < 6) {
        $error = 'Kullanıcı adı en az 3, şifre en az 6 karakter olmalıdır.';
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Kullanıcı var mı?
            $chk = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e');
            $chk->execute([':u' => $username, ':e' => $email]);
            if ($chk->fetch()) {
                $error = 'Kullanıcı adı veya e-posta zaten kayıtlı.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:u, :e, :p)');
                $ins->execute([':u' => $username, ':e' => $email, ':p' => $hash]);

                // Oturum aç ve anasayfaya git
                $_SESSION['user_id'] = (int)$pdo->lastInsertId();
                $_SESSION['user_username'] = $username;
                $_SESSION['user_email'] = $email;
                header('Location: index.php');
                exit;
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
  <title>Kayıt Ol</title>
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
    <h1>Kayıt Ol</h1>
    <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Kullanıcı Adı</label>
        <input name="username" required>
      </div>
      <div class="form-group">
        <label>E-posta</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Şifre</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Kayıt Ol</button>
      <div style="margin-top:10px; text-align:center;">
        Zaten hesabın var mı? <a href="user_login.php">Giriş Yap</a>
      </div>
    </form>
  </div>
</body>
</html>
