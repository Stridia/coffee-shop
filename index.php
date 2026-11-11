<?php
// index.php
$currPage = 'home';
include 'config.php';
include 'helpers.php';
include 'header.php';
include 'navbar.php';

// Fetch categories dynamically from the types table
$stmt = $pdo->query("SELECT id, name FROM types ORDER BY id");
$categories = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="bg-blur-overlay"></div>

<div class="container my-5 text-center py-4">
    <h1 class="display-5 fw-bold text-black mb-3">Welcome to Fulvian Cafe</h1>
    <p class="lead text-muted mx-auto" style="max-width: 600px;">
        Discover our artisanal coffee blends, refreshing non-coffee beverages, and freshly toasted bites.
    </p>
</div>

<!-- Dynamic Category Buttons Section -->
<div class="container mb-5">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-black">Explore Our Menu</h4>
        <p class="text-muted small">Select a category to jump straight to your favorite items</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Default All Menu Card -->
        <div class="col-6 col-md-3">
            <a href="menu.php" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 category-card">
                    <div class="icon-wrapper mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px;">
                        <img src="uploads/Default.png" alt="All Menu" style="width: 80px; height: 80px; object-fit: contain;">
                    </div>
                    <h6 class="fw-bold text-black mb-1">All Menu</h6>
                    <span class="text-muted small">View full catalog</span>
                </div>
            </a>
        </div>

        <!-- Dynamic Category Cards from DB -->
        <?php foreach ($categories as $cat): ?>
            <?php
            $catName = htmlspecialchars($cat['name']);
            $iconPath = "uploads/" . $cat['name'] . ".png";
            
            // Fallback to Default.png if matching icon doesn't exist on disk
            if (!file_exists(__DIR__ . '/' . $iconPath)) {
                $iconPath = "uploads/Default.png";
            }
            ?>
            <div class="col-6 col-md-3">
                <a href="menu.php?category=<?= urlencode($cat['name']) ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 category-card">
                        <div class="icon-wrapper mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px;">
                            <img src="<?= $iconPath ?>" alt="<?= $catName ?>" style="width: 80px; height: 80px; object-fit: contain;">
                        </div>
                        <h6 class="fw-bold text-black mb-1"><?= $catName ?></h6>
                        <span class="text-muted small">Explore <?= $catName ?></span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>