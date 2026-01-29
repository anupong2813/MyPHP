<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Healthy Shop</title>
    
    <!-- เรียกใช้ฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- เชื่อมต่อไฟล์ CSS (ต้องวางไฟล์ style.css ไว้ที่เดียวกัน) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <!-- ส่วนหัว -->
        <div class="header">
            <span class="brand-icon">🍃</span>
            <h1>Add New Product</h1>
            <p>เพิ่มสินค้าเพื่อสุขภาพรายการใหม่</p>
        </div>

        <!-- ฟอร์มส่งข้อมูลไปที่ไฟล์ create.php -->
        <form method="POST" action="create.php" enctype="multipart/form-data">
            
            <!-- ชื่อสินค้า -->
            <div class="form-group">
                <label for="productname">Product Name *</label>
                <input type="text" id="productname" name="productname" 
                       class="form-control" 
                       placeholder="เช่น น้ำผักผลไม้สกัดเย็น สูตร Detox"
                       value="<?php echo isset($_POST['productname']) ? htmlspecialchars($_POST['productname']) : ''; ?>" 
                       required>
            </div>

            <!-- รายละเอียด -->
            <div class="form-group">
                <label for="detail">Product Detail *</label>
                <textarea id="detail" name="detail" class="form-control" 
                          placeholder="รายละเอียดสินค้า ส่วนผสม และสรรพคุณ..."
                          required><?php echo isset($_POST['detail']) ? htmlspecialchars($_POST['detail']) : ''; ?></textarea>
            </div>

            <!-- ราคา -->
            <div class="form-group">
                <label for="price">Price (THB) *</label>
                <input type="number" id="price" name="price" 
                       class="form-control" 
                       step="0.01" min="0.01"
                       placeholder="0.00"
                       value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" 
                       required>
            </div>

            <!-- ลิงก์รูปภาพ -->
            <div class="form-group">
                <label for="img_url">Image URL</label>
                <input type="url" id="img_url" name="img_url" 
                       class="form-control" 
                       placeholder="https://example.com/image.jpg"
                       value="<?php echo isset($_POST['img_url']) ? htmlspecialchars($_POST['img_url']) : ''; ?>">
                <small class="form-help">ใส่ลิงก์รูปภาพ หรืออัปโหลดไฟล์ด้านล่าง</small>
            </div>

            <!-- อัปโหลดรูปภาพ -->
            <div class="form-group">
                <label for="img_upload">Upload Image</label>
                <input type="file" id="img_upload" name="img_upload" 
                       class="form-control" 
                       style="padding: 9px;" 
                       accept="image/*">
                <small class="form-help">รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 5MB)</small>
            </div>

            <!-- ปุ่มกด -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Product</button>
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>