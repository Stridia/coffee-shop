<?php
// admin/index.php
$currPage = 'admin_home';
include '../config.php';
include '../auth.php';

require_admin();

include '../header.php';
include '../navbar.php';

$result = mysqli_query($conn, 
          "SELECT p.id, p.name, p.description, p.image, p.price, p.status, t.name AS type 
           FROM products p
           JOIN types t ON t.id = p.type_id");
?>

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

<?php include '../footer.php' ?>