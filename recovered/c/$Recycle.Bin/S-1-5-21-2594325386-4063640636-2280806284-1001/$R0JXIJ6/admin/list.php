<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
require_admin();

// Fetch news list (id, baslik, ozet, tarih)
$stmt = $pdo->query('SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC');
$haberler = $stmt->fetchAll();
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel - Haberler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Admin</a>
      <div class="d-flex">
        <a class="btn btn-outline-light me-2" href="add.php">Haber Ekle</a>
        <a class="btn btn-warning" href="logout.php">Çıkış</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h4>Haberler</h4>
    <?php if (empty($haberler)): ?>
      <div class="alert alert-info">Henüz kayıtlı haber yok.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Başlık</th>
              <th>Özet</th>
              <th>Tarih</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($haberler as $h): ?>
              <tr>
                <td><?php echo $h['id']; ?></td>
                <td><?php echo htmlspecialchars($h['baslik']); ?></td>
                <td><?php echo htmlspecialchars($h['ozet']); ?></td>
                <td><?php echo $h['tarih']; ?></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="edit.php?id=<?php echo $h['id']; ?>">Düzenle</a>
                  <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo $h['id']; ?>" onclick="return confirm('Silinsin mi?')">Sil</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
