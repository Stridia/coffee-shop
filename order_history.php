<?php
// order_history.php
$currPage = 'order_history';
include 'config.php';
include 'helpers.php';

$user = need_role(['customer', 'cashier', 'owner']);

include 'header.php';
include 'navbar.php';
?>

<div class="bg-blur-overlay"></div>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-black mb-1">Order History</h2>
            <p class="text-muted small mb-0">Track and view your past orders and receipts</p>
        </div>
        <button class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold" onclick="loadOrderHistory()">
            Refresh History
        </button>
    </div>
    <hr class="mt-0 mb-4 text-secondary opacity-25">

    <!-- Empty State Container -->
    <div id="empty-history" class="text-center py-5 my-4" style="display: none;">
        <p class="fs-5 text-black">
            You haven't ordered anything yet. Try ordering <a href="menu.php" class="text-primary fw-bold text-decoration-none">in the menu</a>!
        </p>
    </div>

    <!-- Orders Cards List Container -->
    <div id="order-history-list" class="row g-4">
        <!-- Order cards dynamically loaded here -->
    </div>
</div>

<script src="js/order_history.js"></script>

<?php include 'footer.php'; ?>