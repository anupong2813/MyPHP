<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มเมนูเครื่องดื่มสุขภาพ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>🍊 เพิ่มเมนูเครื่องดื่มสุขภาพ 🍏</h2>
        
        <form method="POST" action="create.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productname">Product Name *</label>
                <input type="text" id="productname" name="productname" class="form-control" placeholder="ชื่อเมนู เช่น สมูตตี้เบอร์รี่" required>
            </div>

            <div class="form-group">
                <label for="detail">Product Detail *</label>
                <textarea id="detail" name="detail" class="form-control" rows="3" placeholder="รายละเอียดวัตถุดิบ..." required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label for="img_upload">📸 Upload Image</label>
                <input type="file" id="img_upload" name="img_upload" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn-primary">Create Product</button>
            <a href="dashboard.php" class="btn-outline">View All Menus</a>
        </form>
    </div>
</body>
</html>