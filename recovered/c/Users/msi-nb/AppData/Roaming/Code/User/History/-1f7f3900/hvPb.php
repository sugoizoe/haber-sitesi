<?php
require_once __DIR__ . '/db.php';
session_start();

$errors = [];
$oldUser = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['user'] ?? '');
  $password = $_POST['pass'] ?? '';

  $oldUser = $identifier;

  if ($identifier === '' || $password === '') {
    $errors[] = 'Kullanıcı adı/e-posta ve parola gerekli.';
  } else {
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      header('Location: index.php');
      exit;
    }
    $errors[] = 'Kullanıcı adı/e-posta veya parola yanlış.';
  }
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kullanıcı Girişi</title>
  <link rel="stylesheet" href="style.css">
  <style> .errors{background:#fee;border:1px solid #f99;padding:8px;border-radius:6px;margin-bottom:12px} </style>
</head>
<body>
  <main class="container">
    <h1>Kullanıcı Girişi</h1>
    <?php if (!empty($errors)): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" action="login.php" style="max-width:360px">
      <label>Kullanıcı adı veya e-posta<br><input name="user" required value="<?php echo htmlspecialchars($oldUser, ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>Parola<br><input name="pass" type="password" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <button type="submit" style="padding:8px 12px;border-radius:6px;margin-top:8px">Giriş</button>
    </form>
  </main>
</body>
</html>
