<?php
session_start();
// แก้ Path เรียกใช้ csrf.php
require __DIR__ . '/core/csrf.php';

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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff00e6; --bg: #111111; --card: #2a2a2a; --input-bg: #1f1f1f; --text-muted: #a0a0a0; }
        body { background-color: var(--bg); color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 40px 0;}
        .container { background: var(--card); padding: 40px; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        h1 { text-align: center; font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 26px; margin-top: 0; margin-bottom: 30px; color: var(--primary); letter-spacing: 1px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        h1 span { filter: grayscale(100%); font-style: normal; }
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
    <h1><span>💿</span> VINYL STATION</h1>
    
    <form method="POST" action="crud/create_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
        
        <div class="form-grid">
            <div class="full-width">
                <label>Artist & Album Name</label>
                <input type="text" name="productname" placeholder="เช่น NewJeans - Get Up" value="<?= e($sticky['productname'] ?? '') ?>" required>
            </div>
            <div>
                <label>Color</label>
                <input type="text" name="color" placeholder="เช่น Black" value="<?= e($sticky['color'] ?? '') ?>">
            </div>
            <div>
                <label>Format</label>
                <input type="text" name="format" placeholder="เช่น Vinyl 1LP" value="<?= e($sticky['format'] ?? '') ?>">
            </div>
            <div>
                <label>First Released</label>
                <input type="text" name="released_date" placeholder="เช่น 1st ม.ค. 1984" value="<?= e($sticky['released_date'] ?? '') ?>">
            </div>
            <div>
                <label>Price (THB)</label>
                <input type="number" step="0.01" name="price" placeholder="0.00" value="<?= e($sticky['price'] ?? '') ?>" required>
            </div>
            <div class="full-width">
                <label>Album Details / Condition</label>
                <input type="text" name="detail" placeholder="ระบุรายละเอียดแผ่นหรือสภาพแผ่น..." value="<?= e($sticky['detail'] ?? '') ?>" required>
            </div>
            <div class="full-width">
                <label>Spotify Album Link</label>
                <input type="url" name="spotify_url" placeholder="เช่น https://open.spotify.com/album/..." value="<?= e($sticky['spotify_url'] ?? '') ?>">
            </div>
            <div class="full-width">
                <label>Album Cover Image</label>
                <input type="file" name="img_upload" accept="image/*">
            </div>
        </div>
        
        <button type="submit">DROP IT! (PREVIEW)</button>
    </form>
    
    <div class="footer-link">
        <a href="views/list.php">← Back to Collection</a>
    </div>
</div>
</body>
</html>