<?php
require_once 'connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productname = trim($_POST['productname'] ?? ''); 
    $detail = trim($_POST['detail'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $img_url = trim($_POST['img_url'] ?? '');
    
    $errors = [];

    if (empty($productname)) $errors[] = "Product name is required.";
    if (empty($detail)) $errors[] = "Product detail is required.";
    if ($price <= 0) $errors[] = "Price must be greater than 0.";

    $img_path = $img_url; 
    
    if (isset($_FILES['img_upload']) && $_FILES['img_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = uniqid('product_', true) . '.' . pathinfo($_FILES['img_upload']['name'], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['img_upload']['tmp_name'], $targetPath)) {
            $img_path = $targetPath;
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (productname, detail, price, img) VALUES (?, ?, ?, ?)");
            $stmt->execute([$productname, $detail, $price, $img_path]);
            echo "เพิ่มสินค้าสำเร็จ!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        foreach ($errors as $error) { echo "<p style='color:red;'>$error</p>"; }
    }
}
?>