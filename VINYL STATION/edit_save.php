<?php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['preview'])) { die('No preview data. Please start again.'); }

$p = $_SESSION['preview'];
$imgValue = null;

if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/'.$p['preview_img'];
    if (!file_exists($tempPath)) die('Uploaded image not found.');
    $uploadDir = __DIR__.'/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $file = basename($tempPath); 
    rename($tempPath, "$uploadDir/$file"); 
    $imgValue = "uploads/$file";
} elseif ($p['source'] === 'url') {
    $imgValue = $p['preview_img'];
} elseif ($p['keep_old_image']) {
    $stmt = $pdo->prepare("SELECT img FROM products WHERE id = ?");
    $stmt->execute([$p['id']]); 
    $imgValue = $stmt->fetchColumn();
}

// ลบรูปเก่าทิ้งถ้ามีการเปลี่ยนรูปใหม่
if (!$p['keep_old_image'] && !empty($p['old_img'])) {
    $oldImgPath = __DIR__ . '/' . $p['old_img'];
    if (file_exists($oldImgPath) && !filter_var($p['old_img'], FILTER_VALIDATE_URL)) {
        @unlink($oldImgPath);
    }
}

// === แก้ไขตรงนี้: เพิ่ม c, f, r เข้าไปในคำสั่ง SQL ให้ครบ ===
$sql = "UPDATE products SET productname = :n, detail = :d, price = :p, img = :i, color = :c, format = :f, released_date = :r WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':n' => $p['productname'], 
    ':d' => $p['detail'], 
    ':p' => $p['price'], 
    ':i' => $imgValue,
    ':c' => $p['color'],
    ':f' => $p['format'],
    ':r' => $p['released_date'],
    ':id' => $p['id']
]);
unset($_SESSION['preview']);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Update Successful - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff00e6; --bg: #111111; --card: #2a2a2a; --text-muted: #a0a0a0; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .success-card { background: var(--card); padding: 40px 30px; border-radius: 12px; text-align: center; max-width: 400px; width: 100%; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        .icon-circle { width: 70px; height: 70px; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px; color: var(--primary); font-size: 32px; border: 2px solid var(--primary); }
        h2 { font-family: 'Kanit', sans-serif; font-weight: 600; margin-top: 0; margin-bottom: 10px; font-size: 22px; }
        p { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; line-height: 1.6; }
        .product-name { color: var(--primary); font-family: 'Montserrat', sans-serif; font-weight: 700; }
        .btn-group { display: flex; flex-direction: column; gap: 12px; }
        .btn-primary { background: var(--primary); color: #111; text-decoration: none; padding: 14px; border-radius: 6px; font-weight: 700; font-family: 'Montserrat', sans-serif; font-size: 14px; transition: 0.3s; text-transform: uppercase; }
        .btn-primary:hover { background: #fff; }
    </style>
</head>
<body>
<div class="success-card">
    <div class="icon-circle">✓</div>
    <h2>แก้ไขข้อมูลสำเร็จ!</h2>
    <p>ระบบได้บันทึกการเปลี่ยนแปลงของ <br>
       <span class="product-name">"<?= htmlspecialchars($p['productname']) ?>"</span> <br>เรียบร้อยแล้ว
    </p>
    <div class="btn-group">
        <a href="list.php" class="btn-primary">กลับไปยังคอลเลกชัน</a>
    </div>
</div>
</body>
</html>