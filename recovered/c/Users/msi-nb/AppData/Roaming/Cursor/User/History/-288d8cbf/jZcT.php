<?php
$news_id = $_GET['id'] ?? 0;
$user = getUser();
$error = '';
$success = '';

// Haber bilgilerini getir
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$news_id]);
$news = $stmt->fetch();

if (!$news) {
    header("Location: index.php?page=admin&action=news");
    exit;
}

// Editor sadece kendi haberlerini düzenleyebilir
if (isEditor() && $news['author_id'] != $user['id']) {
    header("Location: index.php?page=admin&action=news");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    
    if (empty($title) || empty($content)) {
        $error = 'Başlık ve içerik gereklidir.';
    } else {
        $image_name = $news['image'];
        
        // Yeni resim yüklendi mi?
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Eski resmi sil
            if ($image_name && file_exists($upload_dir . $image_name)) {
                unlink($upload_dir . $image_name);
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
            $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, summary = ?, image = ?, category_id = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $content, $summary, $image_name, $category_id, $status, $news_id])) {
                $success = 'Haber başarıyla güncellendi!';
                $news = $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
                $stmt->execute([$news_id]);
                $news = $stmt->fetch();
            } else {
                $error = 'Haber güncellenirken bir hata oluştu.';
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<h2>Haber Düzenle</h2>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Başlık *</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
    </div>
    
    <div class="mb-3">
        <label for="summary" class="form-label">Özet</label>
        <textarea class="form-control" id="summary" name="summary" rows="3"><?php echo htmlspecialchars($news['summary'] ?? ''); ?></textarea>
    </div>
    
    <div class="mb-3">
        <label for="content" class="form-label">İçerik *</label>
        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($news['content']); ?></textarea>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Kategori</label>
        <select class="form-control" id="category_id" name="category_id">
            <option value="">Kategori Seçin</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo $news['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="image" class="form-label">Resim</label>
        <?php if ($news['image']): ?>
        <div class="mb-2">
            <img src="../uploads/<?php echo htmlspecialchars($news['image']); ?>" alt="Mevcut resim" style="max-height: 200px;" class="img-thumbnail">
        </div>
        <?php endif; ?>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        <small class="text-muted">Yeni resim yüklerseniz mevcut resim değiştirilecektir.</small>
    </div>
    
    <div class="mb-3">
        <label for="status" class="form-label">Durum</label>
        <select class="form-control" id="status" name="status">
            <option value="draft" <?php echo $news['status'] == 'draft' ? 'selected' : ''; ?>>Taslak</option>
            <option value="published" <?php echo $news['status'] == 'published' ? 'selected' : ''; ?>>Yayınla</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Güncelle</button>
    <a href="index.php?page=admin&action=news" class="btn btn-secondary">İptal</a>
</form>

