<?php
session_start(); // เริ่ม Session เพื่อใช้งานระบบตะกร้า
require __DIR__ . '/config.php';

// รับค่า id จาก URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ไม่พบรหัสแผ่นเสียง');
}

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die('ไม่พบแผ่นเสียงที่คุณต้องการ');
}

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title><?= e($product['productname']) ?> - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        /* เปลี่ยนสี Accent เป็นสีชมพูบานเย็น */
        :root { --bg-color: #111111; --nav-bg: #1a1a1a; --accent: #ff00e6; --text-main: #ffffff; --text-muted: #a0a0a0; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        
        /* Navbar */
        .navbar { background-color: var(--nav-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; }
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 28px; color: #fff; text-decoration: none; letter-spacing: 1px; }
        .nav-actions { display: flex; align-items: center; gap: 20px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; }
        .nav-actions a { color: var(--text-main); text-decoration: none; transition: 0.3s; }
        .nav-actions a:hover { color: var(--accent); }

        /* Container หน้า View */
        .view-container { max-width: 1200px; margin: 50px auto; padding: 0 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
        
        /* ฝั่งซ้าย รูปภาพ */
        .view-image { background: #fff; display: flex; align-items: center; justify-content: center; position: relative; padding: 40px; border-radius: 4px; }
        .view-image img { width: 100%; max-width: 500px; object-fit: cover; box-shadow: 15px 15px 30px rgba(0,0,0,0.5); }
        .badge { position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.9); color: #fff; font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 700; padding: 6px 12px; border: 1px solid #444; border-radius: 20px; }
        .back-btn { position: absolute; top: 20px; left: 20px; color: #888; text-decoration: none; font-size: 24px; transition: 0.3s; }
        .back-btn:hover { color: #111; }

        /* ฝั่งขวา ข้อมูล */
        .view-info { display: flex; flex-direction: column; }
        .view-title { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 36px; margin: 0 0 5px 0; text-transform: uppercase; line-height: 1.1; }
        .view-subtitle { color: var(--text-muted); font-size: 16px; margin-bottom: 25px; }
        .view-price { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 28px; color: var(--text-main); margin-bottom: 25px; }

        /* ปุ่ม Add to Cart */
        .btn-group { display: flex; gap: 15px; margin-bottom: 30px; }
        .btn-cart { background: var(--accent); color: #111; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 14px; padding: 15px 40px; border: none; border-radius: 4px; cursor: pointer; transition: 0.3s; text-transform: uppercase; height: 100%; }
        .btn-cart:hover { background: #fff; box-shadow: 0 5px 15px rgba(255,0,230,0.4); transform: translateY(-2px); }

        /* ส่วนรายละเอียด */
        .detail-box { border-top: 1px solid #333; border-bottom: 1px solid #333; padding: 25px 0; margin-bottom: 30px; }
        .detail-text { font-size: 14px; color: #ccc; line-height: 1.6; }

        /* ส่วนสเปค */
        .specs-list { list-style: none; padding: 0; margin: 0; }
        .specs-list li { display: flex; align-items: center; gap: 15px; font-size: 15px; color: var(--text-muted); margin-bottom: 15px; }
        .specs-list li strong { color: #fff; font-family: 'Montserrat', sans-serif; font-weight: 700; margin-right: 5px; }
        .spec-icon { color: var(--accent); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .spec-icon svg { width: 100%; height: 100%; }

        @media (max-width: 900px) {
            .view-container { grid-template-columns: 1fr; gap: 40px; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    <div class="nav-actions">
        <a href="cart.php" style="color: var(--accent);">🛒 MY CART (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
    </div>
</div>

<div class="view-container">
    <div class="view-image">
        <a href="list.php" class="back-btn" title="Back to Collection">❮</a>
        <span class="badge">VINYL</span>
        <?php if (!empty($product['img'])): ?>
            <img src="<?= e($product['img']) ?>" alt="cover">
        <?php else: ?>
            <div style="font-family: 'Montserrat', sans-serif; font-weight: 900; color:#ccc; font-size:24px;">NO COVER</div>
        <?php endif; ?>
    </div>

    <div class="view-info">
        <h1 class="view-title"><?= e($product['productname']) ?></h1>
        <div class="view-subtitle">Vinyl Records Collection</div>
        
        <div class="view-price">฿<?= number_format($product['price'], 2) ?></div>
        
        <div class="btn-group">
            <form action="cart.php?action=add" method="POST" style="display:flex; width: 100%;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" class="btn-cart" style="width: 100%;">ADD TO CART</button>
            </form>
        </div>

        <div class="detail-box">
            <div class="detail-text">
                <?= nl2br(e($product['detail'])) ?>
            </div>
        </div>

        <ul class="specs-list">
            <li>
                <div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="10.5" r="1.5"></circle><circle cx="8.5" cy="10.5" r="1.5"></circle><circle cx="11" cy="7" r="1.5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.6-.7 1.6-1.6 0-.4-.1-.8-.4-1.1-.3-.3-.5-.7-.5-1.1 0-.9.7-1.6 1.6-1.6H16c3.3 0 6-2.7 6-6 0-5.5-4.5-10-10-10z"></path></svg></div>
                <div><strong>Color:</strong> <?= e($product['color']) ?: 'Standard Black' ?></div>
            </li>
            <li>
                <div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg></div>
                <div><strong>Format:</strong> <?= e($product['format']) ?: 'Vinyl 1LP' ?></div>
            </li>
            <li>
                <div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><polygon points="12 14 13.5 17 17 17.5 14.5 20 15 23.5 12 21.5 9 23.5 9.5 20 7 17.5 10.5 17 12 14"></polygon></svg></div>
                <div><strong>First released:</strong> <?= e($product['released_date']) ?: 'Unknown' ?></div>
            </li>
        </ul>
    </div>
</div>

</body>
</html>