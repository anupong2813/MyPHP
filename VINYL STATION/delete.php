<?php
require __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// ลบไฟล์รูปจริงออกจาก uploads
$stmt = $pdo->prepare("SELECT img FROM products WHERE id = ?");
$stmt->execute([$id]);
$img = $stmt->fetchColumn();
if ($img && file_exists($img)) { unlink($img); }

// ลบข้อมูลในฐานข้อมูล
$stmt_del = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt_del->execute([$id]);

header("Location: list.php"); // พอลบเสร็จจะเห็น Grid ใหม่ทันที
exit;