<?php
session_start();
require __DIR__ . '/core/config.php'; // ตรวจสอบ path config ให้ตรงกับโปรเจกต์คุณ

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$action = $_GET['action'] ?? '';

if ($id) {
    if ($action === 'increase') {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + 1 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'decrease') {
        // ใช้ GREATEST เพื่อป้องกันไม่ให้สต๊อกติดลบ (ต่ำสุดคือ 0)
        $stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - 1) WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// อัปเดตเสร็จให้เด้งกลับไปหน้าเดิม
header("Location: views/list.php");
exit;