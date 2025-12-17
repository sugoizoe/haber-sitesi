<?php
try {
    // Get published news with details
    $stmt = $pdo->prepare('SELECT n.*, c.name as category_name, a.username as author_name 
                          FROM news n 
                          LEFT JOIN categories c ON n.category_id = c.id 
                          LEFT JOIN admin_users a ON n.admin_user_id = a.id 
                          WHERE n.is_published = 1 
                          ORDER BY n.created_at DESC 
                          LIMIT 12');
    $stmt->execute();
    $news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get image files from uploads directory
    $uploads_dir = __DIR__ . '/../uploads';
    $images = [];
    
    if (is_dir($uploads_dir)) {
        $files = scandir($uploads_dir);
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $valid_extensions)) {
                    $images[] = 'uploads/' . $file;
                }
            }
        }
    }
    
    // Sort images for consistent assignment
    sort($images);
    
} catch (PDOException $e) {
    $error = 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage());
    $news_items = [];
    $images = [];
}

// Image descriptions mapping
$image_descriptions = [
    'teknoloji' => 'Yapay zeka ve teknoloji yenilikleri hayatımızı hızla değiştiriyor. Modern çözümler işletmelerde verimliliği artırıyor.',
    'spor' => 'Spor dünyası sürekli heyecan ve coşkuyla dolu. Atletlerin performansları bizi her zaman etkileyip ilham veriyor.',
    'genel' => 'Ülke ve dünya gündemine dair en son gelişmeleri takip edin. Önemli kararlar ve politikalar hayatımızı şekillendiriyor.',
    'sağlık' => 'Tıbbi inovasyonlar insanların hayat kalitesini iyileştirmek için çalışıyor. Sağlığa yapılan yatırımlar gelecek nesli kurtarıyor.',
    'sosyal' => 'Dijital platformlar toplumun iletişim şeklini kökten değiştirdi. Sosyal medya artık haberlerin ana kaynağı haline geldi.',
];

?>

<div class="container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #111827; font-size: 32px; margin-bottom: 10px;">Güncel Haberler</h1>
        <p style="color: #6b7280; font-size: 16px;">Dünya ve Türkiye'den en son haberleri takip edin</p>
    </div>
    
    <?php if (!empty($news_items)): ?>
        <div class="grid">
            <?php foreach ($news_items as $index => $news): ?>
                <div class="card">
                    <?php 
                    // Assign image from uploads if available
                    $image_url = '';
                    if (isset($images[$index])) {
                        $image_url = $images[$index];
                    } elseif ($news['image_url']) {
                        $image_url = $news['image_url'];
                    }
                    ?>
                    
                    <?php if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($news['title']) ?>" style="object-fit: cover;">
                    <?php else: ?>
                        <div style="width:100%; height:180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-size: 24px;">
                            📰
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div style="margin-bottom: 8px;">
                            <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($news['category_name'] ?? 'Genel') ?>
                            </span>
                        </div>
                        
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        
                        <!-- Image-related description -->
                        <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin: 10px 0;">
                            <?php 
                            $category_lower = strtolower($news['category_name'] ?? 'spor');
                            $desc = '';
                            
                            if (strpos($category_lower, 'teknoloji') !== false) {
                                $desc = $image_descriptions['teknoloji'];
                            } elseif (strpos($category_lower, 'spor') !== false) {
                                $desc = $image_descriptions['spor'];
                            } elseif (strpos($category_lower, 'genel') !== false) {
                                $desc = $image_descriptions['genel'];
                            } elseif (strpos($category_lower, 'sağlık') !== false) {
                                $desc = $image_descriptions['sağlık'];
                            } else {
                                $desc = $image_descriptions['sosyal'];
                            }
                            
                            echo $desc;
                            ?>
                        </p>
                        
                        <div class="meta">
                            📅 <?= date('d.m.Y', strtotime($news['created_at'])) ?>
                            <?php if ($news['author_name']): ?>
                                | ✍️ <?= htmlspecialchars($news['author_name']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <p style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">📰 Henüz haber yok</p>
            <p style="color: #9ca3af;">Kütüphanede haber bulunmuyor. Lütfen daha sonra kontrol edin.</p>
        </div>
    <?php endif; ?>
</div>
