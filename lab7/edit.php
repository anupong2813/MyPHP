<?php
session_start();
require __DIR__ . '/config.php';
require __DIR__ . '/csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die('Vibe not found');

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Edit Vibe</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0a0a0c; color: #fff; font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: #16161a; padding: 40px; border-radius: 25px; border: 1px solid #222; width: 100%; max-width: 450px; }
        h2 { color: #bc13fe; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #888; font-size: 14px; }
        .form-control { width: 100%; background: #0a0a0c; border: 1px solid #333; padding: 12px; border-radius: 12px; color: #fff; box-sizing: border-box; }
        .form-control:focus { border-color: #bc13fe; outline: none; box-shadow: 0 0 10px rgba(188, 19, 254, 0.2); }
        .btn-save { background: #bc13fe; color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { box-shadow: 0 0 20px #bc13fe; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <h2>EDIT VIBE 💿</h2>
    <form method="POST" action="edit_pre.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= e($product['id']) ?>">
        
        <div class="form-group">
            <label>PRODUCT NAME</label>
            <input type="text" name="productname" class="form-control" value="<?= e($product['productname']) ?>" required>
        </div>
        <div class="form-group">
            <label>DETAIL</label>
            <textarea name="detail" class="form-control" rows="3"><?= e($product['detail']) ?></textarea>
        </div>
        <div class="form-group">
            <label>PRICE (฿)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= e($product['price']) ?>" required>
        </div>
        <div class="form-group">
            <label>NEW COVER (FILE)</label>
            <input type="file" name="img_upload" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn-save">PREVIEW CHANGES</button>
        <a href="list.php" class="back-link">← Cancel</a>
    </form>
</div>
</body>
</html>