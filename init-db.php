<?php
require_once __DIR__ . '/config.php';

$message = '';
$error = '';

try {
    // First, create database if it doesn't exist
    $pdo_no_db = new PDO(
        "mysql:host=$db_host;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $pdo_no_db->exec("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Now connect to the database
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Drop existing tables
    $pdo->exec("DROP TABLE IF EXISTS comments");
    $pdo->exec("DROP TABLE IF EXISTS news");
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS admin_users");
    
    // Create tables
    $pdo->exec("CREATE TABLE admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        username VARCHAR(100) UNIQUE NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('Admin','Editor') NOT NULL DEFAULT 'Editor',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        excerpt VARCHAR(500),
        image_url VARCHAR(255) NULL,
        admin_user_id INT NULL,
        category_id INT NULL,
        is_published TINYINT(1) DEFAULT 1,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_news_admin FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
        CONSTRAINT fk_news_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $pdo->exec("CREATE TABLE comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        news_id INT NOT NULL,
        user_id INT NULL,
        author_name VARCHAR(100) NOT NULL,
        author_email VARCHAR(100),
        content TEXT NOT NULL,
        status ENUM('pending','approved','rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_comments_news FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
        CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Insert categories
    $pdo->exec("INSERT INTO categories (name, slug, description) VALUES 
        ('Genel', 'genel', 'Bartın ve Amasra\'dan güncel haberler'),
        ('Kültür', 'kultur', 'Bartın\'ın kültür ve turizm haberleri'),
        ('Yerel', 'yerel', 'Yerel etkinlikler ve gelişmeler')");
    
    // Insert admin users
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $editor_pass = password_hash('editor123', PASSWORD_DEFAULT);
    $user_pass = password_hash('user123', PASSWORD_DEFAULT);
    
    $pdo->exec("INSERT INTO admin_users (name, username, email, password, role) VALUES 
        ('Yönetici', 'admin', 'admin@example.com', '$admin_pass', 'Admin'),
        ('Editör', 'editor', 'editor@example.com', '$editor_pass', 'Editor')");
    
    // Insert sample user
    $pdo->exec("INSERT INTO users (username, email, password) VALUES 
        ('user', 'user@example.com', '$user_pass')");
    
    // Insert demo news articles
    $news_items = [
        [
            'title' => 'Ağlayan Ağaç Ziyareti Yoğunluğu',
            'excerpt' => 'Amasra\'nın ünlü Ağlayan Ağaç noktası turistlerin yoğun ilgisiyle karşılaşıyor. Hafta sonları ziyaretçi akını yaşanıyor.',
            'content' => 'Amasra\'nın en önemli doğal anıtlarından biri olan Ağlayan Ağaç, özellikle hafta sonları yoğun ziyaretçi akınına sahne oluyor. Tarihi ağacın benzersiz görüntüsü fotoğraf meraklılarının da ilgisini çekiyor. Yerel yetkililer bölgenin korunması için çeşitli önlemler alıyor.',
            'category_id' => 1,
            'admin_user_id' => 2
        ],
        [
            'title' => 'Turist 300 Kişilik Rus Gemi Ziyareti',
            'excerpt' => '300 kişilik Rus turist gemisi Amasra limanına demir attı. Turistler tarihi Amasra\'yı gezmeye başladı.',
            'content' => 'Bu sabah saatlerinde Amasra limanına 300 kişilik Rus turist gemisi demir attı. Rusya\'dan gelen turistler, Amasra\'nın tarihi dokusunu, kalelerini ve doğal güzelliklerini gezmek için sahile çıktı. Yerel esnaf yoğun ilgiyle karşılaştı ve turistlere Bartın yöresel ürünlerini tanıttı.',
            'category_id' => 1,
            'admin_user_id' => 1
        ],
        [
            'title' => 'Dün Gece Yarısı Yaşanan Kavga Detayları',
            'excerpt' => 'Amasra\'da dün gece yarısı meydana gelen kavgada 2 kişi yaralandı. Olay yerine polis ve sağlık ekipleri sevk edildi.',
            'content' => 'Amasra ilçesinde dün gece yarısı saat 00:30 sıralarında meydana gelen kavgada iki kişi yaralandı. Olay, merkez mahallede bulunan bir kahvehanede çıkan tartışmanın büyümesi sonucu meydana geldi. Yaralılar Bartın Devlet Hastanesi\'ne kaldırıldı. Polis olayla ilgili soruşturma başlattı.',
            'category_id' => 1,
            'admin_user_id' => 2
        ],
        [
            'title' => 'Bartın\'da Çarşıda Olması Gerekenler Röportajı',
            'excerpt' => 'Bartın çarşısında esnaf ve vatandaşlarla röportaj yaptık. Çarşıda olması gerekenler hakkında görüşler aldık.',
            'content' => 'Bartın çarşısında esnaf ve vatandaşlarla yaptığımız röportajda, çarşının gelişimi için neler yapılması gerektiği konusunda önemli fikirler ortaya çıktı. Esnaflar otopark ihtiyacı, çevre düzenlemesi ve tanıtım eksikliği konularında görüş bildirdi. Vatandaşlar ise daha fazla sosyal alan ve yeşil alan talep etti.',
            'category_id' => 1,
            'admin_user_id' => 1
        ],
        [
            'title' => 'İnkumu\'ndaki Ani Hava Değişimi',
            'excerpt' => 'İnkumu sahilinde ani hava değişimi yaşandı. Plaja gelen vatandaşlar sağanak yağmurdan etkilendi.',
            'content' => 'İnkumu sahilinde bugün öğleden sonra yaşanan ani hava değişimi plajdaki vatandaşları hazırlıksız yakaladı. Güneşli havadan aniden sağanak yağmura geçildi. Meteoroloji yetkilileri önümüzdeki günlerde benzer hava değişimlerinin olabileceği konusunda uyarıda bulundu. Sahil esnafı ziyaretçileri barınakta korudu.',
            'category_id' => 1,
            'admin_user_id' => 2
        ],
        [
            'title' => 'Çekiciler Çarşısı Güzellikleri',
            'excerpt' => 'Bartın\'ın tarihi Çekiciler Çarşısı, restore edilen yapılarıyla büyük ilgi görüyor. Turistler ve fotoğrafçıların gözdesi.',
            'content' => 'Bartın\'ın tarihi Çekiciler Çarşısı, son yıllarda yapılan restorasyon çalışmaları sonrası şehrin en gözde mekanlarından biri haline geldi. Osmanlı döneminden kalma ahşap evler, taş sokaklar ve tarihi atmosfer fotoğraf tutkunlarının ve turistlerin ilgi odağı. Çarşıda bulunan el sanatları atölyeleri ve yöresel ürün satan dükkânlar ziyaretçilere unutulmaz bir deneyim sunuyor.',
            'category_id' => 1,
            'admin_user_id' => 1
        ]
    ];
    
    // Insert news items
    foreach ($news_items as $news) {
        $stmt = $pdo->prepare("INSERT INTO news (title, excerpt, content, category_id, admin_user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $news['title'],
            $news['excerpt'],
            $news['content'],
            $news['category_id'],
            $news['admin_user_id']
        ]);
    }
    
    $message = '✅ Kurulum başarılı! <br><br>
        <strong>Yönetici Girişi:</strong> admin / admin123<br>
        <strong>Editör Girişi:</strong> editor / editor123<br>
        <strong>Üye Girişi:</strong> user / user123<br><br>
        Demo haberler eklenmiştir. <a href="index.php">Anasayfaya Git →</a>';
        
} catch (PDOException $e) {
    $error = 'Hata: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Kurulumu</title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            text-align: center;
        }
        h1 {
            margin: 0 0 30px;
            color: #111827;
            font-size: 28px;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        .message {
            background: #dcfce7;
            color: #166534;
            padding: 20px;
            border-radius: 8px;
            line-height: 1.8;
            border-left: 4px solid #22c55e;
        }
        a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6366f1;
            color: #fff;
            border-radius: 8px;
            transition: all 0.2s;
        }
        a:hover {
            background: #4f46e5;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Veritabanı Kurulumu</h1>
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <?php if($message): ?>
            <div class="message">
                <?= $message ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
