<?php
require __DIR__ . '/config.php';
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
        h1 { font-weight: 600; background: linear-gradient(to right, #bc13fe, #fe13bc); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-add { background: #bc13fe; color: white; text-decoration: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-add:hover { box-shadow: 0 0 15px #bc13fe; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; max-width: 1100px; margin: auto; }
        
        /* 1. บังคับให้ Card ทุกใบมีความสูงเท่ากันในแถวเดียวกัน */
        .card { 
            background: #16161a; 
            border-radius: 20px; 
            overflow: hidden; 
            border: 1px solid #222; 
            transition: 0.3s; 
            display: flex; 
            flex-direction: column; 
            height: 100%; /* สำคัญมาก */
        }
        .card:hover { transform: translateY(-10px); border-color: #bc13fe; box-shadow: 0 10px 25px rgba(188, 19, 254, 0.2); }
        .card img { width: 100%; aspect-ratio: 1/1; object-fit: cover; flex-shrink: 0; }

        /* 2. จัดการเนื้อหาให้ยืดเต็มพื้นที่ */
        .card-content { 
            padding: 20px; 
            flex-grow: 1; /* ดันเนื้อหาให้ยืดเต็มพื้นที่ */
            display: flex; 
            flex-direction: column; 
        }

        /* 3. บังคับความสูงคงที่เพื่อให้ปุ่มเริ่มในจุดเดียวกันเป๊ะ */
        .card-name { 
            font-weight: 600; 
            font-size: 18px; 
            margin-bottom: 5px; 
            color: #fff; 
            min-height: 54px; /* บังคับความสูง 2 บรรทัด */
            overflow: hidden;
        }
        
        .card-detail { 
            color: #888; 
            font-size: 14px; 
            margin-bottom: 15px; 
            min-height: 42px; /* บังคับความสูง 2 บรรทัด */
            overflow: hidden;
            line-height: 1.4;
        }
        
        .card-price { 
            color: #bc13fe; 
            font-weight: 600; 
            font-size: 20px; 
            margin-bottom: 15px; 
            margin-top: auto; /* ดันราคากับปุ่มลงล่างสุด */
        }
        
        /* 4. ปุ่ม Actions จะติดขอบล่างเสมอ */
        .actions { 
            display: flex; 
            gap: 10px; 
            padding-top: 5px;
        }
        
        .btn-edit { flex: 1; text-align: center; background: transparent; border: 1px solid #bc13fe; color: #bc13fe; text-decoration: none; padding: 8px; border-radius: 10px; font-size: 14px; transition: 0.3s; }
        .btn-edit:hover { background: #bc13fe; color: #fff; }
        .btn-delete { flex: 1; text-align: center; background: transparent; border: 1px solid #ff4d4d; color: #ff4d4d; text-decoration: none; padding: 8px; border-radius: 10px; font-size: 14px; transition: 0.3s; }
        .btn-delete:hover { background: #ff4d4d; color: #fff; }
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
        <?php if (!empty($p['img'])): ?>
            <img src="<?= htmlspecialchars($p['img']) ?>" alt="cover">
        <?php else: ?>
            <div style="aspect-ratio: 1/1; background: #333; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #666;">No Cover</div>
        <?php endif; ?>
        <div class="card-content">
            <div class="card-name"><?= htmlspecialchars($p['productname'] ?? 'Unknown Artist') ?></div>
            <div class="card-detail"><?= nl2br(htmlspecialchars($p['detail'] ?? '')) ?></div>
            <div class="card-price">฿<?= number_format($p['price'] ?? 0, 2) ?></div>
            
            <div class="actions">
                <a href="edit.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= htmlspecialchars($p['id']) ?>" 
                   class="btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this vibe?')">Delete</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</body>
</html>