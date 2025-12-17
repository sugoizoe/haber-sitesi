<?php
if ($is_logged_in) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    
    if (!$user || !$email || !$pass) {
        $error = 'Tüm alanlar gerekli.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli e-posta girin.';
    } else {
        // Kontrol
        $chk = $pdo->prepare('SELECT id FROM users WHERE username=:u OR email=:e');
        $chk->execute([':u' => $user, ':e' => $email]);
        if ($chk->fetch()) {
            $error = 'Kullanıcı adı veya e-posta zaten var.';
        } else {
            // Oluştur
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (username, email, password, role, is_active) VALUES (:u, :e, :p, :r, 1)');
            $ins->execute([':u' => $user, ':e' => $email, ':p' => $hash, ':r' => 'User']);
            
            // Gir
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_username'] = $user;
            $_SESSION['user_role'] = 'User';
            header('Location: index.php');
            exit;
        }
    }
}
?>
<div class="container" style="max-width:400px; margin:40px auto;">
    <h1 style="text-align:center;">Kayıt Ol</h1>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Kullanıcı Adı</label>
            <input type="text" name="user" required>
        </div>
        <div class="form-group">
            <label>E-posta</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Şifre</label>
            <input type="password" name="pass" required>
        </div>
        <button type="submit" class="btn" style="width:100%;">Kayıt Ol</button>
        <p style="text-align:center; margin-top:12px; font-size:13px;">Zaten hesabin var mı? <a href="index.php?page=login">Giriş yap</a></p>
    </form>
</div>
