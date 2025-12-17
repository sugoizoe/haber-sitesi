<?php
$error = '';
$success = '';

// Kategori ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'] ?? '';
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'] ?? '';
    
    if (empty($name)) {
        $error = 'Kategori adı gereklidir.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $slug, $description])) {
            $success = 'Kategori başarıyla eklendi.';
        } else {
            $error = 'Kategori eklenirken bir hata oluştu.';
        }
    }
}

// Kategori silme
if (isset($_GET['delete']) && $_GET['delete']) {
    $category_id = $_GET['delete'];
    
    // Bu kategoride haber var mı kontrol et
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM news WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $news_count = $stmt->fetch()['count'];
    
    if ($news_count > 0) {
        $error = 'Bu kategoride ' . $news_count . ' haber bulunmaktadır. Önce haberleri silin veya farklı bir kategoriye taşıyın.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            $success = 'Kategori başarıyla silindi.';
        } else {
            $error = 'Kategori silinirken bir hata oluştu.';
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

<h2>Kategori Yönetimi</h2>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Yeni Kategori Ekle</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="name" placeholder="Kategori Adı" required>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="description" placeholder="Açıklama (opsiyonel)">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_category" class="btn btn-primary w-100">Ekle</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kategori Adı</th>
                <th>Slug</th>
                <th>Açıklama</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT c.*, COUNT(n.id) as news_count FROM categories c LEFT JOIN news n ON c.id = n.category_id GROUP BY c.id ORDER BY c.name");
            $categories = $stmt->fetchAll();
            
            foreach ($categories as $category):
            ?>
            <tr>
                <td><?php echo $category['id']; ?></td>
                <td><?php echo htmlspecialchars($category['name']); ?></td>
                <td><code><?php echo htmlspecialchars($category['slug']); ?></code></td>
                <td><?php echo htmlspecialchars($category['description'] ?? '-'); ?></td>
                <td>
                    <span class="badge bg-info"><?php echo $category['news_count']; ?> haber</span>
                    <a href="index.php?page=admin&action=categories&delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

