<?php
// manage_menu.php
include 'config.php';
include 'auth.php';

require_admin();

include 'navbar.php';

$query = "SELECT * FROM menu";

$result = mysqli_query($conn, "$query");
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
        <link href="style.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">PENGELOLAAN MENU CSK</h4>
                    <a href="create.php" class="btn btn-success btn-sm">+ Add Menu</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover
                        align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="img-col text-center">Image</th>
                                    <th class="text-center">Type</th>
                                    <th class="desc-col text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="img-col text-center">
                                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="htmlspecialchars($row['name'])" 
                                        class="img-menu"/>
                                        <br/><br/>
                                        <a href="" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($row['type']) ?></td>
                                    <td class="desc-col">
                                        <b><?= htmlspecialchars($row['name']) ?></b>
                                        <br/>
                                        <?= htmlspecialchars($row['description']) ?>
                                        <br/><br/>
                                        
                                        <?php $price = $row['price']; 
                                        if (is_customer()) {
                                            echo "<s>$price <br></s>";
                                            $price = $price * 0.9;
                                        }
                                        echo "<b>$price</b>";
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>