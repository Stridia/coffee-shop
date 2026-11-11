<?php
$isAdmin = str_contains($_SERVER['PHP_SELF'], '/admin/');
$pathPrefix = $isAdmin ? '../' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopipiro Coffee Shop</title>
    <link rel="icon" type="image/x-icon" href="uploads/Default.png">
    <link href="<?= $pathPrefix ?>style/bootstrap.min.css" rel="stylesheet" >
    <link href="<?= $pathPrefix ?>style/override.css" rel="stylesheet">
<body class="bg-light">
<div class="bg-blur-overlay"></div>