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
    // แก้ไขจุดที่ 1: เปลี่ยนชื่อตารางเป็น products
    $stmt = $pdo->prepare("SELECT img FROM products WHERE id = ?");
    $stmt->execute([$p['id']]);
    $old = $stmt->fetchColumn();
    $imgValue = $old;
}

/* UPDATE DB - บันทึกข้อมูลใหม่ลงฐานข้อมูล */
// แก้ไขจุดที่ 2: เปลี่ยนชื่อตารางเป็น products
$sql = "UPDATE products 
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
    <title>Update Successful - VINYL STATION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #0a0a0c; 
            color: #fff; 
            font-family: 'Kanit', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        .success-card { 
            background: #16161a; 
            padding: 40px; 
            border-radius: 25px; 
            border: 1px solid #222; 
            text-align: center; 
            max-width: 450px; 
            width: 90%;
            box-shadow: 0 10px 30px rgba(188, 19, 254, 0.1);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(188, 19, 254, 0.1);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            color: #bc13fe;
            font-size: 40px;
            border: 2px solid #bc13fe;
        }
        h2 { color: #fff; margin-bottom: 10px; font-weight: 600; }
        p { color: #888; font-size: 14px; margin-bottom: 30px; line-height: 1.6; }
        .product-name { color: #bc13fe; font-weight: 400; }
        
        .btn-group { display: flex; flex-direction: column; gap: 12px; }
        
        .btn-primary { 
            background: #bc13fe; 
            color: white; 
            text-decoration: none; 
            padding: 12px; 
            border-radius: 12px; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-primary:hover { box-shadow: 0 0 15px #bc13fe; transform: translateY(-2px); }
        
        .btn-outline { 
            background: transparent; 
            color: #888; 
            text-decoration: none; 
            padding: 12px; 
            border-radius: 12px; 
            border: 1px solid #333;
            font-size: 14px;
            transition: 0.3s; 
        }
        .btn-outline:hover { border-color: #bc13fe; color: #bc13fe; }
    </style>
</head>
<body>

<div class="success-card">
    <div class="icon-circle">✓</div>
    <h2>แก้ไขข้อมูลสำเร็จ!</h2>
    <p>ระบบได้บันทึกการเปลี่ยนแปลงของ <br>
       <span class="product-name">"<?= htmlspecialchars($p['productname']) ?>"</span> <br>
       เรียบร้อยแล้ว
    </p>

    <div class="btn-group">
        <a href="list.php" class="btn-primary">กลับไปยังคอลเลกชัน</a>
        <a href="edit.php?id=<?= $p['id'] ?>" class="btn-outline">แก้ไขรายการนี้อีกครั้ง</a>
    </div>
</div>

</body>
</html>