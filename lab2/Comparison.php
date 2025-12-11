<?php
// กำหนดตัวแปรใหม่ (New Variables)
$a = 500;
$b = "500"; // เป็น String
$c = 100;

echo "<h3>PHP Comparison Operators</h3>";
echo "a = $a , b = $b(String) , c = $c <br><br>";

// 1. Equal (เท่ากัน - ดูแค่ค่า)
echo "Equal a == b :";
var_dump($a == $b);
echo "<br><br>";

// 2. Identical (เท่ากันทุกประการ - ดูทั้งค่าและชนิดข้อมูล)
// ผลเป็น false เพราะ $a เป็น int แต่ $b เป็น string
echo "Identical a === b :";
var_dump($a === $b);
echo "<br><br>";

// 3. Not Equal (ไม่เท่ากัน - ใช้ !=)
echo "Not Equal a != c :";
var_dump($a != $c);
echo "<br><br>";

// 4. Not Equal (ไม่เท่ากัน - ใช้ <>)
echo "Not Equal a <> c :";
var_dump($a <> $c);
echo "<br><br>";

// 5. Not Identical (ไม่เท่ากันทุกประการ)
echo "Not Identical a !== b :";
var_dump($a !== $b);
echo "<br><br>";

// 6. Greater than (มากกว่า)
echo "Greater than a > c :";
var_dump($a > $c);
echo "<br><br>";

// 7. Less than (น้อยกว่า)
// สลับเอา c ขึ้นก่อน เพื่อให้ผลเป็น true (100 < 500)
echo "Less than c < a :";
var_dump($c < $a);
echo "<br><br>";

// 8. Greater Or Equal (มากกว่าหรือเท่ากับ)
echo "Greater Or Equal a >= c :";
var_dump($a >= $c);
echo "<br><br>";

// 9. Less Or Equal (น้อยกว่าหรือเท่ากับ)
// 500 น้อยกว่าหรือเท่ากับ 100 หรือไม่? -> เท็จ
echo "Less Or Equal a <= c :";
var_dump($a <= $c);
echo "<br><br>";

// 10. Spaceship (เปรียบเทียบสามทาง)
// ถ้าซ้ายมากกว่าขวา จะได้ 1 (int)
echo "Spaceship a <=> c :";
var_dump($a <=> $c);
echo "<br><br>";
?>