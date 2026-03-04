<?php
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: ../views/list.php"); exit; }
require __DIR__ . '/../core/config.php';
if (empty($_SESSION['preview'])) { die('ไม่พบข้อมูลสำหรับบันทึก'); }
$p = $_SESSION['preview']; $imgValue = null;
if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/../'.$p['preview_img'];
    if (file_exists($tempPath)) {
        $uploadDir = __DIR__.'/../storage/uploads';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $file = basename($tempPath); rename($tempPath, "$uploadDir/$file"); $imgValue = "storage/uploads/$file";
    }
} elseif ($p['source'] === 'url') { $imgValue = $p['preview_img']; }

$sql = "INSERT INTO products (productname, detail, price, img, color, format, released_date, spotify_url, category) VALUES (:n, :d, :p, :i, :c, :f, :r, :s, :cat)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':n' => $p['productname'], ':d' => $p['detail'], ':p' => $p['price'], ':i' => $imgValue, ':c' => $p['color'], ':f' => $p['format'], ':r' => $p['released_date'], ':s' => $p['spotify_url'], ':cat' => $p['category']]);
unset($_SESSION['preview']);
header("Location: ../views/list.php");
exit;