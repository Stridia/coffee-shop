<?php 
// auth.php
session_start();

if (!$_SESSION) {
    $_SESSION['role'] = '';
}

function is_signin() {
    return isset($_SESSION['username']);
}

function is_admin() {
    return $_SESSION['role'] === 'owner';
}

function is_customer() {
    return $_SESSION['role'] === 'customer';
}

function require_admin() {
    if (!is_admin()) {
        http_response_code(403);
        die("403 - Access denied. This page is for admins only.");
    }
}

function get_username() {
    return $_SESSION['username'];
}
?>