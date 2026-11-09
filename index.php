<?php
// index.php
$currPage = 'home';
include 'config.php';
include 'auth.php';
include 'header.php';
include 'navbar.php';
?>

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
            <h5>
                <?php 
                    if (is_signin()) {
                        $username = get_username();
                        echo "<i>Welcome back,</i> $username!";
                    } else echo "<i>Good evening, dear customer!</i>";
                ?>
            </h5>
            <h1 class="mb-0">Fulvian Cafe</h1><br>
            <a href="menu.php" class="btn btn-info btn-sm">Menu</a>
            <?php if (is_admin()): ?>
            <a href="admin/index.php" class="btn btn-info btn-sm">Manage Menu</a>
            <?php endif;?>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>