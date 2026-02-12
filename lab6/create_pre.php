<?php
// create_pre.php
session_start();
require __DIR__ . '/csrf.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// ตรวจสอบความปลอดภัย CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    die('คำขอไม่ถูกต้อง (Invalid request)');
}

// รับค่าจากฟอร์ม
$productname = trim($_POST['productname'] ?? '');
$detail      = trim($_POST['detail'] ?? '');
$price       = trim($_POST['price'] ?? '');
$img_url     = trim($_POST['img_url'] ?? '');

$errors = [];

// ตรวจสอบข้อมูลเบื้องต้น
if ($productname === '') $errors[] = 'กรุณาระบุชื่อศิลปินหรือชื่ออัลบั้ม';
// แก้ไขข้อความแจ้งเตือน Error ให้เป็น "รายละเอียดแผ่นเสียง"
if ($detail === '')      $errors[] = 'กรุณาระบุรายละเอียดแผ่นเสียง';
if ($price === '' || !is_numeric($price) || $price <= 0)
    $errors[] = 'กรุณาระบุราคาที่ถูกต้อง';

$preview_img = null;
$source = 'none';

/* การจัดการรูปภาพอัปโหลด */
if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'ไฟล์รูปภาพไม่ถูกต้อง';

    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG และ GIF';

    if (!$errors) {
        $tmpDir = __DIR__.'/temp';
        
        // ตรวจสอบและสร้างโฟลเดอร์ temp พร้อมบังคับสิทธิ์ 0777
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0777, true);
        }
        @chmod($tmpDir, 0777); 

        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        
        if (move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name")) {
            $preview_img = "temp/$name";
            $source = 'upload';
        } else {
            $errors[] = 'ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์พักได้ (ตรวจสอบ Permission ของโฟลเดอร์ temp)';
        }
    }
}

/* กรณีใส่เป็น URL รูปภาพ */
if (!$preview_img && filter_var($img_url, FILTER_VALIDATE_URL)) {
    $preview_img = $img_url;
    $source = 'url';
}

// หากมีข้อผิดพลาด ส่งกลับไปหน้าฟอร์ม
if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url');
    die('<h3>พบข้อผิดพลาด</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="index.php">ย้อนกลับไปแก้ไข</a>');
}

/* เก็บค่าลง Session เพื่อส่งไปหน้าบันทึก */
$_SESSION['preview'] = compact(
    'productname','detail','price','preview_img','source'
);
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Preview: ตรวจสอบข้อมูลแผ่นเสียง</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0a0a0c; color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: #16161a; padding: 40px; border-radius: 25px; border: 1px solid #222; width: 100%; max-width: 450px; text-align: center; }
        h2 { color: #bc13fe; margin-bottom: 20px; }
        .preview-info { text-align: left; background: #0a0a0c; padding: 20px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #333; }
        img { max-width: 100%; border-radius: 15px; margin-top: 10px; border: 1px solid #bc13fe; }
        .btn-confirm { background: #bc13fe; color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-confirm:hover { box-shadow: 0 0 20px #bc13fe; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>PREVIEW YOUR VIBE 💿</h2>
    <div class="preview-info">
        <p><strong>NAME:</strong> <?= e($productname) ?></p>
        <p><strong>PRICE:</strong> <span style="color: #bc13fe;">฿<?= number_format((float)$price, 2) ?></span></p>
        <p><strong>DETAIL:</strong><br><small><?= nl2br(e($detail)) ?></small></p>

        <?php if ($preview_img): ?>
            <p><strong>COVER:</strong><br>
            <img src="<?= e($preview_img) ?>" alt="Preview"></p>
        <?php endif; ?>
    </div>

    <form method="POST" action="create_save.php">
        <button type="submit" class="btn-confirm">✅ ยืนยันและบันทึกข้อมูล</button>
        <a href="index.php" class="back-link">← กลับไปแก้ไขอีกครั้ง</a>
    </form>
</div>

</body>
</html>