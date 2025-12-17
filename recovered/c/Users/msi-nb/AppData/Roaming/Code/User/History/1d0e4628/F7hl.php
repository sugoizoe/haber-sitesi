<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add.php');
    exit;
}

$baslik = trim($_POST['baslik']);
$ozet   = trim($_POST['ozet']);
$icerik = trim($_POST['icerik']);
$resim_yolu = null;

if (!empty($_FILES['resim']['name']) && $_FILES['resim']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['resim']['tmp_name'];
    $ext  = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
    $izin = ['jpg','jpeg','png','webp'];

    if (in_array($ext, $izin, true)) {
        $dosya = 'uploads/' . uniqid('img_', true) . '.' . $ext;
        $hedef = __DIR__ . '/../' . $dosya;

        if (move_uploaded_file($tmp, $hedef)) {
            $resim_yolu = '/' . $dosya; // tarayıcıdan erişim
        }
    }
}

$stmt = $pdo->prepare(
  "INSERT INTO haberler (baslik, ozet, icerik, resim_yolu) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$baslik, $ozet, $icerik, $resim_yolu]);


header('Location: list.php');
exit;
