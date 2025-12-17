<?php
// Simple frontend user login placeholder
// Replace processing with your user auth logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['user'] ?? '';
  // TODO: authenticate user
  header('Location: index.php');
  exit;
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kullanıcı Girişi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <h1>Kullanıcı Girişi</h1>
    <form method="post" action="login.php" style="max-width:360px">
      <label>Kullanıcı adı<br><input name="user" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <label>Parola<br><input name="pass" type="password" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid #ccc"></label>
      <button type="submit" style="padding:8px 12px;border-radius:6px;margin-top:8px">Giriş</button>
    </form>
  </main>
</body>
</html>
