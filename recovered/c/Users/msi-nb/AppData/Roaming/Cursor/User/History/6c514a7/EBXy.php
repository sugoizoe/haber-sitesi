<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$current_page = $_GET['page'] ?? 'home';
$user = getUser();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_page == 'home' ? 'Ana Sayfa' : ucfirst($current_page); ?> - Haber Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar-brand { font-weight: bold; }
        .news-card { transition: transform 0.3s; }
        .news-card:hover { transform: translateY(-5px); }
        .news-image { height: 200px; object-fit: cover; }
        .hero-image { height: 400px; object-fit: cover; }
        .category-badge { font-size: 0.85rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-newspaper"></i> Haber Sitesi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'home' ? 'active' : ''; ?>" href="index.php">Ana Sayfa</a>
                    </li>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                    $categories = $stmt->fetchAll();
                    foreach ($categories as $category) {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="index.php?page=category&id=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($user): ?>
                        <?php if (isEditor() || isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=admin">
                                    <i class="bi bi-gear"></i> Yönetim Paneli
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($user['full_name']); ?>
                                <span class="badge bg-secondary"><?php echo ucfirst($user['role']); ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=profile">Profilim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?page=logout">Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

