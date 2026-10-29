<?php
// index.php
include 'config.php';
include 'auth.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Coffee Shop Owner</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <?php
                if (isset($_GET['status'])) {
                    echo "
                    <div class='alert alert-success' role='alert'>
                        Pesanan diterima dengan harga $harga!
                    </div>";
                } 
            ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="mb-0">Coffee Shop Keputih</h1><br>
                    <p> User: 
                        <?php 
                            if (is_customer()) echo get_username();
                            else echo "Anonymous";
                        ?>
                    </p>
                    <a href="menu.php" class="btn btn-info btn-sm">Menu</a>
                    <?php if (is_admin()): ?>
                    <a href="manage_menu.php" class="btn btn-info btn-sm">Manage Menu</a>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>