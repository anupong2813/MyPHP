-- สร้างตารางสำหรับเก็บข้อมูลแผ่นเสียง Vinyl
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `productname` varchar(255) NOT NULL, -- ชื่อศิลปิน และ ชื่ออัลบั้ม
  `detail` text NOT NULL,              -- แนวเพลง (Pop, K-Pop, Disco) และรายละเอียดแผ่น
  `price` decimal(10,2) NOT NULL,      -- ราคาแผ่นเสียง
  `img` varchar(500) DEFAULT NULL,     -- ที่อยู่ไฟล์รูปภาพหน้าปก
  `color` varchar(100) DEFAULT NULL,    -- สีของแผ่นเสียง (เช่น สีดำ, สีขาว, สีแดง)
  `format` varchar(100) DEFAULT NULL,   -- รูปแบบของแผ่นเสียง (เช่น 7 นิ้ว, 12 นิ้ว)
  `released_date` varchar(100) DEFAULT NULL, -- วันที่วางจำหน่าย
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;