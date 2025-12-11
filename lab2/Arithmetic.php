<?php
// กำหนดตัวแปร
$x = 10;
$y = 3;

echo "<h3>PHP Arithmetic Operators</h3>";
echo "Initial values: x = $x, y = $y <br><br>";

// 1. Addition
$sum = $x + $y;
echo "Addition $x + $y = $sum <br>";

// 2. Subtraction
$diff = $x - $y;
echo "Subtraction $x - $y = $diff <br>";

// 3. Multiplication
$prod = $x * $y;
echo "Multiplication $x * $y = $prod <br>";

// 4. Division
$quot = $x / $y;
echo "Division $x / $y = $quot <br>";

// 5. Modulus
$mod = $x % $y;
echo "Modulus $x % $y = $mod <br>";

// 6. Exponentiation
$pow = $x ** 2;
echo "Exponentiation $x ^ 2 = $pow <br>";
?>