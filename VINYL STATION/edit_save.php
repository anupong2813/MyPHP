<?php
// edit_save.php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['preview'])) {
    die('No preview data. Please start again.');
}

$p = $_SESSION['preview'];

$imgValue = null;

/* HANDLE IMAGE - กระบวนการย้ายไฟล์ภาพ */
if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/'.$p['preview_img'];
    if (!file_exists($tempPath)) {
        die('Uploaded image not found.');
    }

    $uploadDir = __DIR__.'/uploads';
    // สร้างโฟลเดอร์ uploads และตั้งสิทธิ์ 0777 หากยังไม่มี
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $file = basename($tempPath);
    rename($tempPath, "$uploadDir/$file");
    $imgValue = "uploads/$file";
}
elseif ($p['source'] === 'url') {
    $imgValue = $p['preview_img'];
}
elseif ($p['keep_old_image']) {
    // ดึงค่ารูปภาพเดิมจากฐานข้อมูล Nindam_Products
    $stmt = $pdo->prepare("SELECT img FROM Nindam_Products WHERE id = ?");
    $stmt->execute([$p['id']]);
    $old = $stmt->fetchColumn();
    $imgValue = $old;
}

/* UPDATE DB - บันทึกข้อมูลใหม่ลงฐานข้อมูล */
$sql = "UPDATE Nindam_Products 
        SET productname = :n, detail = :d, price = :p, img = :i 
        WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':n'  => $p['productname'],
    ':d'  => $p['detail'],
    ':p'  => $p['price'],
    ':i'  => $imgValue,
    ':id' => $p['id']
]);

unset($_SESSION['preview']);
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Update Successful - Vinyl Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-card" style="max-width: 500px; text-align: center;">
    <div style="font-size: 50px; margin-bottom: 20px;">✅</div>
    <h2>แก้ไขข้อมูลแผ่นเสียงสำเร็จ!</h2>
    <p style="color: #636e72; margin-bottom: 30px;">ระบบได้บันทึกการเปลี่ยนแปลงของแผ่นเสียง <strong>"<?= htmlspecialchars($p['productname']) ?>"</strong> เรียบร้อยแล้ว</p>

    <div style="display: flex; gap: 10px; flex-direction: column;">
        <a href="list.php" class="btn-primary" style="text-decoration: none;">กลับไปยังหน้ารายการสินค้า</a>
        <a href="edit.php?id=<?= $p['id'] ?>" class="btn-outline">แก้ไขรายการนี้อีกครั้ง</a>
    </div>
</div>
</body>
</html>