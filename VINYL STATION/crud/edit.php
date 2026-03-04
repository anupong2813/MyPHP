<?php
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: ../views/list.php"); exit; }
require __DIR__ . '/../core/config.php';
require __DIR__ . '/../core/csrf.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?"); $stmt->execute([$id]); 
$product = $stmt->fetch();
if (!$product) die('Vibe not found');
$sticky = $_SESSION['sticky'] ?? []; unset($_SESSION['sticky']);
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Edit Vibe - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 40px 0; transition: 0.3s;}
        .container { background: var(--card); padding: 40px; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 15px 50px var(--shadow); border: 1px solid var(--border); transition: 0.3s; position: relative;}
        h2 { text-align: center; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 26px; margin-top: 0; margin-bottom: 30px; color: var(--text-main); letter-spacing: 1px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        h2 span { filter: grayscale(100%); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
        .full-width { grid-column: span 2; }
        label { display: block; margin-bottom: 8px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-family: 'Montserrat', sans-serif; font-weight: 700; }
        input, textarea, select { width: 100%; padding: 14px; margin-bottom: 20px; background: var(--input-bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-family: 'Kanit', sans-serif; font-size: 14px; box-sizing: border-box; transition: 0.3s; }
        input:focus, textarea:focus, select:focus { border-color: var(--text-main); outline: none;}
        input[type="file"] { padding: 10px; color: var(--text-muted); border: 1px dashed var(--border); background: transparent; }
        input[type="file"]::file-selector-button { background: var(--text-main); color: var(--bg); border: none; padding: 8px 16px; border-radius: 4px; font-weight: 600; font-family: 'Kanit', sans-serif; cursor: pointer; margin-right: 15px; transition: 0.3s; font-size: 13px; }
        select option { background: var(--bg); color: var(--text-main); }
        button { width: 100%; padding: 16px; background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; border-radius: 25px; font-size: 15px; font-weight: 900; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; margin-top: 10px; letter-spacing: 1px; }
        button:hover { background: #333333 !important; }
        .footer-link { text-align: center; margin-top: 25px; font-size: 13px; font-weight: 400; }
        .footer-link a { color: var(--text-muted); text-decoration: none; transition: 0.3s; }
        .footer-link a:hover { color: var(--text-main); }
        .theme-btn-inline { position: absolute; top: 20px; right: 20px; background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
        .theme-btn-inline:hover { background: var(--text-main); color: var(--bg); }
    </style>
</head>
<body>
<div class="container">
    <button id="theme-toggle" class="theme-btn-inline">🌙</button>
    <h2>EDIT VIBE <span>💿</span></h2>
    <form method="POST" action="edit_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= e($product['id']) ?>"><input type="hidden" name="old_img" value="<?= e($product['img']) ?>">
        <div class="form-grid">
            <div class="full-width"><label>ARTIST & ALBUM NAME</label><input type="text" name="productname" value="<?= e($sticky['productname'] ?? $product['productname']) ?>" required></div>
            
            <div class="full-width">
                <label>Category (Genre)</label>
                <input type="text" name="category" placeholder="เช่น Pop, R&B, Hip-Hop" value="<?= e($sticky['category'] ?? $product['category'] ?? '') ?>" required>
            </div>

            <div><label>COLOR</label><input type="text" name="color" value="<?= e($sticky['color'] ?? $product['color']) ?>"></div>
            <div><label>FORMAT</label><input type="text" name="format" value="<?= e($sticky['format'] ?? $product['format']) ?>"></div>
            <div><label>FIRST RELEASED</label><input type="text" name="released_date" value="<?= e($sticky['released_date'] ?? $product['released_date']) ?>"></div>
            <div><label>PRICE (฿)</label><input type="number" step="0.01" name="price" value="<?= e($sticky['price'] ?? $product['price']) ?>" required></div>
            <div class="full-width"><label>ALBUM DETAILS / CONDITION</label><input type="text" name="detail" value="<?= e($sticky['detail'] ?? $product['detail']) ?>" required></div>
            <div class="full-width"><label>SPOTIFY ALBUM LINK</label><input type="url" name="spotify_url" value="<?= e($sticky['spotify_url'] ?? $product['spotify_url']) ?>"></div>
            <div class="full-width"><label>NEW COVER (FILE)</label><input type="file" name="img_upload" accept="image/*"></div>
        </div>
        <button type="submit">PREVIEW CHANGES</button>
        <div class="footer-link"><a href="../views/list.php">← Cancel</a></div>
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