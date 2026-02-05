<?php
session_start();
require __DIR__ . '/csrf.php';

$sticky = $_SESSION['sticky'] ?? [];
unset($_SESSION['sticky']);

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>เพิ่มเมนูน้ำผลไม้เพื่อสุขภาพ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* ส่วนตกแต่งที่ทำให้ฟอร์มดูดีขึ้น */
        body { 
            background-color: #f4f9f4; 
            font-family: 'system-ui', 'Segoe UI', sans-serif; 
            padding: 40px 20px; 
        }
        .container { 
            max-width: 500px; 
            margin: auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        h1 { 
            color: #2d5a27; 
            text-align: center; 
            margin-bottom: 25px;
            font-size: 24px;
        }
        label { 
            display: block; 
            margin-top: 15px; 
            font-weight: bold;
            color: #444;
        }
        input[type="text"], 
        input[type="number"], 
        input[type="url"], 
        textarea { 
            width: 100%; 
            padding: 12px; 
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; /* ป้องกัน Input ล้นขอบ */
        }
        input[type="file"] {
            margin-top: 8px;
        }
        button { 
            width: 100%;
            margin-top: 25px; 
            padding: 12px; 
            background-color: #4caf50; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px;
            font-weight: bold;
            cursor: pointer; 
            transition: background 0.3s;
        }
        button:hover { 
            background-color: #45a049; 
        }
        .note {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🥤 เพิ่มเมนูน้ำผลไม้</h1>

    <form method="POST" action="create_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <label>ชื่อเมนูน้ำผลไม้</label>
        <input type="text" name="productname" placeholder="เช่น น้ำสกัดเย็นสูตร Detox" value="<?= e($sticky['productname'] ?? '') ?>" required>

        <label>สรรพคุณและรายละเอียด</label>
        <textarea name="detail" rows="4" placeholder="ระบุประโยชน์ของเครื่องดื่ม..." required><?= e($sticky['detail'] ?? '') ?></textarea>

        <label>ราคาต่อขวด (บาท)</label>
        <input type="number" step="0.01" name="price" placeholder="0.00" value="<?= e($sticky['price'] ?? '') ?>" required>

        <label>อัปโหลดรูปภาพสินค้า</label>
        <input type="file" name="img_upload" accept="image/*">
        <div class="note">* รองรับไฟล์ JPG, PNG, GIF</div>

        <label>หรือ ใส่ลิงก์รูปภาพ (URL)</label>
        <input type="url" name="img_url" placeholder="https://example.com/image.jpg" value="<?= e($sticky['img_url'] ?? '') ?>">

        <button type="submit">ดูตัวอย่างข้อมูล (Preview)</button>
    </form>
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="list.php" style="color: #666; text-decoration: none; font-size: 14px;">← กลับไปหน้ารายการสินค้า</a>
    </div>
</div>

</body>
</html>