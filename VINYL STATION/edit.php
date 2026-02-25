<?php
session_start();
require __DIR__ . '/config.php';
require __DIR__ . '/csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]); 
$product = $stmt->fetch();

if (!$product) die('Vibe not found');

$sticky = $_SESSION['sticky'] ?? [];
unset($_SESSION['sticky']);

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Edit Vibe - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff00e6; --bg: #111111; --card: #2a2a2a; --input-bg: #1f1f1f; --text-muted: #a0a0a0; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 40px 0;}
        
        .container { background: var(--card); padding: 40px; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        h2 { text-align: center; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 26px; margin-top: 0; margin-bottom: 30px; color: var(--primary); letter-spacing: 1px; display: flex; align-items: center; justify-content: center; gap: 8px; text-transform: uppercase; }
        h2 span { filter: grayscale(100%); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
        .full-width { grid-column: span 2; }

        label { display: block; margin-bottom: 8px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-family: 'Montserrat', sans-serif; font-weight: 700; letter-spacing: 0.5px; }
        input, textarea { width: 100%; padding: 14px; margin-bottom: 20px; background: var(--input-bg); border: 1px solid #333; border-radius: 6px; color: #fff; font-family: 'Kanit', sans-serif; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
        input:focus, textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 2px rgba(255, 0, 230, 0.2); }
        
        input[type="file"] { padding: 10px; color: #ccc; border: 1px dashed #555; background: transparent; }
        input[type="file"]::file-selector-button { background: #fff; color: #111; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 600; font-family: 'Kanit', sans-serif; cursor: pointer; margin-right: 15px; transition: 0.3s; font-size: 13px; }
        input[type="file"]::file-selector-button:hover { background: var(--primary); }
        
        button { width: 100%; padding: 16px; background: var(--primary); color: #111; border: none; border-radius: 6px; font-size: 15px; font-weight: 900; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; margin-top: 10px; letter-spacing: 1px; }
        button:hover { background: #fff; transform: translateY(-2px); }
        .footer-link { text-align: center; margin-top: 25px; font-size: 13px; font-weight: 400; }
        .footer-link a { color: #888; text-decoration: none; transition: 0.3s; }
        .footer-link a:hover { color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <h2>EDIT VIBE <span>💿</span></h2>
    <form method="POST" action="edit_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= e($product['id']) ?>">
        <input type="hidden" name="old_img" value="<?= e($product['img']) ?>">
        
        <div class="form-grid">
            <div class="full-width">
                <label>ARTIST & ALBUM NAME</label>
                <input type="text" name="productname" value="<?= e($sticky['productname'] ?? $product['productname']) ?>" required>
            </div>

            <div>
                <label>COLOR</label>
                <input type="text" name="color" value="<?= e($sticky['color'] ?? $product['color']) ?>">
            </div>

            <div>
                <label>FORMAT</label>
                <input type="text" name="format" value="<?= e($sticky['format'] ?? $product['format']) ?>">
            </div>

            <div>
                <label>FIRST RELEASED</label>
                <input type="text" name="released_date" value="<?= e($sticky['released_date'] ?? $product['released_date']) ?>">
            </div>

            <div>
                <label>PRICE (฿)</label>
                <input type="number" step="0.01" name="price" value="<?= e($sticky['price'] ?? $product['price']) ?>" required>
            </div>
            
            <div class="full-width">
                <label>ALBUM DETAILS / CONDITION</label>
                <input type="text" name="detail" value="<?= e($sticky['detail'] ?? $product['detail']) ?>" required>
            </div>

            <div class="full-width">
                <label>NEW COVER (FILE)</label>
                <input type="file" name="img_upload" accept="image/*">
            </div>
        </div>
        
        <button type="submit">PREVIEW CHANGES</button>
        <div class="footer-link"><a href="list.php">← Cancel</a></div>
    </form>
</div>
</body>
</html>