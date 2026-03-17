<?php
session_start();
if (empty($_SESSION['is_admin'])) { header("Location: views/list.php"); exit; }
require __DIR__ . '/core/config.php';

$stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
$orders = $stmt->fetchAll();

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function getDisplayImg($img) {
    if (empty($img)) return ''; if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Manage Orders - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 40px; transition: 0.3s; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px;}
        h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; margin: 0; font-size: 32px; color: var(--text-main); text-transform: uppercase;}
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 700; transition: 0.3s; }
        .back-link:hover { color: var(--text-main); }
        
        .order-card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 25px; margin-bottom: 20px; display: flex; gap: 20px; align-items: flex-start; box-shadow: 0 5px 15px var(--shadow);}
        .o-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; background: #000; border: 1px solid #222;}
        .o-details { flex-grow: 1; }
        .o-id { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-muted); margin-bottom: 8px;}
        .o-items { font-weight: 600; font-size: 18px; margin-bottom: 8px; color: var(--text-main);}
        .o-customer { font-size: 14px; color: var(--text-muted); margin-bottom: 6px; }
        .o-address { font-size: 14px; color: var(--text-main); background: var(--bg); padding: 10px; border-radius: 6px; border: 1px solid var(--border); margin-top: 10px; display: inline-block;}
        
        .o-right { text-align: right; min-width: 150px; }
        .o-price { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 24px; color: #ff3b30;}
        .btn-slip { background: #111; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 12px; transition: 0.3s; display: inline-block; margin-top: 15px;}
        .btn-slip:hover { background: #333; }
        .date { font-size: 12px; color: var(--text-muted); margin-top: 15px;}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CUSTOMER ORDERS 📦</h1>
            <a href="views/list.php" class="back-link">❮ Back to Collection</a>
        </div>
        
        <?php if(empty($orders)): ?>
            <p style="text-align:center; color:var(--text-muted); padding: 50px;">ยังไม่มีคำสั่งซื้อ</p>
        <?php else: ?>
            <?php foreach($orders as $o): ?>
            <div class="order-card">
                <?php $imgSrc = getDisplayImg($o['main_img']); if($imgSrc): ?>
                    <img src="<?= e($imgSrc) ?>" class="o-img" alt="Product">
                <?php else: ?>
                    <div class="o-img" style="display:flex; align-items:center; justify-content:center; color:#555; font-size:12px; font-weight:bold;">NO IMG</div>
                <?php endif; ?>
                
                <div class="o-details">
                    <div class="o-id">ORDER ID: #<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    <div class="o-items"><?= e($o['order_details']) ?></div>
                    <div class="o-customer">👤 <?= e($o['customer_name']) ?> &nbsp;|&nbsp; 📞 <?= e($o['contact_info']) ?></div>
                    <div class="o-address">📍 <strong>จัดส่งที่:</strong> <?= e($o['shipping_address'] ?? 'ไม่ระบุ') ?></div>
                </div>
                
                <div class="o-right">
                    <div class="o-price">฿<?= number_format($o['total_price'], 2) ?></div>
                    <a href="<?= e(getDisplayImg($o['slip_img'])) ?>" target="_blank" class="btn-slip">🔍 View Slip</a>
                    <div class="date">📅 <?= date('d M Y, H:i', strtotime($o['order_date'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>