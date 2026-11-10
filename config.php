<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=coffee_shop;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
        ]
    );

    // MySQLi connection retained for legacy scripts
    $conn = mysqli_connect("localhost", "root", "", "coffee_shop");
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

const DISCOUNT_RATE = 0.10; // 10% discount for customer role
const MIDTRANS_SERVER_KEY = 'PASTE-YOUR-SANDBOX-SERVER-KEY';