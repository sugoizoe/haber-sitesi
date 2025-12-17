<?php
$user = getUser();

// İstatistikler
$stmt = $pdo->query("SELECT COUNT(*) as total FROM news WHERE status = 'published'");
$total_published = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM news WHERE status = 'draft'");
$total_draft = $stmt->fetch()['total'];

if (isEditor()) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM news WHERE author_id = ?");
    $stmt->execute([$user['id']]);
    $my_news = $stmt->fetch()['total'];
} else {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM news");
    $my_news = $stmt->fetch()['total'];
}

if (isAdmin()) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    $total_categories = $stmt->fetch()['total'];
}
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Yayınlanan Haberler</h5>
                <h2><?php echo $total_published; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Taslak Haberler</h5>
                <h2><?php echo $total_draft; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><?php echo isEditor() ? 'Benim Haberlerim' : 'Toplam Haber'; ?></h5>
                <h2><?php echo $my_news; ?></h2>
            </div>
        </div>
    </div>
    <?php if (isAdmin()): ?>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Toplam Kullanıcı</h5>
                <h2><?php echo $total_users; ?></h2>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<h3>Son Haberler</h3>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Durum</th>
                <th>Yazar</th>
                <th>Tarih</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isEditor()) {
                $stmt = $pdo->prepare("SELECT n.*, u.full_name as author_name, c.name as category_name 
                                       FROM news n 
                                       LEFT JOIN users u ON n.author_id = u.id 
                                       LEFT JOIN categories c ON n.category_id = c.id 
                                       WHERE n.author_id = ? 
                                       ORDER BY n.created_at DESC 
                                       LIMIT 10");
                $stmt->execute([$user['id']]);
            } else {
                $stmt = $pdo->query("SELECT n.*, u.full_name as author_name, c.name as category_name 
                                    FROM news n 
                                    LEFT JOIN users u ON n.author_id = u.id 
                                    LEFT JOIN categories c ON n.category_id = c.id 
                                    ORDER BY n.created_at DESC 
                                    LIMIT 10");
            }
            $recent_news = $stmt->fetchAll();
            
            foreach ($recent_news as $item):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td>
                    <span class="badge bg-<?php echo $item['status'] == 'published' ? 'success' : 'warning'; ?>">
                        <?php echo $item['status'] == 'published' ? 'Yayında' : 'Taslak'; ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($item['author_name']); ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($item['created_at'])); ?></td>
                <td>
                    <a href="index.php?page=admin&action=news_edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

