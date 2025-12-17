<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    $errors = [];
    
    if (empty($username)) $errors[] = 'Kullanıcı adı gerekli.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli email gerekli.';
    if (empty($password)) $errors[] = 'Şifre gerekli.';
    if ($password !== $password_confirm) $errors[] = 'Şifreler eşleşmiyor.';
    
    if (empty($errors)) {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Bu kullanıcı adı veya email zaten kayıtlı.';
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                $stmt->execute([$username, $email, $hashed_password]);
                
                // Auto-login
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $username;
                $_SESSION['user_role'] = 'User';
                
                header('Location: ?page=home&success=1');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
        }
    }
    
    // If registration failed, redirect back
    $error_msg = implode(' | ', $errors);
    header("Location: ?page=home&error=" . urlencode($error_msg));
    exit;
}
?>
