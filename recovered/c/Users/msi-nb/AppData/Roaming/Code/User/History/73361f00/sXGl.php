<?php
// Simple frontend registration placeholder
// Replace with real user creation/auth flow as needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim($_POST['user'] ?? '');
  $pass = $_POST['pass'] ?? '';
  // TODO: validate & store user securely (hash passwords, prevent duplicates)
  header('Location: login.php');
  exit;
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kayıt Ol</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <h1>Kayıt Ol</h1>
    <form method="post" action="register.php" style="max-width:420px">
      <label>Kullanıcı adı<br><input name="user" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>Parola<br><input name="pass" type="password" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>E-posta (isteğe bağlı)<br><input name="email" type="email" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <button type="submit" style="padding:8px 12px;border-radius:6px;margin-top:8px">Kayıt Ol</button>
    </form>
  </main>
</body>
</html>
