<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: ../views/list.php"); exit; }
require __DIR__ . '/../core/csrf.php';
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) { die('Invalid request'); }

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$productname = trim($_POST['productname'] ?? ''); $color = trim($_POST['color'] ?? ''); $format = trim($_POST['format'] ?? '');
$category = trim($_POST['category'] ?? 'Other');
$released_date = trim($_POST['released_date'] ?? ''); $price = trim($_POST['price'] ?? ''); $detail = trim($_POST['detail'] ?? '');
$spotify_url = trim($_POST['spotify_url'] ?? ''); $img_url = trim($_POST['img_url'] ?? ''); $old_img = $_POST['old_img'] ?? ''; 

$errors = [];
if (!$id)                 $errors[] = 'รหัสสินค้าไม่ถูกต้อง';
if ($productname === '')  $errors[] = 'กรุณาระบุชื่อศิลปินหรือชื่ออัลบั้ม';
if ($detail === '')       $errors[] = 'กรุณาระบุรายละเอียดแผ่นเสียง';
if ($price === '' || !is_numeric($price) || $price <= 0) $errors[] = 'กรุณาระบุราคาที่ถูกต้อง';

$preview_img = null; $source = 'none'; $keep_old_image = false;
if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'ไฟล์รูปภาพไม่ถูกต้อง';
    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'รองรับเฉพาะไฟล์ JPG, PNG และ GIF';
    if (!$errors) {
        $tmpDir = __DIR__.'/../storage/temp'; 
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0777, true); 
        @chmod($tmpDir, 0777); 
        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        
        if (@move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name")) {
            $preview_img = "storage/temp/$name"; $source = 'upload';
        } else {
            $errors[] = 'เซิร์ฟเวอร์ไม่สามารถบันทึกรูปได้ (Permission Denied) กรุณาตั้งค่า CHMOD โฟลเดอร์ storage ให้เป็น 777';
        }
    }
} elseif (filter_var($img_url, FILTER_VALIDATE_URL)) { $preview_img = $img_url; $source = 'url';
} else { $preview_img = $old_img; $keep_old_image = true; $source = 'old'; }

if ($errors) {
    $_SESSION['sticky'] = compact('productname','category','detail','price','img_url','color','format','released_date','spotify_url');
    die('<!doctype html><html lang="th"><head><meta charset="utf-8"><title>Error</title><link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet"><style>body{background:#161618;color:#f5f5f7;font-family:"Kanit",sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;} .box{background:#1c1c1e;padding:40px;border-radius:12px;border:1px solid #38383a;text-align:center;} ul{text-align:left;color:#ff3b30;margin-bottom:20px;} a{color:#ffffff;text-decoration:none;background:#ff3b30;padding:10px 20px;border-radius:20px;font-weight:bold;}</style></head><body><div class="box"><h3>พบข้อผิดพลาด</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="edit.php?id='.$id.'">ย้อนกลับไปแก้ไข</a></div></body></html>');
}
$_SESSION['preview'] = compact('id', 'productname','category','detail','price','preview_img','source','keep_old_image','old_img','color','format','released_date','spotify_url');

function getPreviewImg($img) {
    if (empty($img)) return '';
    if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return '../' . (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Preview Edit - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding:40px 0; transition: 0.3s;}
        .container { background: var(--card); padding: 40px; border-radius: 12px; width: 100%; max-width: 750px; box-shadow: 0 15px 50px var(--shadow); border: 1px solid var(--border); transition: 0.3s; position: relative;}
        h2 { text-align: center; color: var(--text-main); margin-top: 0; margin-bottom: 30px; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 24px; letter-spacing: 1px; }
        .preview-layout { display: flex; gap: 40px; align-items: center; margin-bottom: 30px; background: var(--input-bg); padding: 25px; border-radius: 8px; border: 1px solid var(--border); }
        .preview-img { flex: 1; text-align: center; background: #000; padding: 10px; border-radius: 8px; }
        .preview-img img { width: 100%; max-width: 300px; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; box-shadow: 0 10px 20px var(--shadow); }
        .no-cover { width: 100%; max-width: 300px; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif; font-weight: 700; color: #555; border-radius: 6px; margin: 0 auto; }
        .preview-info { flex: 1.2; text-align: left; }
        .preview-info p { margin: 0 0 12px 0; font-size: 14px; line-height: 1.4; color: var(--text-muted);}
        .preview-info strong { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-size: 11px; display: block; margin-bottom: 4px; text-transform: uppercase; }
        .val-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 20px; color: var(--text-main); display:block; margin-bottom:12px;}
        .val-price { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 28px; display: block; margin-top: 5px; }
        .btn-confirm { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; width: 100%; padding: 16px; border-radius: 25px; font-weight: 900; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; text-transform: uppercase; font-size: 15px; letter-spacing: 1px; }
        .btn-confirm:hover { background: #333333 !important;}
        .back-link { display: block; text-align: center; margin-top: 20px; color: var(--text-muted); text-decoration: none; font-size: 13px; transition: 0.3s; }
        .back-link:hover { color: var(--text-main); }
        .theme-btn-inline { position: absolute; top: 20px; right: 20px; background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
        .theme-btn-inline:hover { background: var(--text-main); color: var(--bg); }
        @media (max-width: 600px) { .preview-layout { flex-direction: column; } }
    </style>
</head>
<body>
<div class="container">
    <button id="theme-toggle" class="theme-btn-inline">🌙</button>
    <h2>PREVIEW YOUR VIBE 💿</h2>
    <div class="preview-layout">
        <div class="preview-img">
            <?php if ($preview_img): ?><img src="<?= e(getPreviewImg($preview_img)) ?>" alt="Preview"><?php else: ?><div class="no-cover">NO COVER</div><?php endif; ?>
        </div>
        <div class="preview-info">
            <span class="val-name"><?= e($productname) ?></span>
            <p><strong>Category</strong> <?= e($category) ?></p>
            <p><strong>Color & Format</strong> <?= e($color) ?: '-' ?> • <?= e($format) ?: '-' ?></p>
            <p><strong>First Released</strong> <?= e($released_date) ?: '-' ?></p>
            <p><strong>Spotify Link</strong> <?= e($spotify_url) ?: '-' ?></p>
            <p><strong>Detail</strong> <?= nl2br(e($detail)) ?></p>
            <strong style="margin-top:20px;">PRICE</strong>
            <span class="val-price">฿<?= number_format((float)$price, 2) ?></span>
        </div>
    </div>
    <form method="POST" action="edit_save.php">
        <button type="submit" class="btn-confirm">CONFIRM & UPDATE</button>
        <a href="edit.php?id=<?= $id ?>" class="back-link">← กลับไปแก้ไขข้อมูล</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('theme-toggle');
        const updateIcon = (t) => btn.innerHTML = t === 'dark' ? '☀️' : '🌙';
        updateIcon(document.documentElement.getAttribute('data-theme'));
        btn.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next); localStorage.setItem('theme', next); updateIcon(next);
        });
    });
</script>
</body>
</html>