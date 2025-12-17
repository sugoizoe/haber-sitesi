<?php
$category_slug = $_GET['slug'] ?? null;
$category = null;
$news_items = [];

try {
    // Get all categories
    $stmt = $pdo->prepare('SELECT c.*, COUNT(n.id) as news_count FROM categories c 
                           LEFT JOIN news n ON n.category_id = c.id AND n.is_published = 1 
                           GROUP BY c.id ORDER BY c.name');
    $stmt->execute();
    $all_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If a category is selected, get news from that category
    if ($category_slug) {
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
        $stmt->execute([$category_slug]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            $stmt = $pdo->prepare('SELECT n.*, c.name as category_name, a.username as author_name 
                                  FROM news n 
                                  LEFT JOIN categories c ON n.category_id = c.id 
                                  LEFT JOIN admin_users a ON n.admin_user_id = a.id 
                                  WHERE n.category_id = ? AND n.is_published = 1 
                                  ORDER BY n.created_at DESC');
            $stmt->execute([$category['id']]);
            $news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    $error = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
}
?>

<div class="container">
    <h1><?= $category ? htmlspecialchars($category['name']) : 'Kategoriler' ?></h1>
    
    <!-- Categories Grid -->
    <div class="categories-grid">
        <?php foreach ($all_categories as $cat): ?>
            <a href="?page=categories&slug=<?= htmlspecialchars($cat['slug']) ?>" 
               class="category-card <?= ($category && $category['id'] == $cat['id']) ? 'active' : '' ?>">
                <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div class="category-count"><?= $cat['news_count'] ?> haber</div>
                <?php if ($cat['description']): ?>
                    <div class="category-desc"><?= htmlspecialchars($cat['description']) ?></div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- News from selected category -->
    <?php if ($category): ?>
        <div style="margin-top: 40px;">
            <h2 style="margin-bottom: 20px; color: #111827;"><?= htmlspecialchars($category['name']) ?> Haberleri</h2>
            
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
                    <p style="font-size: 16px;">Bu kategoride henüz haber bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.category-card {
    display: block;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s;
    border: 3px solid transparent;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}

.category-card.active {
    border-color: #fff;
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
}

.category-name {
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 8px;
}

.category-count {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
    font-weight: 600;
}

.category-desc {
    font-size: 13px;
    opacity: 0.85;
    line-height: 1.4;
}
</style>
