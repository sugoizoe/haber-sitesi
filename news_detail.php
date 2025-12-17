<?php
session_start();
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
    exit;
}

// Haber getir
$stmt = $pdo->prepare('SELECT id, title, content, image_url, created_at FROM news WHERE id = ?');
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$news) {
    http_response_code(404);
    echo 'Haber bulunamadı';
    exit;
}

// Yorum gönderimi
$comment_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $comment_error = 'Yorum yapmak için giriş yapınız.';
    } else {
        $content = trim($_POST['comment']);
        if ($content === '') {
            $comment_error = 'Yorum boş olamaz.';
        } else {
            $userId = (int)$_SESSION['user_id'];
            $authorName = $_SESSION['user_username'] ?? 'Kullanıcı';
            $authorEmail = $_SESSION['user_email'] ?? null;
            $ins = $pdo->prepare('INSERT INTO comments (news_id, user_id, author_name, author_email, content, status) VALUES (?,?,?,?,?,\'pending\')');
            $ins->execute([$id, $userId, $authorName, $authorEmail, $content]);
        }
    }
}

// Onaylı yorumlar
$cstmt = $pdo->prepare('SELECT author_name, content, created_at FROM comments WHERE news_id = ? AND status = \'approved\' ORDER BY created_at DESC');
$cstmt->execute([$id]);
$comments = $cstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($news['title']) ?> - Haber</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f6f7fb; color:#222; }
    .navbar { background:#111827; color:#fff; padding:12px 20px; }
    .navbar a { color:#e5e7eb; text-decoration:none; margin-right:12px; }
    .container { max-width:900px; margin:24px auto; padding:0 16px; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); overflow:hidden; }
    .p { padding:16px; }
    .comments { margin-top:20px; }
    .comment { background:#fff; padding:12px; border-radius:10px; box-shadow:0 1px 5px rgba(0,0,0,.05); margin-bottom:10px; }
    textarea { width:100%; min-height:100px; padding:10px; border:1px solid #e5e7eb; border-radius:8px; }
    button{ margin-top:10px; padding:10px 14px; background:#6366f1; color:#fff; border:none; border-radius:8px; }
    .error{ background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="navbar">
    <a href="/habersitesi/">HaberSitesi</a>
    <span style="float:right;">
      <?php if(isset($_SESSION['user_id'])): ?>
        <?= htmlspecialchars($_SESSION['user_username']) ?> · <a href="user_logout.php" style="color:#e5e7eb;">Çıkış</a>
      <?php else: ?>
        <a href="user_login.php">Giriş</a> · <a href="register.php">Kayıt Ol</a>
      <?php endif; ?>
    </span>
  </div>

  <div class="container">
    <article class="card">
      <?php if(!empty($news['image_url'])): ?>
        <img src="<?= htmlspecialchars($news['image_url']) ?>" style="width:100%;max-height:360px;object-fit:cover;" alt="">
      <?php endif; ?>
      <div class="p">
        <h1 style="margin-top:0;"><?= htmlspecialchars($news['title']) ?></h1>
        <div style="color:#6b7280; font-size:14px;">Yayın: <?= htmlspecialchars(date('d.m.Y H:i', strtotime($news['created_at']))) ?></div>
        <div style="margin-top:12px; line-height:1.7; color:#374151;"><?= $news['content'] ?></div>
      </div>
    </article>

    <section class="comments">
      <h2>Yorumlar</h2>
      <?php foreach($comments as $c): ?>
        <div class="comment">
          <strong><?= htmlspecialchars($c['author_name']) ?></strong>
          <div><?= nl2br(htmlspecialchars($c['content'])) ?></div>
          <div style="color:#6b7280; font-size:12px; margin-top:6px;"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($c['created_at']))) ?></div>
        </div>
      <?php endforeach; ?>

      <div style="margin-top:16px;">
        <?php if(!isset($_SESSION['user_id'])): ?>
          <div class="error">Yorum yapmak için <a href="user_login.php">giriş yapın</a>.</div>
        <?php else: ?>
          <?php if($comment_error): ?><div class="error"><?= htmlspecialchars($comment_error) ?></div><?php endif; ?>
          <form method="post">
            <textarea name="comment" placeholder="Yorumunuzu yazın..."></textarea>
            <button type="submit">Gönder</button>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </div>
</body>
</html>
