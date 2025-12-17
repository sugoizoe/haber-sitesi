<?php
require_once __DIR__ . '/config.php';
$message = '';
$error = '';
try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdo->exec("DROP TABLE IF EXISTS comments");
    $pdo->exec("DROP TABLE IF EXISTS news");
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS admin_users");
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, username VARCHAR(100) UNIQUE NOT NULL, email VARCHAR(120) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, role ENUM('Admin','Editor') NOT NULL DEFAULT 'Editor', is_active TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) UNIQUE NOT NULL, email VARCHAR(120) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, role ENUM('User') NOT NULL DEFAULT 'User', is_active TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE, slug VARCHAR(100) NOT NULL UNIQUE, description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image_url VARCHAR(255) NULL, admin_user_id INT NULL, category_id INT NULL, is_published TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, CONSTRAINT fk_news_admin FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE SET NULL, CONSTRAINT fk_news_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (id INT AUTO_INCREMENT PRIMARY KEY, news_id INT NOT NULL, user_id INT NULL, author_name VARCHAR(100) NOT NULL, author_email VARCHAR(100), content TEXT NOT NULL, status ENUM('pending','approved','rejected') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_comments_news FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE, CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("INSERT IGNORE INTO categories (name, slug, description) VALUES ('Genel','genel','Genel haberler'),('Spor','spor','Spor haberleri'),('Teknoloji','teknoloji','Teknoloji haberleri')");
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $editor_pass = password_hash('editor123', PASSWORD_DEFAULT);
    $user_pass = password_hash('user123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admin_users (name,username,email,password,role) VALUES ('Admin','admin','admin@example.com','$admin_pass','Admin')");
    $pdo->exec("INSERT IGNORE INTO admin_users (name,username,email,password,role) VALUES ('Editör','editor','editor@example.com','$editor_pass','Editor')");
    $pdo->exec("INSERT IGNORE INTO users (username,email,password) VALUES ('user','user@example.com','$user_pass')");
    $message = 'Kurulum başarılı! Admin: admin/admin123 | Editor: editor/editor123 | User: user/user123';
} catch (PDOException $e) {
    $error = 'Hata: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kurulum</title><style>body { font-family: system-ui; display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f6f7fb; } .container { background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.1); max-width:600px; } h1 { margin:0 0 20px; } .error { background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; } .message { background:#dcfce7; color:#166534; padding:12px; border-radius:8px; line-height:1.6; } a { color:#6366f1; text-decoration:none; font-weight:600; }</style></head><body><div class="container"><h1>Kurulum</h1><?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?><?php if($message): ?><div class="message"><?= htmlspecialchars($message) ?><br><a href="index.php">Anasayfaya Git →</a></div><?php endif; ?></div></body></html>
