<?php
session_start();
// --- ป้องกัน Admin เข้าถึงหน้าตะกร้า ---
if (!empty($_SESSION['is_admin'])) { header("Location: list.php"); exit; }

require __DIR__ . '/../core/config.php';
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
if (!isset($_SESSION['favorites'])) { $_SESSION['favorites'] = []; }

$action = $_GET['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id) { $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1; }
    header('Location: cart.php'); exit;
}
if ($action === 'remove') {
    $remove_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($remove_id && isset($_SESSION['cart'][$remove_id])) { unset($_SESSION['cart'][$remove_id]); }
    header('Location: cart.php'); exit;
}
if ($action === 'clear') { $_SESSION['cart'] = []; header('Location: cart.php'); exit; }

$cartItems = []; $totalPrice = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, productname, price, img FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll();
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $totalPrice += $subtotal;
        $p['qty'] = $qty; $p['subtotal'] = $subtotal;
        $cartItems[] = $p;
    }
}
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function getDisplayImg($img) {
    if (empty($img)) return ''; if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return '../' . (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Cart - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; transition: 0.3s;}
        
        .navbar { background-color: var(--card); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); transition: 0.3s;}
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 28px; color: var(--text-main); text-decoration: none; letter-spacing: 1px; }
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .nav-btn { padding: 10px 20px; border-radius: 25px; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; text-transform: capitalize; transition: 0.3s; display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; }
        .fav-btn { background: transparent !important; color: var(--text-main) !important; border: 2px solid var(--border) !important; }
        .fav-btn:hover { border-color: var(--text-main) !important; }
        
        .container { max-width: 1000px; margin: 50px auto; padding: 0 40px; position: relative; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
        .page-header h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; margin: 0; font-size: 32px; color: var(--text-main); }
        .header-actions { display: flex; gap: 15px; align-items: center; }
        .back-link { color: var(--text-muted); text-decoration: none; font-size: 14px; transition: 0.3s; font-weight: 700; }
        .back-link:hover { color: var(--text-main); }
        
        .theme-btn-inline { background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; box-shadow: 0 2px 5px var(--shadow); }
        .theme-btn-inline:hover { transform: scale(1.1); background: var(--text-main); color: var(--bg); }

        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table th { text-align: left; padding: 15px; border-bottom: 1px solid var(--border); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .cart-table td { padding: 20px 15px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .product-col { display: flex; align-items: center; gap: 20px; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; background: #000000; border: 1px solid #222;}
        .product-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; margin: 0; color: var(--text-main);}
        .price-col { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 15px; color: var(--text-main);}
        .qty-col { font-family: 'Montserrat', sans-serif; font-weight: 700; color: var(--text-main);}
        .subtotal-col { font-family: 'Montserrat', sans-serif; font-weight: 900; color: var(--text-main); font-size: 16px; }
        .btn-remove { color: #ff3b30; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .btn-remove:hover { text-decoration: underline; }
        
        .cart-summary { background: var(--card); padding: 30px; border-radius: 12px; text-align: right; border: 1px solid var(--border); box-shadow: 0 4px 15px var(--shadow);}
        .summary-text { color: var(--text-muted); font-size: 14px; margin-bottom: 10px; font-weight: 700; }
        .total-price { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 36px; color: var(--text-main); margin-bottom: 20px; }
        
        .btn-checkout { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; padding: 12px 30px; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 15px; border-radius: 25px; cursor: pointer; transition: 0.3s; text-transform: capitalize; }
        .btn-checkout:hover { background: #333333 !important; }
        .btn-clear { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; padding: 10px 20px; border-radius: 25px; cursor: pointer; text-decoration: none; font-size: 13px; margin-right: 15px; transition: 0.3s; font-weight: 800; font-family: 'Montserrat', sans-serif;}
        .btn-clear:hover { background: #f0f0f0 !important; }
        
        .empty-cart { text-align: center; padding: 80px 0; color: var(--text-muted); font-size: 18px; }
    </style>
</head>
<body>
<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    <div class="nav-actions">
        <a href="favorites.php" class="nav-btn fav-btn">Favorites (<?= count($_SESSION['favorites']) ?>)</a>
    </div>
</div>
<div class="container">
    <div class="page-header">
        <h1>YOUR CART</h1>
        <div class="header-actions">
            <a href="list.php" class="back-link">CONTINUE SHOPPING ❯</a>
            <button id="theme-toggle" class="theme-btn-inline">🌙</button>
        </div>
    </div>
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart"><div style="font-size: 48px; margin-bottom: 15px;">🛒</div>ตะกร้าสินค้าของคุณยังว่างเปล่า<br><br><a href="list.php" style="color: var(--text-main); text-decoration: underline;">ไปเลือกแผ่นเสียงกันเถอะ!</a></div>
    <?php else: ?>
        <table class="cart-table">
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><div class="product-col">
                            <?php $imgSrc = getDisplayImg($item['img']); if ($imgSrc): ?><img src="<?= e($imgSrc) ?>" class="product-img" alt="cover"><?php else: ?><div class="product-img" style="display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:10px; font-weight:bold;">NO COVER</div><?php endif; ?>
                            <p class="product-name"><?= e($item['productname']) ?></p></div></td>
                    <td class="price-col">฿<?= number_format($item['price'], 2) ?></td>
                    <td class="qty-col">x<?= $item['qty'] ?></td>
                    <td class="subtotal-col">฿<?= number_format($item['subtotal'], 2) ?></td>
                    <td><a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn-remove">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-summary">
            <div class="summary-text">TOTAL ESTIMATE (<?= array_sum($_SESSION['cart']) ?> ITEMS)</div>
            <div class="total-price">฿<?= number_format($totalPrice, 2) ?></div>
            <a href="cart.php?action=clear" class="btn-clear" onclick="return confirm('ต้องการล้างตะกร้าทั้งหมดใช่หรือไม่?')">Clear Cart</a>
            <button class="btn-checkout" onclick="alert('Proceeding to checkout!')">Checkout Securely</button>
        </div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('theme-toggle');
        if(btn) {
            const updateIcon = (t) => btn.innerHTML = t === 'dark' ? '☀️' : '🌙';
            updateIcon(document.documentElement.getAttribute('data-theme'));
            btn.addEventListener('click', () => {
                const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('theme', next); updateIcon(next);
            });
        }
    });
</script>
</body>
</html>