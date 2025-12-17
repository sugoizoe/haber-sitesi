# Bartın 24 - Yerel Haber Sitesi

## 📰 Proje Hakkında

Bartın 24, Bartın ve Amasra bölgesinden güncel haberleri takip etmenizi sağlayan modern bir haber sitesidir. PHP, MySQL ve responsive tasarım kullanılarak geliştirilmiştir.

## ✨ Özellikler

### Genel Özellikler
- 📱 Responsive (Mobil uyumlu) tasarım
- 🎨 Modern ve kullanıcı dostu arayüz
- 🔐 Güvenli kullanıcı kimlik doğrulama sistemi
- 📊 Kategori bazlı haber filtreleme
- 🖼️ Resim yükleme ve görüntüleme desteği

### Kullanıcı Rolleri
1. **Admin (Yönetici)**
   - Tüm kullanıcı yönetimi
   - Editör ve admin hesapları oluşturma/silme
   - Tüm içerik yönetimi
   
2. **Editör**
   - Haber ekleme, düzenleme, silme
   - Resim yükleme
   - İçerik kategorilendirme

3. **User (Üye)**
   - Haber okuma
   - Yorum yapma (gelecek özellik)

### Teknik Özellikler
- **Backend**: PHP 7.4+
- **Veritabanı**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Güvenlik**: 
  - Password hashing (PASSWORD_DEFAULT)
  - PDO Prepared Statements (SQL Injection koruması)
  - Session yönetimi
  - CSRF koruması

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya MariaDB 10.2+
- Apache/Nginx web sunucusu
- WAMP/XAMPP/LAMP (önerilir)

### Kurulum Adımları

#### 1. Projeyi İndirin
```bash
git clone https://github.com/sugoizoe/haber-sitesi.git
cd haber-sitesi
```

#### 2. Veritabanı Ayarları
`config.php` dosyasını oluşturun:
```php
<?php
$db_host = 'localhost';
$db_name = 'habersitesi';
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';
?>
```

#### 3. Veritabanını Oluşturun
Tarayıcınızda şu adrese gidin:
```
http://localhost/habersitesi/init-db.php
```
Bu işlem:
- `habersitesi` veritabanını oluşturur
- Gerekli tabloları oluşturur
- Demo haberler ve kullanıcılar ekler

#### 4. Hazır!
Ana sayfaya gidin:
```
http://localhost/habersitesi/
```

## 🔑 Varsayılan Kullanıcılar

Kurulum sonrası aşağıdaki hesaplarla giriş yapabilirsiniz:

| Rol | Kullanıcı Adı | Şifre |
|-----|---------------|-------|
| Admin | admin | admin123 |
| Editör | editor | editor123 |
| Üye | user | user123 |

## 📁 Proje Yapısı

```
habersitesi/
├── index.php              # Ana router dosyası
├── config.php            # Veritabanı ayarları (gitignore'da)
├── init-db.php           # Veritabanı kurulum scripti
├── .gitignore            # Git ignore dosyası
├── README.md             # Bu dosya
├── pages/                # Sayfa modülleri
│   ├── home.php         # Ana sayfa
│   ├── categories.php   # Kategori listesi
│   ├── latest.php       # Son haberler
│   ├── admin.php        # Admin paneli
│   ├── editor.php       # Editör paneli
│   └── logout.php       # Çıkış işlemi
└── uploads/             # Yüklenen resimler
    ├── amasra-1.jpg
    ├── amasra ağlayan ağaç.jpg
    ├── amasra mendirek.jpg
    ├── bartın çarşı.jpg
    ├── çekiciler sokağı.jpg
    └── inkumu.jpg
```

## 🗄️ Veritabanı Şeması

### Tablolar

#### `admin_users`
Yönetici ve editör hesapları
- id, name, username, email, password, role, is_active, created_at, updated_at

#### `users`
Site üyeleri
- id, username, email, password, is_active, created_at

#### `categories`
Haber kategorileri (Genel, Kültür, Yerel)
- id, name, slug, description, created_at

#### `news`
Haber içerikleri
- id, title, content, excerpt, image_url, admin_user_id, category_id, is_published, views, created_at, updated_at

#### `comments`
Yorumlar (gelecek özellik)
- id, news_id, user_id, author_name, author_email, content, status, created_at

## 🎨 Kullanılan Teknolojiler

- **PHP**: Server-side scripting
- **MySQL**: İlişkisel veritabanı
- **HTML5**: Yapısal işaretleme
- **CSS3**: Stil ve tasarım (Flexbox, Grid, Animations)
- **JavaScript**: Modal işlemleri ve form yönetimi
- **PDO**: Veritabanı bağlantısı
- **Git**: Versiyon kontrol sistemi

## 🔒 Güvenlik Özellikleri

1. **Password Hashing**: `password_hash()` ile güvenli şifreleme
2. **Prepared Statements**: SQL injection koruması
3. **Session Management**: Güvenli oturum yönetimi
4. **Input Sanitization**: `htmlspecialchars()` ile XSS koruması
5. **Role-Based Access Control**: Rol bazlı erişim kontrolü

## 📱 Responsive Tasarım

Site tüm cihazlarda (desktop, tablet, mobil) düzgün çalışır:
- Flexbox layout sistemi
- CSS Grid için haber kartları
- Media queries ile mobil optimizasyon
- Touch-friendly butonlar ve linkler

## 🎯 Gelecek Özellikler

- [ ] Yorum sistemi (admin onayı ile)
- [ ] Haber arama fonksiyonu
- [ ] Haber detay sayfası
- [ ] Sosyal medya paylaşım butonları
- [ ] Okunma sayısı takibi
- [ ] Email bildirimleri
- [ ] RSS feed
- [ ] Çoklu dil desteği

## 📝 Notlar

- `config.php` dosyası `.gitignore` içinde olduğu için manuel oluşturulmalıdır
- `uploads/` klasörü resimleri içerir ve Git'e dahildir
- Demo veriler `init-db.php` ile otomatik yüklenir
- Session cookie güvenliği için HTTPS önerilir (production)

## 👨‍💻 Geliştirici

**Proje**: Bartın 24 Haber Sitesi  
**GitHub**: [sugoizoe/haber-sitesi](https://github.com/sugoizoe/haber-sitesi)  
**Yıl**: 2025

## 📄 Lisans

Bu proje eğitim amaçlı geliştirilmiştir.

---

**Not**: Proje WAMP/XAMPP ortamında test edilmiştir. Production ortamında kullanmadan önce güvenlik ayarlarını gözden geçirin.