<?php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['preview'])) { die('ไม่พบข้อมูลสำหรับบันทึก'); }
$p = $_SESSION['preview'];
$imgValue = null;

if ($p['source'] === 'upload') {
    $tempPath = __DIR__.'/'.$p['preview_img'];
    if (file_exists($tempPath)) {
        $uploadDir = __DIR__.'/uploads';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $file = basename($tempPath);
        rename($tempPath, "$uploadDir/$file");
        $imgValue = "uploads/$file";
    }
} elseif ($p['source'] === 'url') { $imgValue = $p['preview_img']; }

// อัปเดตคำสั่ง INSERT ให้บันทึกครบทุกค่า
$sql = "INSERT INTO products (productname, detail, price, img, color, format, released_date) VALUES (:n, :d, :p, :i, :c, :f, :r)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':n' => $p['productname'],
    ':d' => $p['detail'],
    ':p' => $p['price'],
    ':i' => $imgValue,
    ':c' => $p['color'],
    ':f' => $p['format'],
    ':r' => $p['released_date']
]);

unset($_SESSION['preview']);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Success - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff00e6; --bg: #111111; --card: #2a2a2a; --text-muted: #a0a0a0; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        
        .success-card { background: var(--card); padding: 50px 40px; border-radius: 12px; text-align: center; max-width: 420px; width: 100%; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        
        /* Glow Effect สีชมพู */
        .icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 25px; color: var(--primary); font-size: 36px; border: 3px solid var(--primary); box-shadow: 0 0 25px rgba(255, 0, 230, 0.3), inset 0 0 15px rgba(255, 0, 230, 0.1); }
        
        h2 { font-family: 'Kanit', sans-serif; font-weight: 600; margin-top: 0; margin-bottom: 12px; font-size: 24px; color: #fff; }
        p { color: var(--text-muted); font-size: 15px; margin-bottom: 35px; line-height: 1.6; }
        .product-name { color: var(--primary); font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; }
        
        .btn-group { display: flex; flex-direction: column; gap: 15px; }
        .btn-primary { background: var(--primary); color: #111; text-decoration: none; padding: 16px; border-radius: 6px; font-weight: 900; font-family: 'Montserrat', sans-serif; font-size: 14px; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
        .btn-primary:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,255,255,0.1); }
    </style>
</head>
<body>
<div class="success-card">
    <div class="icon-circle">✓</div>
    <h2>เพิ่มข้อมูลสำเร็จ!</h2>
    <p>ระบบได้บันทึกการเปลี่ยนแปลงของ <br>
       <span class="product-name">"<?= htmlspecialchars($p['productname']) ?>"</span> <br>ลงในคอลเลกชันเรียบร้อยแล้ว
    </p>
    <div class="btn-group">
        <a href="list.php" class="btn-primary">กลับไปยังคอลเลกชัน</a>
    </div>
</div>
</body>
</html>