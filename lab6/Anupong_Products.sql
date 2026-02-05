-- สร้างตารางใหม่สำหรับน้ำผลไม้
CREATE TABLE `Juice_Products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL, -- ชื่อน้ำผลไม้
  `description` text NOT NULL,         -- สรรพคุณ/รายละเอียด
  `price` decimal(10,2) NOT NULL,      -- ราคา
  `category` varchar(100) DEFAULT NULL, -- หมวดหมู่ เช่น Cold-Pressed, Smoothie
  `image_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;