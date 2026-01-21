<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>ลงทะเบียน</h1>
        <form action="result.php" method="POST">
            <div class="form-group">
                <label>รหัส (ID)</label>
                <input type="text" name="id" placeholder="กรอกรหัส" required>
            </div>
            <div class="form-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" name="name" placeholder="กรอกชื่อ-นามสกุล" required>
            </div>
            <div class="form-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="tel" name="phone" placeholder="กรอกเบอร์โทรศัพท์" required>
            </div>
            <div class="form-group">
                <label>อีเมล</label>
                <input type="email" name="email" placeholder="กรอกอีเมล" required>
            </div>
            <button type="submit">ส่งข้อมูล</button>
        </form>
    </div>
</body>
</html>