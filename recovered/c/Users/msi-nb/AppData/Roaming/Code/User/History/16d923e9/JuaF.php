<?php
require_once __DIR__ . '/db.php';

// Öne çıkan haber
$stmt = $pdo->query("SELECT * FROM haberler ORDER BY tarih DESC LIMIT 1");
$hero = $stmt->fetch();

// Liste
$stmt2 = $pdo->query("SELECT id, baslik, ozet, tarih FROM haberler ORDER BY tarih DESC LIMIT 10");
$liste = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Gündem 24 - Basit haber demo sitesi" />
    <title>Test123 – Haber</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <header class="topbar">
      <div class="wrap">
        <a class="logo" href="#">
          <span class="logo-badge">G</span>
          <span>Gündem 24</span>
        </a>

        <nav aria-label="Ana Menü">
          <a class="navlink" href="#">Gündem</a>
          <a class="navlink" href="#">Ekonomi</a>
          <a class="navlink" href="#">Spor</a>
          <a class="navlink" href="#">Teknoloji</a>
          <a class="navlink" href="#">Magazin</a>
        </nav>

        <div class="search" role="search">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 21l-3.8-3.8M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <input placeholder="Ara (HTML demo)" aria-label="Haber ara" />
        </div>
      </div>
    </header>

    <main class="container">
      <section class="hero">
        <article class="lead">
          <img class="cover" src="https://picsum.photos/1200/640?random=3" alt="Manşet görseli" />
          <div class="meta">
            <span class="chip">SON DAKİKA</span>
            <h1>
              Merkez Bankası'ndan yılın üçüncü enflasyon raporu: Beklentiler yukarı yönlü
              güncellendi
            </h1>
            <p>Ekonomi • <time datetime="PT12M">12 dakika önce</time></p>
          </div>
        </article>

        <aside class="herolist" aria-label="Öne çıkan haberler">
          <a class="mini" href="#">
            <img src="https://picsum.photos/300/200?random=11" alt="Spor haberi" />
            <div>
              <h3>Şampiyonlar Ligi'nde çeyrek final eşleşmeleri belli oldu</h3>
              <div class="meta-row"><span>Spor</span><span>•</span><span>1s</span></div>
            </div>
          </a>

          <a class="mini" href="#">
            <img src="https://picsum.photos/300/200?random=12" alt="Teknoloji haberi" />
            <div>
              <h3>Yapay zekâdan sağlıkta dev atılım: Tanı süresi %40 kısalıyor</h3>
              <div class="meta-row"><span>Teknoloji</span><span>•</span><span>3s</span></div>
            </div>
          </a>

          <a class="mini" href="#">
            <img src="https://picsum.photos/300/200?random=13" alt="Ekonomi haberi" />
            <div>
              <h3>Borsa günü yatay kapattı, gözler FED açıklamasında</h3>
              <div class="meta-row"><span>Ekonomi</span><span>•</span><span>2s</span></div>
            </div>
          </a>
        </aside>
      </section>

      <section class="content">
        <div class="main-col">
          <div class="cards">
            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=21" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Gündem</span>
                <h4>Meclis'te yeni yasa teklifi: Öğrenci burslarında düzenleme</h4>
                <p>
                  Komisyondan geçen teklifle birlikte burs ve kredi üst limitlerinde artış
                  planlanıyor…
                </p>
              </div>
            </a>

            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=22" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Spor</span>
                <h4>Milli takım aday kadrosu açıklandı: 3 sürpriz isim</h4>
                <p>Teknik direktörün geniş kadroya davet ettiği genç oyuncular sosyal medyada ses getirdi…</p>
              </div>
            </a>

            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=23" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Ekonomi</span>
                <h4>Altında sert dalgalanma: Uzmanlardan uyarı</h4>
                <p>Küresel piyasalardaki belirsizlik altın fiyatlarında dalgalanmayı artırdı…</p>
              </div>
            </a>

            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=24" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Teknoloji</span>
                <h4>Yeni nesil bataryalarla 8 dakikada tam şarj</h4>
                <p>Prototip testlerinde yüksek güvenlik ve düşük ısı profiliyle dikkat çekiyor…</p>
              </div>
            </a>

            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=25" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Magazin</span>
                <h4>Sezonun en çok izlenen dizisi final yapıyor</h4>
                <p>Yapım ekibi son bölüm için özel bir gala planladığını duyurdu…</p>
              </div>
            </a>

            <a href="#" class="card">
              <img src="https://picsum.photos/640/400?random=26" alt="Haber görseli" />
              <div class="pad">
                <span class="chip">Kültür</span>
                <h4>Bienal kapılarını açtı: 42 ülkeden 300 sanatçı</h4>
                <p>Bu yılın teması sürdürülebilirlik ve dijital dönüşüm üzerine kuruldu…</p>
              </div>
            </a>
          </div>

          <nav class="pagination" aria-label="Sayfalama">
            <span class="page">Önceki</span>
            <span class="page active">1</span>
            <span class="page">2</span>
            <span class="page">3</span>
            <span class="page">Sonraki</span>
          </nav>
        </div>

        <aside class="sidebar">
          <div class="block">
            <h5>Kategoriler</h5>
            <div class="tags">
              <a class="tag" href="#">Gündem</a>
              <a class="tag" href="#">Ekonomi</a>
              <a class="tag" href="#">Spor</a>
              <a class="tag" href="#">Teknoloji</a>
              <a class="tag" href="#">Dünya</a>
              <a class="tag" href="#">Kültür</a>
            </div>
          </div>

          <div class="block">
            <h5>En Çok Okunan</h5>
            <div class="list">
              <a href="#"><span class="dot" aria-hidden="true"></span><span>Deprem sonrası ilk 24 saat: Neler yaşandı?</span></a>
              <a href="#"><span class="dot" aria-hidden="true"></span><span>Kripto piyasasında sert düşüş</span></a>
              <a href="#"><span class="dot" aria-hidden="true"></span><span>Yeni müfredat tartışmaları</span></a>
            </div>
          </div>

          <div class="block">
            <h5>Bülten</h5>
            <p class="muted">E-mail ile günlük özet almak için kaydolun. (HTML demo)</p>
          </div>
        </aside>
      </section>
    </main>

    <footer>
      <div class="wrap">
        <p>© Gündem 24 — Demo site. Tüm hakları saklıdır.</p>
      </div>
    </footer>
  </body>
</html>