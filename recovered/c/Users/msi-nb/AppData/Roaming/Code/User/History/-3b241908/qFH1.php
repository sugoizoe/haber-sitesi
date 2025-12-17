<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
require_admin();
?>

<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Haber Ekle</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h3>Haber Ekle</h3>

 <form method="post" action="add_process.php" enctype="multipart/form-data">

   <div class="mb-3">
  <label class="form-label">Resim</label>
  <input type="file" name="resim" class="form-control" accept="image/*">
</div>


    <div class="mb-3">
      <label class="form-label">Özet</label>
      <input name="ozet" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">İçerik</label>
      <textarea name="icerik" class="form-control" rows="6" required></textarea>
    </div>

    <button class="btn btn-primary">Kaydet</button>
    <a href="list.php" class="btn btn-secondary">Geri</a>
  </form>
</div>

</body>
</html>
