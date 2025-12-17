<?php
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Yönetim Paneli</h1>
    
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="index.php?page=admin" class="list-group-item list-group-item-action active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="index.php?page=admin&action=news" class="list-group-item list-group-item-action">
                    <i class="bi bi-newspaper"></i> Haber Yönetimi
                </a>
                <a href="index.php?page=admin&action=news_add" class="list-group-item list-group-item-action">
                    <i class="bi bi-plus-circle"></i> Yeni Haber Ekle
                </a>
                <?php if (isAdmin()): ?>
                <a href="index.php?page=admin&action=users" class="list-group-item list-group-item-action">
                    <i class="bi bi-people"></i> Kullanıcı Yönetimi
                </a>
                <a href="index.php?page=admin&action=categories" class="list-group-item list-group-item-action">
                    <i class="bi bi-tags"></i> Kategori Yönetimi
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-9">
            <?php
            $action = $_GET['action'] ?? 'dashboard';
            
            switch ($action) {
                case 'news':
                    include 'news_list.php';
                    break;
                case 'news_add':
                    include 'news_add.php';
                    break;
                case 'news_edit':
                    include 'news_edit.php';
                    break;
                case 'users':
                    if (isAdmin()) {
                        include 'users_list.php';
                    } else {
                        echo '<div class="alert alert-danger">Bu sayfaya erişim yetkiniz yok.</div>';
                    }
                    break;
                case 'categories':
                    if (isAdmin()) {
                        include 'categories_list.php';
                    } else {
                        echo '<div class="alert alert-danger">Bu sayfaya erişim yetkiniz yok.</div>';
                    }
                    break;
                default:
                    include 'dashboard.php';
                    break;
            }
            ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

