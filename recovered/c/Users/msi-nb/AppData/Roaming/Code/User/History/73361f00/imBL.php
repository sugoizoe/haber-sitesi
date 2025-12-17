<?php
require_once __DIR__ . '/db.php';
session_start();

$errors = [];
$old = ['user' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['user'] ?? '');
  $password = $_POST['pass'] ?? '';
  $email = trim($_POST['email'] ?? '');

  $old['user'] = $username;
  $old['email'] = $email;

  if ($username === '') {
    $errors[] = 'Kullanıcı adı gerekli.';
  } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
    $errors[] = 'Kullanıcı adı 3-30 karakter, sadece harf,rakam veya _ olmalı.';
  }

  if ($email === '') {
    $errors[] = 'E-posta gerekli.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Geçersiz e-posta adresi.';
  }

  if (strlen($password) < 6) {
    $errors[] = 'Parola en az 6 karakter olmalı.';
  }

  // Disallow common SQL injection characters/sequences in password
  if (preg_match('/(\'|"|;|\\\\|--|\/\*|\*\/)/', $password)) {
    $errors[] = 'Parolada yasaklı karakter veya diziler kullanılmış (ör: \' " ; -- /* */ \ ).';
  }

  if (empty($errors)) {
    // check duplicates
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
      $errors[] = 'Kullanıcı adı veya e-posta zaten kullanılıyor.';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
      $ins->execute([$username, $email, $hash]);
      header('Location: login.php');
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kayıt Ol</title>
  <link rel="stylesheet" href="style.css">
  <style> .errors{background:#fee;border:1px solid #f99;padding:8px;border-radius:6px;margin-bottom:12px} </style>
</head>
<body>
  <main class="container">
    <h1>Kayıt Ol</h1>
    <?php if (!empty($errors)): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" action="register.php" style="max-width:420px">
      <label>Kullanıcı adı<br><input name="user" required value="<?php echo htmlspecialchars($old['user'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>Parola<br><input name="pass" type="password" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>E-posta<br><input name="email" type="email" required value="<?php echo htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <button type="submit" style="padding:8px 12px;border-radius:6px;margin-top:8px">Kayıt Ol</button>
    </form>
  </main>
</body>
</html>
