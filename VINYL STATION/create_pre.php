<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
require __DIR__ . '/csrf.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('คำขอไม่ถูกต้อง (Invalid request)');
}

$productname = trim($_POST['productname'] ?? '');
$color       = trim($_POST['color'] ?? '');
$format      = trim($_POST['format'] ?? '');
$released_date = trim($_POST['released_date'] ?? '');
$price       = trim($_POST['price'] ?? '');
$detail      = trim($_POST['detail'] ?? '');
$img_url     = trim($_POST['img_url'] ?? '');

$errors = [];
if ($productname === '') $errors[] = 'กรุณาระบุชื่อศิลปินหรือชื่ออัลบั้ม';
if ($detail === '')      $errors[] = 'กรุณาระบุรายละเอียดแผ่นเสียง';
if ($price === '' || !is_numeric($price) || $price <= 0) $errors[] = 'กรุณาระบุราคาที่ถูกต้อง';

$preview_img = null;
$source = 'none';

if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'ไฟล์รูปภาพไม่ถูกต้อง';
    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG และ GIF';

    if (!$errors) {
        $tmpDir = __DIR__.'/temp';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0777, true); }
        @chmod($tmpDir, 0777); 
        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        if (move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name")) {
            $preview_img = "temp/$name";
            $source = 'upload';
        }
    }
}

if (!$preview_img && filter_var($img_url, FILTER_VALIDATE_URL)) {
    $preview_img = $img_url; $source = 'url';
}

if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url','color','format','released_date');
    die('<h3>พบข้อผิดพลาด</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="index.php">ย้อนกลับไปแก้ไข</a>');
}

$_SESSION['preview'] = compact('productname','detail','price','preview_img','source','color','format','released_date');
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Preview: ตรวจสอบข้อมูลแผ่นเสียง</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff00e6; --bg: #111111; --card: #2a2a2a; --text-muted: #a0a0a0; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding:40px 0;}
        
        .container { background: var(--card); padding: 40px; border-radius: 12px; width: 100%; max-width: 750px; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        h2 { text-align: center; color: var(--primary); margin-top: 0; margin-bottom: 30px; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 24px; letter-spacing: 1px; }
        
        /* เลย์เอาต์ 2 คอลัมน์ (ซ้ายรูป ขวาข้อมูล) */
        .preview-layout { display: flex; gap: 40px; align-items: center; margin-bottom: 30px; background: #1f1f1f; padding: 25px; border-radius: 8px; border: 1px solid #333; }
        
        .preview-img { flex: 1; text-align: center; }
        .preview-img img { width: 100%; max-width: 300px; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .no-cover { width: 100%; max-width: 300px; aspect-ratio: 1/1; background: #111; display: flex; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif; font-weight: 700; color: #555; border-radius: 6px; margin: 0 auto; }

        .preview-info { flex: 1.2; text-align: left; }
        .preview-info p { margin: 0 0 12px 0; font-size: 14px; line-height: 1.4; color: #ccc;}
        .preview-info strong { color: var(--text-muted); font-family: 'Montserrat', sans-serif; font-size: 11px; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .val-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 20px; color: #fff; display:block; margin-bottom:12px;}
        .val-price { color: var(--primary); font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 28px; display: block; margin-top: 5px; }
        
        .btn-confirm { background: var(--primary); color: #111; border: none; width: 100%; padding: 16px; border-radius: 6px; font-weight: 900; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; text-transform: uppercase; font-size: 15px; letter-spacing: 1px; }
        .btn-confirm:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,0,230,0.3);}
        .back-link { display: block; text-align: center; margin-top: 20px; color: #888; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .back-link:hover { color: #fff; }

        @media (max-width: 600px) {
            .preview-layout { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>PREVIEW YOUR VIBE 💿</h2>
    
    <div class="preview-layout">
        <div class="preview-img">
            <?php if ($preview_img): ?>
                <img src="<?= e($preview_img) ?>" alt="Preview">
            <?php else: ?>
                <div class="no-cover">NO COVER</div>
            <?php endif; ?>
        </div>

        <div class="preview-info">
            <span class="val-name"><?= e($productname) ?></span>
            <p><strong>Color & Format</strong> <?= e($color) ?: '-' ?> • <?= e($format) ?: '-' ?></p>
            <p><strong>First Released</strong> <?= e($released_date) ?: '-' ?></p>
            <p><strong>Detail</strong> <?= nl2br(e($detail)) ?></p>
            <strong style="margin-top:20px;">PRICE</strong>
            <span class="val-price">฿<?= number_format((float)$price, 2) ?></span>
        </div>
    </div>

    <form method="POST" action="create_save.php">
        <button type="submit" class="btn-confirm">CONFIRM & SAVE DATA</button>
        <a href="index.php" class="back-link">← กลับไปแก้ไขอีกครั้ง</a>
    </form>
</div>
</body>
</html>