<?php
$category_id = $_GET['id'] ?? 0;
$page = $_GET['p'] ?? 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Kategori bilgisini getir
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: index.php");
    exit;
}

// Toplam haber sayısı
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM news WHERE category_id = ? AND status = 'published'");
$stmt->execute([$category_id]);
$total_news = $stmt->fetch()['total'];
$total_pages = ceil($total_news / $per_page);

// Haberleri getir
$stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name 
                       FROM news n 
                       LEFT JOIN users u ON n.author_id = u.id 
                       WHERE n.category_id = ? AND n.status = 'published' 
                       ORDER BY n.created_at DESC 
                       LIMIT ? OFFSET ?");
$stmt->execute([$category_id, $per_page, $offset]);
$news = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo htmlspecialchars($category['name']); ?></h1>
    
    <?php if ($category['description']): ?>
    <p class="text-muted mb-4"><?php echo htmlspecialchars($category['description']); ?></p>
    <?php endif; ?>

    <?php if (count($news) > 0): ?>
    <div class="row">
        <?php foreach ($news as $item): ?>
        <div class="col-md-4 mb-4">
            <div class="card news-card h-100">
                <?php if ($item['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top news-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($item['summary'] ?? substr($item['content'], 0, 150) . '...'); ?></p>
                    <div class="mt-auto">
                        <a href="index.php?page=news&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Devamını Oku</a>
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

    <!-- Sayfalama -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Sayfa navigasyonu">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=category&id=<?php echo $category_id; ?>&p=<?php echo $page - 1; ?>">Önceki</a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="index.php?page=category&id=<?php echo $category_id; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="index.php?page=category&id=<?php echo $category_id; ?>&p=<?php echo $page + 1; ?>">Sonraki</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Bu kategoride henüz haber bulunmamaktadır.
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

