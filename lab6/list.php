<?php
require __DIR__ . '/config.php';
// ดึงข้อมูลจากตาราง products (ตรงตามที่อยู่ใน phpMyAdmin ของคุณ)
$sql = "SELECT id, productname, detail, price, img FROM products ORDER BY id DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Vinyl Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0a0a0c; color: #fff; font-family: 'Kanit', sans-serif; padding: 40px; margin: 0; }
        .header { display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto 40px; }
        h1 { font-weight: 600; background: linear-gradient(to right, #bc13fe, #fe13bc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-add { background: #bc13fe; color: white; text-decoration: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { box-shadow: 0 0 15px #bc13fe; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; max-width: 1100px; margin: auto; }
        .card { background: #16161a; border-radius: 20px; overflow: hidden; border: 1px solid #222; transition: 0.3s; }
        .card:hover { transform: translateY(-10px); border-color: #bc13fe; }
        .card img { width: 100%; aspect-ratio: 1/1; object-fit: cover; }
        .card-content { padding: 20px; }
        .card-name { font-weight: 600; font-size: 18px; margin-bottom: 5px; }
        .card-detail { color: #888; font-size: 14px; margin-bottom: 15px; }
        .card-price { color: #bc13fe; font-weight: 600; font-size: 20px; }
    </style>
</head>
<body>
<div class="header">
    <h1>COLLECTION 💿</h1>
    <a href="index.php" class="btn-add">+ ADD NEW VIBE</a>
</div>

<div class="grid">
    <?php foreach ($products as $p): ?>
    <div class="card">
        <?php if ($p['img']): ?>
            <img src="<?= htmlspecialchars($p['img']) ?>" alt="cover">
        <?php else: ?>
            <div style="aspect-ratio: 1/1; background: #333; display: flex; align-items: center; justify-content: center;">No Cover</div>
        <?php endif; ?>
        <div class="card-content">
            <div class="card-name"><?= htmlspecialchars($p['productname']) ?></div>
            <div class="card-detail"><?= nl2br(htmlspecialchars($p['detail'])) ?></div>
            <div class="card-price">฿<?= number_format($p['price'], 2) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</body>
</html>