<?php
$page_title = 'Haberler';
$stmt = $pdo->query("SELECT id, title, content, image_url, created_at FROM news WHERE is_published=1 ORDER BY created_at DESC LIMIT 20");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container">
    <h1>Güncel Haberler</h1>
    <div class="grid">
        <?php foreach ($news as $n): ?>
        <div class="card">
            <?php if ($n['image_url']): ?><img src="<?= htmlspecialchars($n['image_url']) ?>" alt=""><?php else: ?><div style="width:100%; height:160px; background:#eef2ff;"></div><?php endif; ?>
            <div class="card-body">
                <h3><?= htmlspecialchars($n['title']) ?></h3>
                <p><?= htmlspecialchars(mb_substr(strip_tags($n['content']), 0, 80)) ?>...</p>
                <div class="meta"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></div>
                <a href="index.php?page=news&id=<?= (int)$n['id'] ?>" class="btn btn-sm" style="margin-top:8px;">Oku</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($news)): ?><p style="text-align:center; color:#9ca3af; margin-top:40px;">Henüz haber yok.</p><?php endif; ?>
</div>
