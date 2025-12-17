<?php
$news_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name, c.name as category_name 
                       FROM news n 
                       LEFT JOIN users u ON n.author_id = u.id 
                       LEFT JOIN categories c ON n.category_id = c.id 
                       WHERE n.id = ? AND n.status = 'published'");
$stmt->execute([$news_id]);
$news = $stmt->fetch();

if (!$news) {
    header("Location: index.php");
    exit;
}

// Görüntülenme sayısını artır
$stmt = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
$stmt->execute([$news_id]);

include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=category&id=<?php echo $news['category_id']; ?>"><?php echo htmlspecialchars($news['category_name'] ?? 'Genel'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($news['title']); ?></li>
        </ol>
    </nav>

    <article>
        <h1 class="mb-3"><?php echo htmlspecialchars($news['title']); ?></h1>
        
        <div class="d-flex gap-3 mb-3 text-muted">
            <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($news['author_name']); ?></span>
            <span><i class="bi bi-calendar"></i> <?php echo date('d.m.Y H:i', strtotime($news['created_at'])); ?></span>
            <span><i class="bi bi-eye"></i> <?php echo number_format($news['views']); ?> görüntülenme</span>
            <span class="badge bg-primary"><?php echo htmlspecialchars($news['category_name'] ?? 'Genel'); ?></span>
        </div>

        <?php if ($news['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($news['image']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($news['title']); ?>">
        <?php endif; ?>

        <?php if ($news['summary']): ?>
        <div class="alert alert-light border" role="alert">
            <strong>Özet:</strong> <?php echo nl2br(htmlspecialchars($news['summary'])); ?>
        </div>
        <?php endif; ?>

        <div class="news-content">
            <?php echo nl2br(htmlspecialchars($news['content'])); ?>
        </div>
    </article>

    <!-- Benzer Haberler -->
    <hr class="my-5">
    <h3 class="mb-4">Benzer Haberler</h3>
    <div class="row">
        <?php
        $stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name 
                               FROM news n 
                               LEFT JOIN users u ON n.author_id = u.id 
                               WHERE n.category_id = ? AND n.id != ? AND n.status = 'published' 
                               ORDER BY n.created_at DESC 
                               LIMIT 3");
        $stmt->execute([$news['category_id'], $news_id]);
        $related = $stmt->fetchAll();
        
        foreach ($related as $item):
        ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <?php if ($item['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top news-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($item['content'], 0, 100) . '...'); ?></p>
                    <a href="index.php?page=news&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Oku</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

