<?php
$error = '';
$success = '';

// Kullanıcı silme
if (isset($_GET['delete']) && $_GET['delete']) {
    $user_id = $_GET['delete'];
    
    // Kendini silme
    if ($user_id == $_SESSION['user_id']) {
        $error = 'Kendi hesabınızı silemezsiniz.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $success = 'Kullanıcı başarıyla silindi.';
        } else {
            $error = 'Kullanıcı silinirken bir hata oluştu.';
        }
    }
}

// Rol güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Kendi rolünü değiştirme
    if ($user_id == $_SESSION['user_id']) {
        $error = 'Kendi rolünüzü değiştiremezsiniz.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        if ($stmt->execute([$new_role, $user_id])) {
            $success = 'Kullanıcı rolü güncellendi.';
        } else {
            $error = 'Rol güncellenirken bir hata oluştu.';
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

<h2>Kullanıcı Yönetimi</h2>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı Adı</th>
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Kayıt Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
            
            foreach ($users as $u):
            ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <select name="role" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                            <option value="user" <?php echo $u['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="editor" <?php echo $u['role'] == 'editor' ? 'selected' : ''; ?>>Editor</option>
                            <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <input type="hidden" name="update_role" value="1">
                    </form>
                </td>
                <td><?php echo date('d.m.Y', strtotime($u['created_at'])); ?></td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="index.php?page=admin&action=users&delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

