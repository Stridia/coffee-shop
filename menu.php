<?php
// menu.php
$currPage = 'menu';
include 'config.php';
include 'helpers.php';
include 'header.php';
include 'navbar.php';
?>

<div class="bg-blur-overlay"></div>

<div class="container my-4">
    <!-- Member Discount / Promo Banner -->
    <div id="promo-banner" class="alert alert-info shadow-sm mb-4" role="alert" style="display: none;"></div>

    <div class="row g-4">
        <!-- Products Column -->
        <div class="col-lg-8">
            <!-- Clean Header & Category Filter Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h2 class="fw-bold text-black mb-0">Menu Kopipiro</h2>
                <div id="category-filters" class="d-flex flex-wrap gap-2">
                    <!-- Category filter buttons loaded dynamically -->
                </div>
            </div>
            <hr class="mt-0 mb-4 text-primary opacity-75 fw-bold">

            <!-- Grouped Category Sections with Menu Item Cards -->
            <div id="product-grid">
                <!-- Dynamic Category Headings, Dividers, and Cards -->
            </div>
        </div>

        <!-- Cart & Checkout Sidebar Column -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 80px;">
                <div class="card-header bg-dark text-white py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold fs-6">Order Cart</h5>
                </div>
                <div class="card-body p-4">
                    <ul id="cart-list" class="list-group list-group-flush mb-3">
                        <li class="list-group-item text-muted text-center py-3 border-0">Your cart is empty</li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center fw-bold fs-5 mb-3">
                        <span>Total:</span>
                        <span id="cart-total" class="text-primary">Rp 0</span>
                    </div>

                    <?php if (!is_signin()): ?>
                    <div class="form-floating mb-3">
                        <input type="text" id="guestName" class="form-control rounded-3" placeholder="Your Name">
                        <label for="guestName">Customer Name (Guest)</label>
                    </div>
                    <?php endif; ?>

                    <button id="btnCheckout" class="btn btn-primary w-100 py-2 rounded-3 fw-bold" onclick="handleCheckout()">Place Order</button>
                    
                    <div id="checkout-result"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/menu.js"></script>

<?php include 'footer.php'; ?>