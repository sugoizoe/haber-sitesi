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

$stmt = $pdo->prepare(
    "INSERT INTO haberler (baslik, ozet, icerik) VALUES (?, ?, ?)"
);
$stmt->execute([$baslik, $ozet, $icerik]);

header('Location: list.php');
exit;
