<?php
// admin/login_process.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Hard-coded credentials for course project
if ($username === 'admin' && $password === '12345') {
    session_regenerate_id(true);
    $_SESSION['admin'] = true;
    // Redirect to admin dashboard (same folder)
    header('Location: index.php');
    exit;
}

// On failure, set error and return to login
$_SESSION['error'] = 'Kullanıcı adı veya şifre yanlış';
header('Location: login.php');
exit;
<?php
// Processes admin login POST and redirects accordingly
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Hard-coded credentials for course project (change for production)
if ($username === 'admin' && $password === '12345') {
    // Regenerate session id to be safer
    session_regenerate_id(true);
    $_SESSION['is_admin'] = true;
    header('Location: index.php');
    exit;
} else {
    $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
    header('Location: login.php');
    exit;
}
