<?php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['preview'])) { die('ไม่พบข้อมูลสำหรับบันทึก'); }
$p = $_SESSION['preview'];
$imgValue = null;

if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/'.$p['preview_img'];
    if (file_exists($tempPath)) {
        $uploadDir = __DIR__.'/uploads';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $file = basename($tempPath);
        rename($tempPath, "$uploadDir/$file");
        $imgValue = "uploads/$file";
    }
} elseif ($p['source'] === 'url') { $imgValue = $p['preview_img']; }

// บันทึกลงตาราง products ตามที่มีใน phpMyAdmin ของคุณ
$sql = "INSERT INTO products (productname, detail, price, img) VALUES (:n, :d, :p, :i)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':n' => $p['productname'],
    ':d' => $p['detail'],
    ':p' => $p['price'],
    ':i' => $imgValue
]);

unset($_SESSION['preview']);
header("Location: list.php"); // บันทึกเสร็จแล้วไปหน้าแสดงรายการทันที
exit;