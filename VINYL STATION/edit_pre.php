<?php
// edit_pre.php
session_start();
require __DIR__ . '/csrf.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// ตรวจสอบความปลอดภัย CSRF
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
$old_img     = $_POST['old_img'] ?? ''; 

$errors = [];

// ปรับแก้ข้อความแจ้งเตือน Error ให้เข้ากับรายละเอียดแผ่นเสียง
if (!$id)                 $errors[] = 'รหัสสินค้าไม่ถูกต้อง';
if ($productname === '')  $errors[] = 'กรุณาระบุชื่อศิลปินหรือชื่ออัลบั้ม';
if ($detail === '')       $errors[] = 'กรุณาระบุรายละเอียดแผ่นเสียง'; // เปลี่ยนจาก Detail required
if ($price === '' || !is_numeric($price) || $price <= 0)
    $errors[] = 'กรุณาระบุราคาที่ถูกต้อง';

$preview_img = null;
$source = 'none';
$keep_old_image = false;

// 1. ตรวจสอบการอัปโหลดไฟล์ใหม่
if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'ไฟล์รูปภาพไม่ถูกต้อง';

    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG และ GIF';

    if (!$errors) {
        $tmpDir = __DIR__.'/temp';
        
        // ป้องกัน Warning เรื่องสิทธิ์การเข้าถึง
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0777, true); 
        }
        @chmod($tmpDir, 0777); 

        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name");

        $preview_img = "temp/$name";
        $source = 'upload';
    }
}
// 2. ตรวจสอบการใช้ Image URL ใหม่
elseif (filter_var($img_url, FILTER_VALIDATE_URL)) {
    $preview_img = $img_url;
    $source = 'url';
}
// 3. ถ้าไม่มีการเปลี่ยนแปลง ให้ใช้รูปเดิม
else {
    $preview_img = $old_img; 
    $keep_old_image = true;
    $source = 'old';
}

// แสดงข้อความ Error
if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url');
    die('<h3>พบข้อผิดพลาด</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="edit.php?id='.$id.'">ย้อนกลับไปแก้ไข</a>');
}

/* บันทึกลง Session รวมถึงค่ารูปเดิมด้วย */
$_SESSION['preview'] = compact(
    'id', 'productname','detail','price','preview_img','source','keep_old_image','old_img'
);
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Preview Edit - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0a0a0c; color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: #16161a; padding: 40px; border-radius: 25px; border: 1px solid #222; width: 100%; max-width: 450px; text-align: center; }
        h2 { color: #bc13fe; margin-bottom: 20px; }
        .preview-info { text-align: left; background: #0a0a0c; padding: 20px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #333; }
        img { max-width: 100%; border-radius: 15px; margin-top: 10px; border: 1px solid #bc13fe; }
        .btn-confirm { background: #bc13fe; color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-confirm:hover { box-shadow: 0 0 20px #bc13fe; }
        .back-link { display: block; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h2>PREVIEW YOUR VIBE 💿</h2>
    <div class="preview-info">
        <p><strong>NAME:</strong> <?= e($productname) ?></p>
        <p><strong>PRICE:</strong> <span style="color: #bc13fe;">฿<?= number_format((float)$price, 2) ?></span></p>
        <p><strong>DETAIL:</strong><br><small><?= nl2br(e($detail)) ?></small></p>
        <p><strong>COVER:</strong><br>
            <?php if ($preview_img): ?>
                <img src="<?= e($preview_img) ?>" alt="Preview">
            <?php else: ?>
                <span style="color:#666">No image selected</span>
            <?php endif; ?>
        </p>
    </div>

    <form method="POST" action="edit_save.php">
        <button type="submit" class="btn-confirm">CONFIRM & UPDATE</button>
        <a href="edit.php?id=<?= $id ?>" class="back-link">← กลับไปแก้ไขข้อมูล</a>
    </form>
</div>
</body>
</html>