<?php
// Editor/Content Panel - Admin ve Editor erişebilir
if (!in_array($user_role, ['Admin', 'Editor'])) {
    echo '<div class="container"><div class="error">Erişim reddedildi.</div></div>';
    return;
}

$success = $error = '';
$action = $_GET['action'] ?? 'list';
$action = preg_replace('/[^a-z_]/', '', $action);
$admin_id = $_SESSION['admin_id'] ?? 0;
$upload_error = '';

// Haber ekle
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cat_id = (int)($_POST['category'] ?? 1);
    $image = $_FILES['image'] ?? null;
    $image_url = '';
    
    // Resim yükleme
    if ($image && $image['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($image['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $uploads_dir = __DIR__ . '/../uploads';
                if (!is_dir($uploads_dir)) {
                    @mkdir($uploads_dir, 0755, true);
                }
                if (!is_dir($uploads_dir) || !is_writable($uploads_dir)) {
                    $upload_error = 'Yükleme klasörü yazılabilir değil.';
                } else {
                    $filename = 'news_' . time() . '.' . $ext;
                    $path = $uploads_dir . '/' . $filename;
                    if (move_uploaded_file($image['tmp_name'], $path)) {
                        $image_url = 'uploads/' . $filename; // store relative path consistently
                    } else {
                        $upload_error = 'Resim yüklenemedi.';
                    }
                }
            } else {
                $upload_error = 'Geçersiz dosya türü. Sadece jpg, jpeg, png, gif.';
            }
        } else {
            $upload_error = 'Resim yükleme hatası (kod: ' . (int)$image['error'] . ').';
        }
    }
    
    if ($title && $content) {
        $ins = $pdo->prepare('INSERT INTO news (title, content, image_url, category_id, admin_user_id, is_published) VALUES (:t, :c, :i, :cat, :a, 1)');
        $ins->execute([':t' => $title, ':c' => $content, ':i' => $image_url, ':cat' => $cat_id, ':a' => $admin_id]);
        $success = 'Haber oluşturuldu.' . ($upload_error ? ' (Resim: ' . $upload_error . ')' : '');
    } else {
        $error = 'Başlık ve içerik gerekli.';
    }
}

// Haber sil (sadece kendi haberi silebilir)
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $chk = $pdo->prepare('SELECT admin_user_id FROM news WHERE id=?');
    $chk->execute([$id]);
    $n = $chk->fetch();
    if ($n && ($n['admin_user_id'] == $admin_id || $user_role === 'Admin')) {
        $pdo->prepare('DELETE FROM news WHERE id=?')->execute([$id]);
        $success = 'Haber silindi.';
    }
}

// Haberler listesi
if ($user_role === 'Admin') {
    $news = $pdo->query('SELECT id, title, admin_user_id, created_at FROM news ORDER BY created_at DESC')->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT id, title, created_at FROM news WHERE admin_user_id=? ORDER BY created_at DESC');
    $stmt->execute([$admin_id]);
    $news = $stmt->fetchAll();
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
?>

<div class="container">
    <h1><?= $user_role === 'Admin' ? 'Tüm Haberler' : 'Benim Haberlerim' ?></h1>
    
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    
    <?php if ($action === 'add'): ?>
        <h2>Yeni Haber Ekle</h2>
        <form method="post" enctype="multipart/form-data" style="max-width:600px;">
            <div class="form-group"><label>Başlık</label><input type="text" name="title" required></div>
            <div class="form-group"><label>İçerik</label><textarea name="content" required></textarea></div>
            <div class="form-group"><label>Kategori</label><select name="category"><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label>Resim</label><input type="file" name="image" accept="image/*"></div>
            <button type="submit" class="btn">Kaydet</button>
        </form>
    <?php else: ?>
        <div style="margin-bottom:16px;">
            <a href="index.php?page=editor&action=add" class="btn">+ Yeni Haber</a>
        </div>
        <table>
            <thead><tr><th>ID</th><th>Başlık</th><th>Tarih</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($news as $n): ?>
                <tr>
                    <td><?= (int)$n['id'] ?></td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></td>
                    <td><a href="index.php?page=editor&action=delete&id=<?= (int)$n['id'] ?>" onclick="return confirm('Sil?')">Sil</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
