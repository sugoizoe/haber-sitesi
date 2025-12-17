<?php
require_once __DIR__ . '/db.php';

// Öne çıkan haber
$stmt = $pdo->query("SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC LIMIT 1");
$hero = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

// Liste (son 10)
$stmt2 = $pdo->query("SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC LIMIT 10");
$liste = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Hero dışındaki haberlerden sağ mini liste için 6 tane ayır
$mini = $liste;
if ($hero) {
  $mini = array_values(array_filter($mini, fn($x) => (int)$x['id'] !== (int)$hero['id']));
}
$mini = array_slice($mini, 0, 6);

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmtDate($dt) {
  if (!$dt) return '';
  $ts = strtotime($dt);
  return $ts ? date('d.m.Y H:i', $ts) : (string)$dt;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Gündem 24 - Basit haber demo sitesi" />
  <title>Gündem 24 – Haber</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>
<header class="topbar">
  <div class="wrap">
    <a class="logo" href="index.php">
      <span class="logo-badge">G</span>
      <span>Gündem 24</span>
    </a>

    <nav aria-label="Ana Menü">
      <a class="navlink" href="#">Gündem</a>
      <a class="navlink" href="#">Ekonomi</a>
      <a class="navlink" href="#">Spor</a>
      <a class="navlink" href="#">Teknoloji</a>
      <a class="navlink" href="#">Magazin</a>
    </nav>

    <div class="search" role="search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 21l-3.8-3.8M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input placeholder="Ara (demo)" aria-label="Haber ara" />
    </div>
  </div>
</header>

<main class="container">

  <!-- HERO + SAĞ LİSTE -->
  <section class="hero">
    <article class="lead">
      <img class="cover" src="https://picsum.photos/1200/640?random=3" alt="Manşet görseli" />

      <div class="meta">
        <span class="chip">SON DAKİKA</span>

        <?php if ($hero): ?>
          <h1><?= h($hero['baslik']) ?></h1>
          <p>
            <time><?= h(fmtDate($hero['tarih'])) ?></time>
            <?= h($hero['ozet']) ?>
          </p>
          <a class="btn" href="haber.php?id=<?= (int)$hero['id'] ?>">Haberi oku</a>
        <?php else: ?>
          <h1>Henüz haber yok</h1>
          <p>Veritabanında haber bulunamadı.</p>
        <?php endif; ?>
      </div>
    </article>

    <aside class="herolist" aria-label="Son haberler">
      <h3 class="side-title">Son Haberler</h3>

     <?php foreach ($mini as $m): ?>
  <a class="mini" href="haber.php?id=<?= (int)$m['id'] ?>">
    <img src="https://picsum.photos/300/220?random=<?= (int)$m['id'] ?>" alt="Haber görseli" />
    <div>
      <h3><?= h($m['baslik']) ?></h3>
      <div class="meta-row">
        <span><?= h(fmtDate($m['tarih'])) ?></span>
      </div>
    </div>
  </a>
<?php endforeach; ?>
    </aside>
  </section>

  <!-- KART GRID (DB'DEN) -->
  <section class="content">
    <div class="main-col">
      <h2 class="section-title">Güncel Haberler</h2>

      <?php if (count($liste) === 0): ?>
        <div class="empty">Henüz haber eklenmemiş.</div>
      <?php else: ?>
        <div class="cards">
          <?php foreach ($liste as $hbr): ?>
            <a href="haber.php?id=<?= (int)$hbr['id'] ?>" class="card">
              <img src="https://picsum.photos/640/400?random=<?= (int)$hbr['id'] ?>" alt="Haber görseli" />
              <div class="pad">
                <h4><?= h($hbr['baslik']) ?></h4>
                <p><?= h($hbr['ozet']) ?></p>
                <small><?= h(fmtDate($hbr['tarih'])) ?></small>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Basit sayfalama (demo) -->
      <nav class="pagination" aria-label="Sayfalama">
        <span class="page active">1</span>
      </nav>
    </div>
  </section>

</main>

<footer>
  <div class="wrap">
    <p>© Gündem 24 — Demo site. Tüm hakları saklıdır.</p>
  </div>
</footer>
</body>
</html>
