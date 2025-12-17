<?php
if ($is_logged_in) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = $_POST['pass'] ?? '';
    
    if (!$user || !$pass) {
        $error = 'Kullanıcı adı ve şifre gerekli.';
    } else {
        // Admin/Editor giriş
        $stmt = $pdo->prepare('SELECT id, name, username, role, password FROM admin_users WHERE (username=:u OR email=:u) AND is_active=1');
        $stmt->execute([':u' => $user]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($pass, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: index.php');
            exit;
        }
        
        // User giriş
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE (username=:u OR email=:u) AND is_active=1');
        $stmt->execute([':u' => $user]);
        $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_row && password_verify($pass, $user_row['password'])) {
            $_SESSION['user_id'] = $user_row['id'];
            $_SESSION['user_username'] = $user_row['username'];
            $_SESSION['user_role'] = 'User';
            header('Location: index.php');
            exit;
        }
        
        $error = 'Kullanıcı adı veya şifre hatalı.';
    }
}
?>
<div class="container" style="max-width:400px; margin:40px auto;">
    <h1 style="text-align:center;">Giriş Yap</h1>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Kullanıcı Adı / E-posta</label>
            <input type="text" name="user" required autofocus>
        </div>
        <div class="form-group">
            <label>Şifre</label>
            <input type="password" name="pass" required>
        </div>
        <button type="submit" class="btn" style="width:100%;">Giriş</button>
        <p style="text-align:center; margin-top:12px; font-size:13px;">Hesabın yok mu? <a href="index.php?page=register">Kayıt ol</a></p>
    </form>
</div>
