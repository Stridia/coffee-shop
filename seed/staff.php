<?php
include '../config.php';

$username = 'staff1';
$password = password_hash('staff123', PASSWORD_DEFAULT);
$role_id = 2;
$email = '';

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role_id, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssis", $username, $password, $role_id, $email);
mysqli_stmt_execute($stmt);

echo "Staff account created. Username: staff1, Password: staff123, Delete this file now!";
?>