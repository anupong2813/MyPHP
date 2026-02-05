<?php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['preview'])) {
    die('No preview data. Please start again.');
}

$p = $_SESSION['preview'];

$imgValue = null;

/* HANDLE IMAGE */
if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/'.$p['preview_img'];
    if (!file_exists($tempPath)) {
        die('Uploaded image not found. Please upload again.');
    }

    $uploadDir = __DIR__.'/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

    $file = basename($tempPath);
    rename($tempPath,"$uploadDir/$file");
    $imgValue = "uploads/$file";
}
elseif ($p['source'] === 'url') {
    $imgValue = $p['preview_img'];
}

/* SAVE DB */
$sql = "INSERT INTO Nindam_Products (productname,detail,price,img)
        VALUES (:n,:d,:p,:i)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':n'=>$p['productname'],
    ':d'=>$p['detail'],
    ':p'=>$p['price'],
    ':i'=>$imgValue
]);

unset($_SESSION['preview']);
?>
<h2>Product Saved Successfully</h2>
<a href="create.php">Add New Product</a>
