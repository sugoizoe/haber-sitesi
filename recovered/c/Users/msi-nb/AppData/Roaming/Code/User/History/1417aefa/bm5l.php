<?php
require_once __DIR__ . '/auth.php';
require_admin();
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Kontrol Paneli</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Admin</a>
      <div class="d-flex">
        <a class="btn btn-outline-light me-2" href="list.php">Haberler</a>
        <a class="btn btn-warning" href="logout.php">Çıkış</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="card">
      <div class="card-body">
        <h3>Hoş geldin Admin</h3>
        <p>Bu panelden haberleri ekleyebilir, düzenleyebilir ve silebilirsiniz.</p>
      </div>
    </div>
  </div>
</body>
</html>
