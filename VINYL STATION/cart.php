<?php
session_start();
require __DIR__ . '/config.php';

// สร้าง session cart ถ้ายังไม่มี
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';

// --- ระบบเพิ่มสินค้าลงตะกร้า ---
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id) {
        // ถ้ามีสินค้านี้ในตะกร้าแล้ว ให้บวกจำนวนขึ้น 1 ถ้ายังไม่มีให้เซ็ตเป็น 1
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
    }
    // เด้งกลับมาหน้า cart เพื่อดูผลลัพธ์
    header('Location: cart.php');
    exit;
}

// --- ระบบลบสินค้าออกจากตะกร้า ---
if ($action === 'remove') {
    $remove_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($remove_id && isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header('Location: cart.php');
    exit;
}

// --- ระบบเคลียร์ตะกร้าทั้งหมด ---
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

// --- ดึงข้อมูลสินค้าที่อยู่ในตะกร้ามาแสดง ---
$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    // ดึงคีย์ (ID สินค้า) ทั้งหมดจาก session
    $product_ids = array_keys($_SESSION['cart']);
    
    // สร้างเครื่องหมาย ? ตามจำนวนสินค้า เช่น (?, ?, ?) เพื่อใช้ในคำสั่ง SQL IN
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $stmt = $pdo->prepare("SELECT id, productname, price, img FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll();

    // คำนวณราคาและรวมข้อมูล
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $totalPrice += $subtotal;
        
        $p['qty'] = $qty;
        $p['subtotal'] = $subtotal;
        $cartItems[] = $p;
    }
}

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Cart - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        /* เปลี่ยนสี Accent เป็นสีชมพูบานเย็น */
        :root { --bg-color: #111111; --nav-bg: #1a1a1a; --accent: #ff00e6; --text-main: #ffffff; --text-muted: #a0a0a0; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        
        .navbar { background-color: var(--nav-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; }
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 28px; color: #fff; text-decoration: none; letter-spacing: 1px; }
        
        .container { max-width: 1000px; margin: 50px auto; padding: 0 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .page-header h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; margin: 0; font-size: 32px; color: var(--accent); }
        .back-link { color: #fff; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .back-link:hover { color: var(--accent); }

        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table th { text-align: left; padding: 15px; border-bottom: 1px solid #444; color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .cart-table td { padding: 20px 15px; border-bottom: 1px solid #222; vertical-align: middle; }
        
        .product-col { display: flex; align-items: center; gap: 20px; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; background: #fff; }
        .product-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; margin: 0; }
        
        .price-col { font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 15px; }
        .qty-col { font-family: 'Montserrat', sans-serif; font-weight: 700; }
        .subtotal-col { font-family: 'Montserrat', sans-serif; font-weight: 900; color: var(--accent); font-size: 16px; }
        
        .btn-remove { color: #ff4d4d; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .btn-remove:hover { color: #ff1a1a; text-decoration: underline; }

        .cart-summary { background: #1a1a1a; padding: 30px; border-radius: 8px; text-align: right; border: 1px solid #333; }
        .summary-text { color: var(--text-muted); font-size: 14px; margin-bottom: 10px; }
        .total-price { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 36px; color: var(--accent); margin-bottom: 20px; }
        
        .btn-checkout { background: var(--accent); color: #111; border: none; padding: 15px 40px; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 16px; border-radius: 4px; cursor: pointer; transition: 0.3s; text-transform: uppercase; }
        .btn-checkout:hover { background: #fff; box-shadow: 0 5px 15px rgba(255,0,230,0.3); transform: translateY(-2px); }
        .btn-clear { background: transparent; color: var(--text-muted); border: 1px solid #444; padding: 10px 20px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; margin-right: 15px; transition: 0.3s; }
        .btn-clear:hover { color: #fff; border-color: #fff; }
        
        .empty-cart { text-align: center; padding: 80px 0; color: #666; font-size: 18px; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
</div>

<div class="container">
    <div class="page-header">
        <h1>YOUR CART</h1>
        <a href="list.php" class="back-link">CONTINUE SHOPPING ❯</a>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <div style="font-size: 48px; margin-bottom: 15px;">🛒</div>
            ตะกร้าสินค้าของคุณยังว่างเปล่า<br><br>
            <a href="list.php" style="color: var(--accent); text-decoration: none;">ไปเลือกแผ่นเสียงกันเถอะ!</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td>
                        <div class="product-col">
                            <?php if (!empty($item['img'])): ?>
                                <img src="<?= e($item['img']) ?>" class="product-img" alt="cover">
                            <?php else: ?>
                                <div class="product-img" style="display:flex; align-items:center; justify-content:center; color:#ccc; font-size:10px; font-weight:bold;">NO COVER</div>
                            <?php endif; ?>
                            <p class="product-name"><?= e($item['productname']) ?></p>
                        </div>
                    </td>
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
            
            <a href="cart.php?action=clear" class="btn-clear" onclick="return confirm('ต้องการล้างตะกร้าทั้งหมดใช่หรือไม่?')">CLEAR CART</a>
            <button class="btn-checkout" onclick="alert('Proceeding to checkout! (Demo)')">CHECKOUT SECURELY</button>
        </div>
    <?php endif; ?>
</div>

</body>
</html>