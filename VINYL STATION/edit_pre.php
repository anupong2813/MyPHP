<?php
// edit_pre.php
session_start();
require __DIR__ . '/csrf.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    die('Invalid request');
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$productname = trim($_POST['productname'] ?? '');
$detail      = trim($_POST['detail'] ?? '');
$price       = trim($_POST['price'] ?? '');
$img_url     = trim($_POST['img_url'] ?? '');

$errors = [];

if (!$id)                 $errors[] = 'Invalid product ID';
if ($productname === '')  $errors[] = 'Product name required';
if ($detail === '')       $errors[] = 'Detail required';
if ($price === '' || !is_numeric($price) || $price <= 0)
    $errors[] = 'Invalid price';

$preview_img = null;
$source = 'none';
$keep_old_image = false;

// IMAGE UPLOAD
if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'Invalid image file';

    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'Image type not allowed';

    if (!$errors) {
        $tmpDir = __DIR__.'/temp';
        // สร้างโฟลเดอร์ temp และตั้งสิทธิ์ 0777
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name");

        $preview_img = "temp/$name";
        $source = 'upload';
    }
}
// IMAGE URL
elseif (filter_var($img_url, FILTER_VALIDATE_URL)) {
    $preview_img = $img_url;
    $source = 'url';
}
// Keep old image
else {
    $keep_old_image = true;
}

if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url');
    die('<h3>Validation Errors</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="edit.php?id='.$id.'">Back</a>');
}

/* SAVE PREVIEW TO SESSION */
$_SESSION['preview'] = compact(
    'id', 'productname','detail','price','preview_img','source','keep_old_image'
);
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Preview Edit - Vinyl Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-card" style="max-width: 500px;">
    <h2>👁️ ตรวจสอบการแก้ไข (Preview)</h2>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; margin-bottom: 20px;">
        <p style="margin-bottom: 10px;"><strong>ชื่อแผ่นเสียง:</strong> <?= e($productname) ?></p>
        <p style="margin-bottom: 10px;"><strong>รายละเอียด:</strong><br>
           <span style="font-size: 14px; color: #636e72;"><?= nl2br(e($detail)) ?></span></p>
        <p style="margin-bottom: 10px;"><strong>ราคา:</strong> <span style="color: #2d6a4f; font-weight: bold;"><?= number_format((float)$price, 2) ?> ฿</span></p>

        <div style="text-align: center; margin-top: 15px;">
            <?php if ($preview_img): ?>
                <p><small>ปกแผ่นเสียงใหม่ที่จะใช้:</small></p>
                <img src="<?= e($preview_img) ?>" class="current-img" style="max-width: 150px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);" alt="New preview image">
            <?php elseif ($keep_old_image): ?>
                <p style="color: #b2bec3; font-size: 13px;"><em>(ใช้รูปปกเดิม ไม่มีการเปลี่ยนแปลงรูปภาพ)</em></p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" action="edit_save.php">
        <button type="submit" class="btn-primary">✅ ยืนยันและบันทึกข้อมูล</button>
    </form>
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="edit.php?id=<?= $id ?>" class="btn-outline">กลับไปแก้ไขอีกครั้ง</a>
    </div>
</div>
</body>
</html>