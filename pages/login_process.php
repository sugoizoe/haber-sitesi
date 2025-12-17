<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'user';
    
    if (empty($username_or_email) || empty($password)) {
        $error = 'Kullanıcı adı/email ve şifre gerekli.';
    } else {
        if ($user_type === 'admin') {
            // Admin/Editor login
            try {
                $stmt = $pdo->prepare('SELECT id, username, role, password FROM admin_users WHERE username = ? OR email = ? LIMIT 1');
                $stmt->execute([$username_or_email, $username_or_email]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin && password_verify($password, $admin['password']) && $admin['role'] !== 'User') {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_name'] = $admin['username'];
                    $_SESSION['user_role'] = $admin['role'];
                    header('Location: ?page=home');
                    exit;
                } else {
                    $error = 'Yanlış kullanıcı adı/email veya şifre.';
                }
            } catch (PDOException $e) {
                $error = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            // User login
            try {
                $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1');
                $stmt->execute([$username_or_email, $username_or_email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['username'];
                    $_SESSION['user_role'] = 'User';
                    header('Location: ?page=home');
                    exit;
                } else {
                    $error = 'Yanlış kullanıcı adı/email veya şifre.';
                }
            } catch (PDOException $e) {
                $error = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
    
    // If login failed, redirect back to home with error
    header("Location: ?page=home&error=" . urlencode($error));
    exit;
}
?>
