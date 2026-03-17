<?php
session_start();
require __DIR__ . '/core/config.php';
require __DIR__ . '/core/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) { die('Invalid request'); }
if (empty($_SESSION['checkout_data'])) { die('No order data found.'); }

$c_name = trim($_POST['customer_name'] ?? '');
$c_contact = trim($_POST['contact_info'] ?? '');

// 🟢 1. รับข้อมูลที่อยู่จัดส่ง
$address = trim($_POST['address'] ?? '');
$sub_district = trim($_POST['sub_district'] ?? '');
$district = trim($_POST['district'] ?? '');
$province = trim($_POST['province'] ?? '');
$zipcode = trim($_POST['zipcode'] ?? '');

$total_price = $_SESSION['checkout_data']['total_price'];
$order_details = $_SESSION['checkout_data']['order_details'];
$main_img = $_SESSION['checkout_data']['main_img'];

// เช็คข้อมูลให้ครบ
if ($c_name === '' || $c_contact === '' || $address === '' || $sub_district === '' || $district === '' || $province === '' || $zipcode === '' || empty($_FILES['slip_upload']['tmp_name'])) {
    die('ข้อมูลไม่ครบถ้วน กรุณาย้อนกลับไปกรอกใหม่');
}

// รวมที่อยู่เป็นบรรทัดเดียว
$full_shipping_address = $address . " ต." . $sub_district . " อ." . $district . " จ." . $province . " " . $zipcode;

// จัดการอัปโหลดสลิป
$slip_img = '';
$info = getimagesize($_FILES['slip_upload']['tmp_name']);
$extMap = ['image/jpeg'=>'jpg','image/png'=>'png'];
if ($info && isset($extMap[$info['mime']])) {
    $dir = __DIR__ . '/storage/slips';
    if (!is_dir($dir)) { @mkdir($dir, 0777, true); @chmod($dir, 0777); }
    
    $filename = 'slip_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $extMap[$info['mime']];
    if (@move_uploaded_file($_FILES['slip_upload']['tmp_name'], "$dir/$filename")) {
        $slip_img = "storage/slips/$filename";
    } else {
        die('ไม่สามารถอัปโหลดสลิปได้ (Permission Denied)');
    }
} else {
    die('ไฟล์สลิปต้องเป็นรูปภาพ JPG หรือ PNG เท่านั้น');
}

// 🟢 2. บันทึกลงฐานข้อมูล (เพิ่ม full_shipping_address)
$sql = "INSERT INTO orders (customer_name, contact_info, shipping_address, order_details, total_price, main_img, slip_img) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$c_name, $c_contact, $full_shipping_address, $order_details, $total_price, $main_img, $slip_img]);

// 🟢 3. ตัดสต๊อกสินค้า
if (isset($_GET['buy_now']) && isset($_SESSION['checkout_data']['buy_now_id'])) {
    $stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - 1) WHERE id = ?");
    $stmt->execute([$_SESSION['checkout_data']['buy_now_id']]);
} elseif (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $stmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
        $stmt->execute([$qty, $pid]);
    }
}

// ล้างตะกร้าสินค้า (ถ้าไม่ได้กด Buy Now)
if (!isset($_GET['buy_now'])) {
    unset($_SESSION['cart']);
}
unset($_SESSION['checkout_data']);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Order Success - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:wght@700;900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; }
        body { background: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: var(--card); padding: 50px; border-radius: 12px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; margin-bottom: 10px; font-size: 32px;}
        p { color: var(--text-muted); margin-bottom: 30px; }
        a { background: #111; color: #fff; text-decoration: none; padding: 15px 30px; border-radius: 25px; font-weight: bold; font-family: 'Montserrat', sans-serif; text-transform: uppercase;}
    </style>
</head>
<body>
    <div class="box">
        <div style="font-size: 60px; margin-bottom: 20px;">🎉</div>
        <h1>PAYMENT SUCCESS</h1>
        <p>ได้รับข้อมูลการสั่งซื้อและสลิปการโอนเงินเรียบร้อยแล้ว<br>เราจะทำการตรวจสอบและจัดส่งสินค้าให้เร็วที่สุด</p>
        <a href="views/list.php">BACK TO COLLECTION</a>
    </div>
</body>
</html>