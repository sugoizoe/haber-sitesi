<?php
$user = getUser();
$error = '';
$success = '';

// Haber silme
if (isset($_GET['delete']) && $_GET['delete']) {
    $news_id = $_GET['delete'];
    
    // Editor sadece kendi haberlerini silebilir
    if (isEditor()) {
        $stmt = $pdo->prepare("SELECT author_id FROM news WHERE id = ?");
        $stmt->execute([$news_id]);
        $news = $stmt->fetch();
        if ($news && $news['author_id'] != $user['id']) {
            $error = 'Bu haberi silme yetkiniz yok.';
        } else {
            // Resmi sil
            $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
            $stmt->execute([$news_id]);
            $news = $stmt->fetch();
            if ($news && $news['image'] && file_exists(__DIR__ . '/../../uploads/' . $news['image'])) {
                unlink(__DIR__ . '/../../uploads/' . $news['image']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = ? AND author_id = ?");
            if ($stmt->execute([$news_id, $user['id']])) {
                $success = 'Haber başarıyla silindi.';
            }
        }
    } else {
        // Admin tüm haberleri silebilir
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$news_id]);
        $news = $stmt->fetch();
        if ($news && $news['image'] && file_exists(__DIR__ . '/../../uploads/' . $news['image'])) {
            unlink(__DIR__ . '/../../uploads/' . $news['image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        if ($stmt->execute([$news_id])) {
            $success = 'Haber başarıyla silindi.';
        }
    }
}

if ($error) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
}
if ($success) {
    echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
}
?>

<h2>Haber Yönetimi</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Durum</th>
                <th>Yazar</th>
                <th>Görüntülenme</th>
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
                                       ORDER BY n.created_at DESC");
                $stmt->execute([$user['id']]);
            } else {
                $stmt = $pdo->query("SELECT n.*, u.full_name as author_name, c.name as category_name 
                                    FROM news n 
                                    LEFT JOIN users u ON n.author_id = u.id 
                                    LEFT JOIN categories c ON n.category_id = c.id 
                                    ORDER BY n.created_at DESC");
            }
            $news_list = $stmt->fetchAll();
            
            foreach ($news_list as $item):
            ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo htmlspecialchars($item['category_name'] ?? 'Genel'); ?></td>
                <td>
                    <span class="badge bg-<?php echo $item['status'] == 'published' ? 'success' : 'warning'; ?>">
                        <?php echo $item['status'] == 'published' ? 'Yayında' : 'Taslak'; ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($item['author_name']); ?></td>
                <td><?php echo number_format($item['views']); ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($item['created_at'])); ?></td>
                <td>
                    <a href="index.php?page=news&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" target="_blank">Görüntüle</a>
                    <a href="index.php?page=admin&action=news_edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                    <a href="index.php?page=admin&action=news&delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu haberi silmek istediğinizden emin misiniz?')">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

