<?php
session_start();
require __DIR__ . '/csrf.php';

// ตรวจสอบว่ามี token หรือยัง
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sticky = $_SESSION['sticky'] ?? [];
unset($_SESSION['sticky']);

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Vinyl Station - Add New Beat</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #bc13fe; --bg: #0a0a0c; --card: #16161a; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: var(--card); padding: 40px; border-radius: 24px; box-shadow: 0 0 20px rgba(188, 19, 254, 0.2); width: 100%; max-width: 450px; border: 1px solid #222; }
        
        h1 { 
            text-align: center; 
            font-weight: 600; 
            font-size: 28px; 
            margin-bottom: 30px; 
            background: linear-gradient(to right, #bc13fe, #fe13bc); 
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent; 
        }

        label { display: block; margin-bottom: 8px; font-size: 14px; color: #aaa; }
        input, textarea { width: 100%; padding: 12px; margin-bottom: 20px; background: #222; border: 1px solid #333; border-radius: 12px; color: #fff; font-size: 16px; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 10px rgba(188, 19, 254, 0.3); }
        button { width: 100%; padding: 15px; background: var(--primary); color: white; border: none; border-radius: 12px; font-size: 18px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(188, 19, 254, 0.4); }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .footer-link a { color: #888; text-decoration: none; transition: 0.3s; }
        .footer-link a:hover { color: var(--primary); }
    </style>
</head>
<body>
<div class="container">
    <h1>💿 VINYL STATION</h1>
    <form method="POST" action="create_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
        
        <label>Artist & Album Name</label>
        <input type="text" name="productname" placeholder="เช่น NewJeans - Get Up" value="<?= e($sticky['productname'] ?? '') ?>" required>

        <label>Album Details / Condition</label>
        <input type="text" name="detail" placeholder="ระบุรายละเอียดแผ่นหรือสภาพแผ่น..." value="<?= e($sticky['detail'] ?? '') ?>" required>

        <label>Price (THB)</label>
        <input type="number" step="0.01" name="price" placeholder="0.00" value="<?= e($sticky['price'] ?? '') ?>" required>

        <label>Album Cover Image</label>
        <input type="file" name="img_upload" accept="image/*">
        
        <button type="submit">DROP IT! (Preview)</button>
    </form>
    <div class="footer-link">
        <a href="list.php">← Back to Collection</a>
    </div>
</div>
</body>
</html>