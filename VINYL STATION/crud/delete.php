<?php
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: ../views/list.php"); exit; }

require __DIR__ . '/../core/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare("SELECT img FROM products WHERE id = ?");
$stmt->execute([$id]);
$img = $stmt->fetchColumn();

if ($img && !filter_var($img, FILTER_VALIDATE_URL)) {
    $imgPath = __DIR__ . '/../' . (strpos($img, 'storage/') === 0 ? $img : 'storage/'.$img);
    if (file_exists($imgPath)) { @unlink($imgPath); }
}

$stmt_del = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt_del->execute([$id]);

header("Location: ../views/list.php");
exit;