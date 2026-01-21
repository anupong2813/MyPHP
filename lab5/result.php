<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>-ข้อมูลของคุณ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>ข้อมูลที่บันทึก</h1>

        <?php
        $id = htmlspecialchars($_POST['id'] ?? '');
        $name = htmlspecialchars($_POST['name'] ?? '');
        $phone = htmlspecialchars($_POST['phone'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        ?>

        <div class="info-item">
            <div class="label">รหัส (ID)</div>
            <div class="value"><?php echo $id; ?></div>
        </div>

        <div class="info-item">
            <div class="label">ชื่อ-นามสกุล</div>
            <div class="value"><?php echo $name; ?></div>
        </div>

        <div class="info-item">
            <div class="label">เบอร์โทรศัพท์</div>
            <div class="value"><?php echo $phone; ?></div>
        </div>

        <div class="info-item">
            <div class="label">อีเมล</div>
            <div class="value"><?php echo $email; ?></div>
        </div>

        <a href="index.php" class="btn">กลับไปหน้าฟอร์ม</a>
    </div>
</body>
</html>