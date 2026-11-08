<?php
include '../config.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role_id = 1;
$email = '';

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role_id, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $role_id, $email);
mysqli_stmt_execute($stmt);

echo "Admin account created. Username: admin, Password: admin123, Delete this file now!";
?>