<?php
// connect.php
$servername = "localhost"; //  '119.59.102.161' คือ server ของอาจารย์
$username = "std6630202813"; // ใส่ Username ของตัวเอง
$password = "bai2U&qe@1"; 
$dbname = "it_std6630202813";

try {
    // สร้างการเชื่อมต่อ PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // ตั้งค่า Error Mode ให้แจ้งเตือนแบบ Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ตั้งค่าการดึงข้อมูลเป็นแบบ Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>