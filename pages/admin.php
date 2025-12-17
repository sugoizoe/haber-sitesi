<?php
// Admin Panel - sadece Admin role
if ($user_role !== 'Admin') {
    echo '<div class="container"><div class="error">Erişim reddedildi.</div></div>';
    return;
}

$action = $_GET['action'] ?? 'dashboard';
$action = preg_replace('/[^a-z_]/', '', $action);

// User ekleme
if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $user = trim($_POST['user'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] === 'Editor' ? 'Editor' : 'Admin';
    $pass = $_POST['pass'] ?? '';
    
    if ($name && $user && $email && $pass) {
        $chk = $pdo->prepare('SELECT id FROM admin_users WHERE username=:u OR email=:e');
        $chk->execute([':u' => $user, ':e' => $email]);
        if (!$chk->fetch()) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO admin_users (name, username, email, password, role, is_active) VALUES (:n, :u, :e, :p, :r, 1)');
            $ins->execute([':n' => $name, ':u' => $user, ':e' => $email, ':p' => $hash, ':r' => $role]);
            $success = 'Hesap oluşturuldu.';
        }
    }
}

// User silme
if ($action === 'delete_user' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id !== $_SESSION['admin_id']) {
        $pdo->prepare('DELETE FROM admin_users WHERE id=?')->execute([$id]);
        $success = 'Hesap silindi.';
    }
}

$users = $pdo->query('SELECT id, name, username, email, role, is_active FROM admin_users ORDER BY role, username')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Admin Panel</h1>
    
    <?php if (isset($success)): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    
    <?php if ($action === 'add'): ?>
        <h2>Yeni Admin/Editör Ekle</h2>
        <form method="post" action="index.php?page=admin&action=add_user" style="max-width:500px;">
            <div class="form-group"><label>Ad</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Kullanıcı Adı</label><input type="text" name="user" required></div>
            <div class="form-group"><label>E-posta</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Şifre</label><input type="password" name="pass" required></div>
            <div class="form-group"><label>Rol</label><select name="role"><option>Admin</option><option>Editor</option></select></div>
            <button type="submit" class="btn">Ekle</button>
        </form>
    <?php else: ?>
        <div style="margin-bottom:16px;">
            <a href="index.php?page=admin&action=add" class="btn">+ Yeni Admin/Editör</a>
        </div>
        <table>
            <thead><tr><th>ID</th><th>Ad</th><th>Kullanıcı</th><th>E-posta</th><th>Rol</th><th>Durum</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge <?= strtolower($u['role']) ?>"><?= $u['role'] ?></span></td>
                    <td><?= $u['is_active'] ? 'Aktif' : 'Pasif' ?></td>
                    <td><?php if ($u['id'] !== $_SESSION['admin_id']): ?><a href="index.php?page=admin&action=delete_user&id=<?= (int)$u['id'] ?>" onclick="return confirm('Sil?')">Sil</a><?php else: ?>-<?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
