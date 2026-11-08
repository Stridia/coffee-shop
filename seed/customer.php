<?php
include '../config.php';

$username = 'cust1';
$password = password_hash('customer123', PASSWORD_DEFAULT);
$role_id = 3;
$email = '';

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role_id, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $role_id, $email);
mysqli_stmt_execute($stmt);

echo "Customer account created. Username: cust1, Password: customer123, Delete this file now!";
?>