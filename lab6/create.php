<?php
// 1. เรียกใช้งานไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db.php';

// 2. ตรวจสอบว่ามีการส่งข้อมูลผ่านฟอร์ม (POST Method) หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // รับค่าจากฟอร์มตามชื่อที่ตั้งไว้ในหน้า HTML (productname, detail, price)
    $productname = $_POST['productname'];
    $detail = $_POST['detail'];
    $price = $_POST['price'];
    
    // เตรียมตัวแปรสำหรับเก็บข้อมูลรูปภาพ (img)
    $img = ""; 

    // 3. จัดการเรื่องรูปภาพ (img)
    // กรณีที่ 1: มีการอัปโหลดไฟล์รูปภาพผ่านหน้าเว็บ
    if (isset($_FILES['img_upload']) && $_FILES['img_upload']['error'] == 0) {
        $target_dir = "uploads/"; // โฟลเดอร์ที่ใช้เก็บรูป
        
        // ถ้ายังไม่มีโฟลเดอร์ uploads ให้สร้างขึ้นมาใหม่
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // ตั้งชื่อไฟล์ใหม่ (เวลาปัจจุบัน + ชื่อไฟล์เดิม) เพื่อป้องกันชื่อซ้ำ
        $filename = time() . "_" . basename($_FILES["img_upload"]["name"]);
        $target_file = $target_dir . $filename;

        // ย้ายไฟล์จากโฟลเดอร์ชั่วคราวไปยังโฟลเดอร์ uploads บน Server
        if (move_uploaded_file($_FILES["img_upload"]["tmp_name"], $target_file)) {
            $img = $target_file; // เก็บ Path ของไฟล์รูปภาพลงในตัวแปร $img
        }
    } 
    // กรณีที่ 2: ไม่ได้อัปโหลดไฟล์ แต่ใส่เป็น Image URL มาแทน
    elseif (!empty($_POST['img_url'])) {
        $img = $_POST['img_url']; // เก็บลิงก์ URL ลงในตัวแปร $img
    }

    // 4. เขียนคำสั่ง SQL เพื่อบันทึกข้อมูลลงใน 4 คอลัมน์ที่ต้องการ
    // คอลัมน์ id ไม่ต้องใส่เพราะระบบจะรันเลขให้อัตโนมัติ (AUTO_INCREMENT)
    $sql = "INSERT INTO products (productname, detail, price, img) 
            VALUES ('$productname', '$detail', '$price', '$img')";

    // 5. สั่งรันคำสั่ง SQL และตรวจสอบผลลัพธ์
    if ($conn->query($sql) === TRUE) {
        // หากบันทึกสำเร็จ ให้แสดงกล่องข้อความแจ้งเตือนและกลับไปหน้าฟอร์มเพิ่มสินค้า
        echo "<script>
                alert('บันทึกข้อมูลสินค้าเครื่องดื่มสุขภาพสำเร็จ!');
                window.location.href = 'add_product.php'; 
              </script>";
    } else {
        // หากเกิดข้อผิดพลาด ให้แสดงข้อความแจ้งเตือน Error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
} else {
    // หากพยายามเข้าไฟล์นี้โดยตรงโดยไม่ผ่านฟอร์ม ให้เด้งกลับไปหน้าเพิ่มสินค้า
    header("Location: add_product.php");
    exit();
}
?>