<?php
session_start();
require __DIR__ . '/core/config.php';
require __DIR__ . '/core/csrf.php';

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$checkout_items = [];
$totalPrice = 0;
$main_img = '';
$order_details = [];

// เช็คว่ามาจากการกด Buy Now หรือมาจาก Cart
if (isset($_GET['buy_now'])) {
    $id = filter_input(INPUT_GET, 'buy_now', FILTER_VALIDATE_INT);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if ($p) {
        $checkout_items[] = ['name' => $p['productname'], 'qty' => 1, 'price' => $p['price'], 'subtotal' => $p['price'], 'img' => $p['img']];
        $totalPrice = $p['price'];
        $main_img = $p['img'];
        $order_details[] = $p['productname'] . " (x1)";
    }
} elseif (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, productname, price, img FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll();
    foreach ($products as $i => $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $totalPrice += $subtotal;
        $checkout_items[] = ['name' => $p['productname'], 'qty' => $qty, 'price' => $p['price'], 'subtotal' => $subtotal, 'img' => $p['img']];
        $order_details[] = $p['productname'] . " (x" . $qty . ")";
        if ($i === 0) $main_img = $p['img']; // เอารูปแรกเป็นรูปหลักของออเดอร์
    }
}

if (empty($checkout_items)) { header("Location: views/list.php"); exit; }

// 🟢 ส่วนที่แก้ไข: เก็บข้อมูลไว้ส่งไปหน้า Process (เพิ่ม buy_now_id แล้ว)
$_SESSION['checkout_data'] = [
    'total_price' => $totalPrice,
    'order_details' => implode(", ", $order_details),
    'main_img' => $main_img,
    'buy_now_id' => isset($_GET['buy_now']) ? filter_input(INPUT_GET, 'buy_now', FILTER_VALIDATE_INT) : null
];

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function getDisplayImg($img) {
    if (empty($img)) return ''; if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}

// 🟢 ระบุพาร์ทไฟล์รูป QR Code ของคุณที่นำไปใส่ในโฟลเดอร์ storage 
$qr_url = "storage/my_qrcode.jpg"; 
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Checkout - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 40px; transition: 0.3s; }
        .checkout-container { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; margin-top: 0; font-size: 32px; color: var(--text-main); text-transform: uppercase;}
        .box { background: var(--card); padding: 30px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 10px 30px var(--shadow); }
        
        .item-row { display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding: 15px 0; font-size: 14px;}
        .item-row:last-child { border-bottom: none; }
        .total-row { display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 24px; padding-top: 20px; border-top: 2px solid var(--text-main); margin-top: 20px;}
        
        .qr-box { text-align: center; margin-bottom: 25px; padding: 20px; background: #ffffff; border-radius: 12px; }
        .qr-box img { max-width: 320px; width: 100%; border: 1px solid #e5e5ea; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .qr-text { color: #1d1d1f; font-family: 'Montserrat', sans-serif; font-weight: 800; margin-top: 15px; font-size: 18px;}
        
        label { display: block; margin-bottom: 8px; font-size: 12px; color: var(--text-muted); font-family: 'Montserrat', sans-serif; font-weight: 700; text-transform: uppercase;}
        input[type="text"] { width: 100%; padding: 14px; margin-bottom: 15px; background: var(--input-bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-family: 'Kanit', sans-serif; font-size: 14px; box-sizing: border-box; }
        input[type="file"] { width: 100%; padding: 10px; color: var(--text-muted); border: 1px dashed var(--border); background: transparent; margin-bottom: 20px; box-sizing: border-box;}
        input[type="file"]::file-selector-button { background: var(--text-main); color: var(--bg); border: none; padding: 8px 16px; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 15px; }
        
        .input-group { display: flex; gap: 15px; }
        .input-group > div { flex: 1; }

        .btn-submit { width: 100%; padding: 16px; background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; border-radius: 25px; font-size: 15px; font-weight: 900; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: 0.3s; text-transform: uppercase; margin-top: 10px;}
        .btn-submit:hover { background: #333333 !important; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--text-muted); text-decoration: none; font-weight: 700; transition: 0.3s; }
        .back-link:hover { color: var(--text-main); }
        .section-title { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 16px; margin: 25px 0 15px 0; padding-bottom: 10px; border-bottom: 1px solid var(--border); color: var(--text-main); }
        
        @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; } .input-group { flex-direction: column; gap: 0; } }
    </style>
</head>
<body>
    <a href="views/list.php" class="back-link">❮ BACK TO SHOP</a>
    <div class="checkout-container">
        <div class="box">
            <h1>Order Summary</h1>
            <?php foreach($checkout_items as $item): ?>
            <div class="item-row">
                <div><?= e($item['name']) ?> <strong>(x<?= $item['qty'] ?>)</strong></div>
                <div>฿<?= number_format($item['subtotal'], 2) ?></div>
            </div>
            <?php endforeach; ?>
            <div class="total-row">
                <div>TOTAL</div>
                <div>฿<?= number_format($totalPrice, 2) ?></div>
            </div>
        </div>

        <div class="box">
            <h1>Payment & Shipping</h1>
            <div class="qr-box">
                <img src="<?= htmlspecialchars($qr_url) ?>" alt="Store QR Code">
                <div class="qr-text">SCAN TO PAY</div>
                <p style="font-size: 14px; color: #ff3b30; font-weight: bold; margin: 5px 0 0 0;">ยอดโอน: ฿<?= number_format($totalPrice, 2) ?></p>
            </div>

            <form action="checkout_process.php<?= isset($_GET['buy_now']) ? '?buy_now=1' : '' ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                
                <div class="section-title">Customer Info</div>
                <label>ชื่อ-นามสกุล (Full Name)</label>
                <input type="text" name="customer_name" placeholder="ชื่อผู้รับสินค้า" required>
                
                <label>เบอร์โทรศัพท์ (Phone Number)</label>
                <input type="text" name="contact_info" placeholder="08x-xxx-xxxx" required>
                
                <div class="section-title">Shipping Address</div>
                <label>ที่อยู่ (Address)</label>
                <input type="text" name="address" placeholder="บ้านเลขที่, หมู่, ซอย, ถนน, ตึก/หมู่บ้าน" required>
                
                <div class="input-group">
                    <div>
                        <label>ตำบล / แขวง (Sub-district)</label>
                        <input type="text" name="sub_district" required>
                    </div>
                    <div>
                        <label>อำเภอ / เขต (District)</label>
                        <input type="text" name="district" required>
                    </div>
                </div>

                <div class="input-group">
                    <div>
                        <label>จังหวัด (Province)</label>
                        <input type="text" name="province" required>
                    </div>
                    <div>
                        <label>รหัสไปรษณีย์ (Zip Code)</label>
                        <input type="text" name="zipcode" required>
                    </div>
                </div>
                
                <div class="section-title">Payment Proof</div>
                <label>Upload Payment Slip (รูปสลิปโอนเงิน)</label>
                <input type="file" name="slip_upload" accept="image/*" required>
                
                <button type="submit" class="btn-submit">Confirm Order & Payment</button>
            </form>
        </div>
    </div>
</body>
</html>