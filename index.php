<?php
session_start();
require_once __DIR__ . '/config.php';

try {
  $pdo = new PDO(
    "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
    $db_user,
    $db_pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
} catch (PDOException $e) {
  $db_error = $e->getMessage();
}

$news = [];
if (!isset($db_error)) {
  $stmt = $pdo->query("SELECT id, title, content, image_url, created_at FROM news ORDER BY created_at DESC LIMIT 12");
  $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Haber Sitesi</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f6f7fb; color:#222; }
    .navbar { position:sticky; top:0; background:#111827; color:#fff; padding:12px 20px; display:flex; gap:16px; align-items:center; z-index:10; }
    .navbar a { color:#e5e7eb; text-decoration:none; padding:8px 10px; border-radius:6px; }
    .navbar a:hover { background:#1f2937; }
    .brand { font-weight:700; color:#fff; margin-right:auto; }
    .container { max-width:1100px; margin:24px auto; padding:0 16px; }
    .hero { background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; padding:40px 24px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.15); }
    .hero h1 { margin:0 0 8px; font-size:32px; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; margin-top:20px; }
    .card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.06); display:flex; flex-direction:column; }
    .card img { width:100%; height:160px; object-fit:cover; background:#eef2ff; }
    .card .p { padding:14px; }
    .card h3 { margin:0 0 8px; font-size:18px; color:#111827; }
    .card p { margin:0; color:#4b5563; font-size:14px; line-height:1.4; }
    .muted { color:#6b7280; font-size:12px; margin-top:8px; }
    .btn { display:inline-block; padding:10px 14px; background:#6366f1; color:#fff; text-decoration:none; border-radius:8px; margin-top:10px; }
    .btn:hover { background:#4f46e5; }
    footer { text-align:center; color:#6b7280; padding:30px 0; }
    .empty { background:#fff; padding:30px; border-radius:12px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.06); }
  </style>
  <link rel="icon" href="uploads/amasra-btv-logo.svg">
  <meta name="description" content="Basit Haber Sitesi">
  <meta name="theme-color" content="#111827">
  <script>
    function truncate(t, n){ return t.length>n ? t.slice(0,n).trim()+"…" : t }
  </script>
</head>
<body>
  <nav class="navbar">
    <a class="brand" href="/habersitesi/">HaberSitesi</a>
    <a href="/habersitesi/">Anasayfa</a>
    <a href="/habersitesi/user_login.php">Giriş</a>
    <a href="/habersitesi/register.php">Kayıt Ol</a>
    <a href="/habersitesi/admin/dashboard.php">Admin</a>
  </nav>

  <div class="container">
    <div class="hero">
      <h1>Günün Haberleri</h1>
      <div style="opacity:.9">Son eklenen haberler burada listelenir.</div>
    </div>

    <?php if(isset($db_error)): ?>
      <div class="empty" style="margin-top:20px;color:#b91c1c;">Veritabanı hatası: <?= htmlspecialchars($db_error) ?></div>
    <?php elseif(empty($news)): ?>
      <div class="empty" style="margin-top:20px;">Henüz haber yok. Admin panelinden haber ekleyin.</div>
    <?php else: ?>
      <div class="grid" style="margin-top:20px;">
        <?php foreach($news as $n): ?>
          <article class="card">
            <?php if(!empty($n['image_url'])): ?>
              <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="">
            <?php else: ?>
              <img src="uploads/amasra-hero.jpg" alt="">
            <?php endif; ?>
            <div class="p">
              <h3><?= htmlspecialchars($n['title']) ?></h3>
              <p><?= htmlspecialchars(mb_substr(strip_tags($n['content']),0,120)) ?><?= mb_strlen(strip_tags($n['content']))>120?'…':'' ?></p>
              <div class="muted"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($n['created_at']))) ?></div>
              <a class="btn" href="news_detail.php?id=<?= (int)$n['id'] ?>">Oku</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <footer>
    © <?= date('Y') ?> HaberSitesi
  </footer>
</body>
</html>
<?php
require_once __DIR__ . '/db.php';

$stmt = $pdo->query("SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC LIMIT 1");
$hero = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

$stmt2 = $pdo->query("SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC LIMIT 10");
$liste = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];

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
  <meta name="description" content="Amasra ve Bartın gündemi - Amasra BTV" />
  <title>Amasra BTV</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>
<header class="topbar">
  <div class="wrap">
    <a class="logo" href="index.php">
      <img src="uploads/amasra-btv-logo.svg" alt="Amasra BTV Logo" onerror="this.style.display='none'" />
      <span class="logo-text">Amasra BTV</span>
    </a>

    <nav aria-label="Ana Menü">
      <a class="navlink" href="#">Gündem</a>
      <a class="navlink" href="#">Ekonomi</a>
      <a class="navlink" href="#">Spor</a>
      <a class="navlink" href="#">Teknoloji</a>
      <a class="navlink" href="#">Magazin</a>
    </nav>

    <div class="actions">
      <div class="search" role="search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 21l-3.8-3.8M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input placeholder="Ara (demo)" aria-label="Haber ara" />
      </div>
      <a class="login" href="login.php">Giriş</a>
      <a class="register" href="register.php">Kayıt Ol</a>
    </div>
  </div>
</header>

<main class="container">

  
  <section class="hero">
    <article class="lead">
      <img class="cover" src="uploads/amasra-hero.jpg" alt="Amasra manşet görseli" onerror="this.onerror=null;this.src='https://picsum.photos/1200/640?random=3'" />

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
  <img src="uploads/amasra_thumb_<?= (int)$m['id'] ?>.jpg" alt="Haber görseli" onerror="this.onerror=null;this.src='https://picsum.photos/300/220?random=' + <?= (int)$m['id'] ?>" />
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

  
  <section class="content">
  <div class="main-col">
    <h2 class="section-title">Güncel Haberler</h2>

    <div class="cards">
      <?php foreach ($liste as $hbr): ?>
        <a href="haber.php?id=<?= (int)$hbr['id'] ?>" class="card">
          <img src="uploads/amasra_card_<?= (int)$hbr['id'] ?>.jpg" alt="Haber görseli" onerror="this.onerror=null;this.src='https://picsum.photos/640/400?random=' + <?= (int)$hbr['id'] ?>" />
          <div class="pad">
            <h4><?= h($hbr['baslik']) ?></h4>
            <p><?= h($hbr['ozet']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <nav class="pagination" aria-label="Sayfalama">
      <span class="page active">1</span>
    </nav>
  </div>

  <aside class="sidebar">
    <div class="block">
      <h5>Son Eklenenler</h5>
      <div class="list">
        <?php foreach ($liste as $h): ?>
          <a href="haber.php?id=<?= (int)$h['id'] ?>">
            <span class="dot"></span>
            <div>
              <div><?= h($h['baslik']) ?></div>
              <small><?= h(fmtDate($h['tarih'])) ?></small>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="block">
      <h5>Etiketler (Amasra & Bartın)</h5>
      <div class="tags">
        <a class="tag" href="#">Amasra</a>
        <a class="tag" href="#">Bartın</a>
        <a class="tag" href="#">Turizm</a>
        <a class="tag" href="#">Kültür</a>
      </div>
    </div>
  </aside>
</section>


</main>

<footer>
  <div class="wrap">
    <p>© Amasra BTV — Amasra & Bartın haberleri (Demo).</p>
  </div>
</footer>
</body>
</html>
