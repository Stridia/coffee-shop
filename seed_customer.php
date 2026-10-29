<?php
include 'config.php';

$username = 'cust1';
$password = password_hash('customer123', PASSWORD_DEFAULT);
$role = 'customer';

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);
mysqli_stmt_execute($stmt);

echo "Admin account created. Username: cust1, Password: customer123, Delete this file now!";
?>