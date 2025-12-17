<?php
try {
    // Get all published news ordered by latest
    $stmt = $pdo->prepare('SELECT n.*, c.name as category_name, a.username as author_name 
                          FROM news n 
                          LEFT JOIN categories c ON n.category_id = c.id 
                          LEFT JOIN admin_users a ON n.admin_user_id = a.id 
                          WHERE n.is_published = 1 
                          ORDER BY n.created_at DESC');
    $stmt->execute();
    $news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
    $news_items = [];
}
?>

<div class="container">
    <h1>Son Haberler</h1>
    
    <?php if (!empty($news_items)): ?>
        <div class="grid">
            <?php foreach ($news_items as $news): ?>
                <div class="card">
                    <?php if ($news['image_url']): ?>
                        <img src="<?= htmlspecialchars($news['image_url']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    <?php else: ?>
                        <div style="width:100%; height:180px; background:#eef2ff; display:flex; align-items:center; justify-content:center; color:#9ca3af;">
                            📰 Resim yok
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div style="margin-bottom: 8px;">
                            <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($news['category_name'] ?? 'Kategorisiz') ?>
                            </span>
                        </div>
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p><?= htmlspecialchars(substr($news['excerpt'] ?? $news['content'], 0, 100)) ?>...</p>
                        <div class="meta">
                            📅 <?= date('d.m.Y', strtotime($news['created_at'])) ?>
                            <?php if ($news['author_name']): ?>
                                | ✍️ <?= htmlspecialchars($news['author_name']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <p style="font-size: 16px;">Henüz haber bulunmuyor.</p>
        </div>
    <?php endif; ?>
</div>
