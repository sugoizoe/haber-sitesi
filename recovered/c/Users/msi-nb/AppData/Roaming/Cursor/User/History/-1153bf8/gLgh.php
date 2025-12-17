<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Öne Çıkan Haber -->
    <?php
    $stmt = $pdo->query("SELECT n.*, u.full_name as author_name, c.name as category_name 
                         FROM news n 
                         LEFT JOIN users u ON n.author_id = u.id 
                         LEFT JOIN categories c ON n.category_id = c.id 
                         WHERE n.status = 'published' 
                         ORDER BY n.created_at DESC 
                         LIMIT 1");
    $featured = $stmt->fetch();
    ?>
    
    <?php if ($featured): ?>
    <div class="card mb-4">
        <?php if ($featured['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($featured['image']); ?>" class="card-img-top hero-image" alt="<?php echo htmlspecialchars($featured['title']); ?>">
        <?php endif; ?>
        <div class="card-body">
            <span class="badge bg-primary category-badge"><?php echo htmlspecialchars($featured['category_name'] ?? 'Genel'); ?></span>
            <h1 class="card-title mt-2"><?php echo htmlspecialchars($featured['title']); ?></h1>
            <p class="card-text"><?php echo htmlspecialchars($featured['summary'] ?? substr($featured['content'], 0, 200) . '...'); ?></p>
            <a href="index.php?page=news&id=<?php echo $featured['id']; ?>" class="btn btn-primary">Devamını Oku</a>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-person"></i> <?php echo htmlspecialchars($featured['author_name']); ?> | 
                <i class="bi bi-calendar"></i> <?php echo date('d.m.Y H:i', strtotime($featured['created_at'])); ?>
            </small>
        </div>
    </div>
    <?php endif; ?>

    <!-- Kategorilere Göre Haberler -->
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        foreach ($categories as $category) {
            $stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name 
                                   FROM news n 
                                   LEFT JOIN users u ON n.author_id = u.id 
                                   WHERE n.category_id = ? AND n.status = 'published' 
                                   ORDER BY n.created_at DESC 
                                   LIMIT 3");
            $stmt->execute([$category['id']]);
            $news = $stmt->fetchAll();
            
            if (count($news) > 0) {
        ?>
        <div class="col-12 mb-4">
            <h2 class="border-bottom pb-2">
                <a href="index.php?page=category&id=<?php echo $category['id']; ?>" class="text-decoration-none text-dark">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            </h2>
            <div class="row">
                <?php foreach ($news as $item): ?>
                <div class="col-md-4 mb-3">
                    <div class="card news-card h-100">
                        <?php if ($item['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top news-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['summary'] ?? substr($item['content'], 0, 150) . '...'); ?></p>
                            <div class="mt-auto">
                                <a href="index.php?page=news&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">Oku</a>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($item['author_name']); ?><br>
                                    <i class="bi bi-calendar"></i> <?php echo date('d.m.Y', strtotime($item['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-end mt-2">
                <a href="index.php?page=category&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-secondary">Tüm <?php echo htmlspecialchars($category['name']); ?> Haberleri →</a>
            </div>
        </div>
        <?php
            }
        }
        ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

