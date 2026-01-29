<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "119.59.102.161";
$username = "it_std6630202813"; // ชื่อผู้ใช้ฐานข้อมูล (ตามที่ปรากฏใน phpMyAdmin ของคุณ)
$password = "bai2U&qe@1"; // *** สำคัญมาก: เปลี่ยนเป็นรหัสผ่านจริงของคุณ ***
$dbname = "it_std6630202813";    // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตั้งค่าให้รองรับภาษาไทย (UTF-8)
$conn->set_charset("utf8mb4");
?>