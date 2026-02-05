<?php
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

// ปรับข้อความแจ้งเตือนให้เข้ากับแผ่นเสียง
if ($productname === '') $errors[] = 'กรุณาระบุชื่อศิลปินหรือชื่ออัลบั้ม';
if ($detail === '')      $errors[] = 'กรุณาระบุแนวเพลงหรือรายละเอียดแผ่นเสียง';
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
        if (!is_dir($tmpDir)) mkdir($tmpDir,0777,true);

        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name");

        $preview_img = "temp/$name";
        $source = 'upload';
    }
}

/* กรณีใส่เป็น URL รูปภาพ */
if (!$preview_img && filter_var($img_url, FILTER_VALIDATE_URL)) {
    $preview_img = $img_url;
    $source = 'url';
}

// ถ้ามีข้อผิดพลาด ส่งกลับไปหน้าฟอร์ม (แนะนำให้เปลี่ยนเป็น index.php ถ้าคุณเปลี่ยนชื่อไฟล์แล้ว)
if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url');
    die('<h3>พบข้อผิดพลาด</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="index.php">ย้อนกลับไปแก้ไข</a>');
}

/* เก็บค่าลง Session เพื่อส่งไปบันทึก */
$_SESSION['preview'] = compact(
    'productname','detail','price','preview_img','source'
);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ตรวจสอบข้อมูลแผ่นเสียง</title>
    <style>
        body { font-family: 'system-ui', sans-serif; background-color: #f8f9fa; padding: 40px; }
        .preview-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: auto; }
        h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .label { font-weight: bold; color: #666; }
        img { border-radius: 10px; margin: 15px 0; border: 1px solid #ddd; }
        .btn-group { margin-top: 25px; }
        .btn-save { background: #333; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .btn-back { color: #666; text-decoration: none; margin-left: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="preview-card">
    <h2>Preview: ตรวจสอบความถูกต้อง 💿</h2>
    
    <p><span class="label">ชื่ออัลบั้ม/ศิลปิน:</span><br> <?= e($productname) ?></p>
    <p><span class="label">แนวเพลง/รายละเอียด:</span><br> <?= nl2br(e($detail)) ?></p>
    <p><span class="label">ราคา:</span> <span style="color: #d32f2f; font-weight: bold;">฿<?= number_format((float)$price, 2) ?></span></p>

    <?php if ($preview_img): ?>
        <p><span class="label">รูปหน้าปก:</span></p>
        <img src="<?= e($preview_img) ?>" width="100%">
    <?php endif; ?>

    <div class="btn-group">
        <form method="POST" action="create_save.php" style="display: inline;">
            <button type="submit" class="btn-save">ยืนยันและบันทึกข้อมูล</button>
        </form>
        <a href="index.php" class="btn-back">ย้อนกลับไปแก้ไข</a>
    </div>
</div>

</body>
</html>