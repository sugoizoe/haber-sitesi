<?php
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT id, baslik, ozet, icerik, resim_yolu, tarih FROM haberler WHERE id = ?");
$stmt->execute([$id]);
$haber = $stmt->fetch();

if (!$haber) {
  http_response_code(404);
  echo "Haber bulunamadı.";
  exit;
}

$tarih = $haber['tarih'] ? date('d.m.Y H:i', strtotime($haber['tarih'])) : '';
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($haber['baslik']) ?></title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 900px;">
  <a href="/habersitesi/" class="text-decoration-none">← Ana sayfa</a>

  <h1 class="mt-3"><?= htmlspecialchars($haber['baslik']) ?></h1>
  <p class="text-muted mb-2"><?= htmlspecialchars($tarih) ?></p>

  <?php if (!empty($haber['resim_yolu'])): ?>
    <img class="img-fluid rounded mb-3" src="<?= htmlspecialchars($haber['resim_yolu']) ?>" alt="">
  <?php endif; ?>

  <?php if (!empty($haber['ozet'])): ?>
    <p class="fw-semibold"><?= htmlspecialchars($haber['ozet']) ?></p>
  <?php endif; ?>

  <div class="bg-white p-3 rounded">
    <?= nl2br(htmlspecialchars($haber['icerik'])) ?>
  </div>
</div>

</body>
</html>
