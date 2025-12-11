<?php
// กำหนดตัวแปร (ตามชื่อที่ขอมา)
$tod = true;     // จริง
$taohua = false; // เท็จ
$tobi = true;    // จริง

echo "<h3>Logical Operators</h3>";

// แสดงค่าตัวแปรเริ่มต้น
echo "tod=true , taohua=false , tobi=true <br><br>";

// 1. And (และ)
// tod(จริง) เจอ taohua(เท็จ) -> ผลลัพธ์ต้องเป็น เท็จ
echo "And (tod && taohua):";
var_dump($tod && $taohua);
echo "<br><br>";

// 2. Or (หรือ)
// tod(จริง) หรือ taohua(เท็จ) -> มีจริง 1 ตัว ผลลัพธ์เลยเป็น จริง
echo "Or (tod || taohua):";
var_dump($tod || $taohua);
echo "<br><br>";

// 3. Xor (Exclusive Or)
// tod(จริง) xor tobi(จริง) -> ค่าเหมือนกันทั้งคู่ ผลลัพธ์เลยเป็น เท็จ
echo "Xor (tod xor tobi):";
var_dump($tod xor $tobi);
echo "<br><br>";

// 4. Not (นิเสธ)
// taohua เป็นเท็จ ใส่ ! (Not) เข้าไป -> กลับค่าเป็น จริง
echo "Not (!taohua):";
var_dump(!$taohua);
echo "<br><br>";
?>