<?php
require_once 'connect.php'; 
// ดึงข้อมูลเรียงจากใหม่ไปเก่า
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// ส่วนประมวลผลการลบข้อมูล
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // ดึงชื่อไฟล์รูปภาพมาลบออกจากโฟลเดอร์ uploads ด้วย
    $stmt_img = $pdo->prepare("SELECT img FROM products WHERE id = ?");
    $stmt_img->execute([$id]);
    $row = $stmt_img->fetch();
    if ($row && file_exists($row['img'])) {
        unlink($row['img']);
    }

    $stmt_del = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt_del->execute([$id]); // ใช้ -> ถูกต้องแล้วครับ
    header("Location: dashboard.php"); // Refresh หน้า
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Healthy Drinks</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-card">
        <h2>🍹 รายการเครื่องดื่มสุขภาพ 🍏</h2>
        
        <div class="action-bar">
            <a href="index.php" class="btn-add">+ เพิ่มเมนูใหม่</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row): ?>
                <tr>
                    <td>
                        <div class="img-frame">
                            <img src="<?= htmlspecialchars($row['img']) ?>" alt="product">
                        </div>
                    </td>
                    <td style="text-align: left;">
                        <strong><?= htmlspecialchars($row['productname']) ?></strong>
                        <p style="font-size: 12px; color: #b2bec3; margin: 5px 0 0;"><?= htmlspecialchars($row['detail']) ?></p>
                    </td>
                    <td style="font-weight: bold; color: #2d6a4f;">
                        <?= number_format($row['price'], 2) ?> ฿
                    </td>
                    <td>
                        <a href="?delete_id=<?= $row['id'] ?>" 
                           class="btn-delete" 
                           onclick="return confirm('ยืนยันการลบเมนูนี้ใช่หรือไม่?')">ลบ</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>