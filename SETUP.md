# 🗞️ Haber Sitesi - Kurulum Rehberi

## 1️⃣ Database Oluştur

Tarayıcıda şu URL'yi aç:
```
http://localhost/habersitesi/create-db.php
```

Başarı mesajı görersen devam et. Yoksa MySQL'de manuel oluştur:
```sql
CREATE DATABASE IF NOT EXISTS habersitesi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 2️⃣ Tabloları Oluştur

Tarayıcıda aç:
```
http://localhost/habersitesi/setup.php
```

Kurulum başarılı mesajı göreceksin.

---

## 3️⃣ Siteye Erişim

Anasayfa:
```
http://localhost/habersitesi/
```

---

## 👥 Test Hesapları

| Rol | Kullanıcı | Şifre |
|-----|-----------|-------|
| Admin | `admin` | `admin123` |
| Editor | `editor` | `editor123` |
| User | `user` | `user123` |

---

## 🛣️ Sayfa Rotaları

- `/index.php` → Anasayfa (haberler listesi)
- `/?page=login` → Giriş
- `/?page=register` → Kayıt ol
- `/?page=admin` → Admin Panel (sadece Admin)
- `/?page=editor` → İçerik Yönetimi (Admin + Editor)
- `/?page=logout` → Çıkış

---

## 📁 Dosya Yapısı

```
habersitesi/
├── index.php              ← Ana router
├── config.php             ← Database bilgileri (gitignore)
├── setup.php              ← Tablo oluşturma
├── create-db.php          ← Database oluşturma
├── style.css              ← Stiller
├── pages/
│   ├── home.php           ← Anasayfa
│   ├── login.php          ← Giriş
│   ├── register.php       ← Kayıt
│   ├── logout.php         ← Çıkış
│   ├── admin.php          ← Admin Panel
│   └── editor.php         ← İçerik Yönetimi
├── uploads/               ← Yüklenen görseller
└── .gitignore             ← config.php ve uploads/ dışlanmış
```

---

## 🚀 Başla!

1. `create-db.php` aç → Database oluştur
2. `setup.php` aç → Tabloları oluştur
3. Ana sayfaya git → Giriş yap
4. Test hesaplarını kullan

Hepsi hazır! 🎉
