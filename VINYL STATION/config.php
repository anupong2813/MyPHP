<?php

// config.php
$DB_HOST = 'localhost';
$DB_NAME = 'it_std6630202813'; 
$DB_USER = 'std6630202813';    
$DB_PASS = 'bai2U&qe@1';       
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}