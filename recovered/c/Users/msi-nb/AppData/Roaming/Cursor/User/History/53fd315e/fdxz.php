<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    
    if (empty($title) || empty($content)) {
        $error = 'Başlık ve içerik gereklidir.';
    } else {
        $user = getUser();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Resim yükleme
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($file_ext), $allowed_ext)) {
                $image_name = uniqid() . '.' . $file_ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            } else {
                $error = 'Geçersiz dosya formatı. Sadece JPG, PNG, GIF yüklenebilir.';
            }
        }
        
        if (!$error) {
            // Slug benzersizliğini kontrol et
            $stmt = $pdo->prepare("SELECT id FROM news WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            $stmt = $pdo->prepare("INSERT INTO news (title, slug, content, summary, image, category_id, author_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $slug, $content, $summary, $image_name, $category_id, $user['id'], $status])) {
                $success = 'Haber başarıyla eklendi!';
                header("refresh:2;url=index.php?page=admin&action=news");
            } else {
                $error = 'Haber eklenirken bir hata oluştu.';
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<h2>Yeni Haber Ekle</h2>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Başlık *</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    
    <div class="mb-3">
        <label for="summary" class="form-label">Özet</label>
        <textarea class="form-control" id="summary" name="summary" rows="3"></textarea>
        <small class="text-muted">Haber özeti (opsiyonel)</small>
    </div>
    
    <div class="mb-3">
        <label for="content" class="form-label">İçerik *</label>
        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Kategori</label>
        <select class="form-control" id="category_id" name="category_id">
            <option value="">Kategori Seçin</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="image" class="form-label">Resim</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        <small class="text-muted">JPG, PNG veya GIF formatında resim yükleyebilirsiniz.</small>
    </div>
    
    <div class="mb-3">
        <label for="status" class="form-label">Durum</label>
        <select class="form-control" id="status" name="status">
            <option value="draft">Taslak</option>
            <option value="published">Yayınla</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Kaydet</button>
    <a href="index.php?page=admin&action=news" class="btn btn-secondary">İptal</a>
</form>

