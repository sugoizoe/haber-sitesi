<?php
session_start();

// Kullanıcı oturum kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcı bilgilerini getir
function getUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Rol kontrolü
function hasRole($required_role) {
    $user = getUser();
    if (!$user) {
        return false;
    }
    
    $roles = ['user' => 1, 'editor' => 2, 'admin' => 3];
    $user_level = $roles[$user['role']] ?? 0;
    $required_level = $roles[$required_role] ?? 0;
    
    return $user_level >= $required_level;
}

// Admin kontrolü
function isAdmin() {
    return hasRole('admin');
}

// Editor kontrolü
function isEditor() {
    return hasRole('editor');
}

// Kullanıcıyı yönlendir (giriş yapmamışsa login sayfasına)
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php?page=login");
        exit;
    }
}

// Rol bazlı yönlendirme
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header("Location: index.php");
        exit;
    }
}
?>

