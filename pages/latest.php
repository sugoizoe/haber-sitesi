<?php
// Image descriptions mapping
$image_descriptions = [
    'genel' => 'Bartın ve Amasra\'dan güncel gelişmeler. Yerel haber ve etkinliklerden haberdar olun.',
    'kultur' => 'Bartın\'n tarihi dokusu ve turistik güzelliklerini keşfedin. Bölgenin kültürel zenginlikleri sizleri bekliyor.',
    'yerel' => 'Yerel etkinlikler ve gelişmeler. Bartın halkının günlük hayatından kesitler.',
];

// Get images from uploads
$uploads_dir = __DIR__ . '/../uploads';
$images = [];

if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $valid_extensions)) {
                $images[] = 'uploads/' . $file;
            }
        }
    }
}

sort($images);

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
            <?php foreach ($news_items as $index => $news): ?>
                <div class="card">
                    <?php 
                    // Assign image from uploads if available
                    $image_url = '';
                    if (isset($images[$index])) {
                        $image_url = $images[$index];
                    } elseif ($news['image_url']) {
                        $image_url = $news['image_url'];
                    }
                    ?>
                    
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($news['title']) ?>" style="object-fit: cover;">
                    <?php else: ?>
                        <div style="width:100%; height:180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-size: 24px;">
                            📰
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div style="margin-bottom: 8px;">
                            <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($news['category_name'] ?? 'Genel') ?>
                            </span>
                        </div>
                        
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        
                        <!-- Image-related description -->
                        <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin: 10px 0;">
                            <?php 
                            $cat_lower = strtolower($news['category_name'] ?? 'genel');
                            $desc = '';
                            
                            if (strpos($cat_lower, 'kultur') !== false || strpos($cat_lower, 'kültür') !== false) {
                                $desc = $image_descriptions['kultur'];
                            } elseif (strpos($cat_lower, 'yerel') !== false) {
                                $desc = $image_descriptions['yerel'];
                            } else {
                                $desc = $image_descriptions['genel'];
                            }
                            
                            echo $desc;
                            ?>
                        </p>
                        
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
