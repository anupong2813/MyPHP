<?php
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: ../views/list.php"); exit; }
require __DIR__ . '/../core/config.php';
if (empty($_SESSION['preview'])) { die('No preview data. Please start again.'); }
$p = $_SESSION['preview']; $imgValue = null;

if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/../'.$p['preview_img'];
    if (!file_exists($tempPath)) die('Uploaded image not found.');
    $uploadDir = __DIR__.'/../storage/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $file = basename($tempPath); rename($tempPath, "$uploadDir/$file"); $imgValue = "storage/uploads/$file";
} elseif ($p['source'] === 'url') { $imgValue = $p['preview_img'];
} elseif ($p['keep_old_image']) {
    $stmt = $pdo->prepare("SELECT img FROM products WHERE id = ?"); $stmt->execute([$p['id']]); $imgValue = $stmt->fetchColumn();
}
if (!$p['keep_old_image'] && !empty($p['old_img'])) {
    $oldPathStr = strpos($p['old_img'], 'storage/') === 0 ? $p['old_img'] : 'storage/'.$p['old_img'];
    $oldImgPath = __DIR__ . '/../' . $oldPathStr;
    if (file_exists($oldImgPath) && !filter_var($p['old_img'], FILTER_VALIDATE_URL)) { @unlink($oldImgPath); }
}

$sql = "UPDATE products SET productname = :n, detail = :d, price = :p, img = :i, color = :c, format = :f, released_date = :r, spotify_url = :s, category = :cat WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':n' => $p['productname'], ':d' => $p['detail'], ':p' => $p['price'], ':i' => $imgValue, ':c' => $p['color'], ':f' => $p['format'], ':r' => $p['released_date'], ':s' => $p['spotify_url'], ':cat' => $p['category'], ':id' => $p['id']]);
unset($_SESSION['preview']);
header("Location: ../views/list.php");
exit;