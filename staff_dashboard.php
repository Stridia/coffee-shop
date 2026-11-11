<?php
// staff_dashboard.php
$currPage = 'staff_dashboard';
include 'config.php';
include 'helpers.php';

$user = need_role(['owner', 'cashier']);

include 'header.php';
include 'navbar.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-black mb-1">Staff & Sales Dashboard</h2>
            <p class="text-muted small mb-0">
                Logged in as <strong class="text-dark"><?= htmlspecialchars($user['username']) ?></strong> 
                <span class="badge bg-primary ms-1"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
            </p>
        </div>
        
        <?php if ($user['role'] === 'owner'): ?>
        <div>
            <a href="api/export.php" class="btn btn-success rounded-3 fw-bold px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Orders (Excel)
            </a>
        </div>
        <?php endif; ?>
    </div>

    <hr class="mt-0 mb-4 text-secondary opacity-25">

    <!-- Owner Sales Analytics Section (Visible to Owner only) -->
    <?php if ($user['role'] === 'owner'): ?>
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <h5 class="fw-bold text-black mb-0 fs-6">Daily Revenue Trend</h5>
                </div>
                <div class="card-body p-3">
                    <canvas id="dailyRevenueChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0 rounded-top-4">
                    <h5 class="fw-bold text-black mb-0 fs-6">Top Selling Items</h5>
                </div>
                <div class="card-body p-3 d-flex align-items-center justify-content-center">
                    <canvas id="topProductsChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Orders Management Section -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 id="orders-section-title" class="mb-0 fw-bold fs-6">
                <?= $user['role'] === 'owner' ? 'All Orders Management' : 'Assigned Cashier Orders' ?>
            </h5>
            <button class="btn btn-sm btn-light rounded-pill px-3" onclick="loadOrders()">
                Refresh Orders
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order Code</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items Summary</th>
                            <th>Total</th>
                            <th>Cashier</th>
                            <th class="pe-4 text-end">Order Status</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Loading orders...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="js/staff_dashboard.js"></script>

<?php include 'footer.php'; ?>