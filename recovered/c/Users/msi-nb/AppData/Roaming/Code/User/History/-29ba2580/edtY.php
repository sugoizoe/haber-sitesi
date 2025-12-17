<?php
session_start();
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Girişi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">Admin Girişi</h4>
            <?php if (!empty($_SESSION['error'])): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
              <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <form method="post" action="login_process.php">
              <div class="mb-3">
                <label class="form-label">Kullanıcı Adı</label>
                <input name="username" class="form-control" required autofocus>
              </div>
              <div class="mb-3">
                <label class="form-label">Şifre</label>
                <input name="password" type="password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary">Giriş Yap</button>
              </div>
            </form>
            <hr>
            <a href="../index.html" class="btn btn-link">Siteye dön</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
