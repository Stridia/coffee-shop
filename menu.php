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
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 class="fw-bold text-black mb-0">Menu Kopipiro</h2>
                <div id="category-filters" class="d-flex flex-wrap gap-2">
                    <!-- Category filter buttons loaded dynamically -->
                </div>
            </div>

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

<script>
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

let me = { role: 'guest' };
const urlParams = new URLSearchParams(window.location.search);
let currentCategory = urlParams.get('category') || '';
let cart = {};
let knownProducts = {};

async function api(url, opts = {}) {
    const res = await fetch(url, { headers: { 'Content-Type': 'application/json' }, ...opts });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
}

async function initMenu() {
    try {
        me = await api('api/auth.php?action=me');
        const promoBanner = document.getElementById('promo-banner');
        promoBanner.style.display = 'block';
        if (me.role === 'customer') {
            promoBanner.className = 'alert alert-success shadow-sm mb-4';
            promoBanner.innerHTML = '<strong>Member Discount Active!</strong> You receive 10% off on all items.';
        } else {
            promoBanner.className = 'alert alert-info shadow-sm mb-4';
            promoBanner.innerHTML = '<strong>Guest Mode:</strong> Sign in as a customer to unlock a 10% member discount!';
        }
    } catch (e) {
        console.warn('Session check failed', e);
    }
    await loadProducts();
}

async function loadProducts() {
    const query = currentCategory ? '?category=' + encodeURIComponent(currentCategory) : '';
    const data = await api('api/products.php' + query);
    
    knownProducts = {};
    data.products.forEach(p => knownProducts[p.id] = p);

    // Render Filter Buttons
    const catContainer = document.getElementById('category-filters');
    catContainer.innerHTML = `<button class="btn btn-sm rounded-pill px-3 ${currentCategory === '' ? 'btn-dark fw-bold' : 'btn-outline-dark'}" onclick="filterCategory('')">All</button>` +
        data.categories.map(c => `
            <button class="btn btn-sm rounded-pill px-3 ${currentCategory === c.name ? 'btn-dark fw-bold' : 'btn-outline-dark'}" onclick="filterCategory('${esc(c.name)}')">
                ${esc(c.name)}
            </button>
        `).join('');

    const grid = document.getElementById('product-grid');
    if (data.products.length === 0) {
        grid.innerHTML = '<div class="text-center text-muted py-5">No products available in this category.</div>';
        return;
    }

    // Group items by Category
    const grouped = {};
    data.products.forEach(p => {
        const catName = p.category || 'Other';
        if (!grouped[catName]) grouped[catName] = [];
        grouped[catName].push(p);
    });

    let html = '';
    for (const [catName, products] of Object.entries(grouped)) {
        html += `
            <div class="category-section mb-5">
                <h4 class="fw-bold text-black mb-1">${esc(catName)}</h4>
                <hr class="mt-1 mb-4 text-secondary opacity-25">
                <div class="row g-3">
                    ${products.map(p => {
                        const isDiscounted = p.final_price < p.price;
                        const imgPath = p.image ? esc(p.image) : 'https://via.placeholder.com/300x200?text=No+Image';
                        return `
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                    <img src="${imgPath}" class="card-img-top" style="height: 170px; object-fit: cover;" alt="${esc(p.name)}">
                                    <div class="card-body d-flex flex-column p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-black mb-0">${esc(p.name)}</h6>
                                            <span class="badge bg-secondary small">${esc(p.category)}</span>
                                        </div>
                                        <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.85rem;">
                                            ${esc(p.description || '')}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <div>
                                                ${isDiscounted ? `<span class="text-muted text-decoration-line-through small me-1">${rp(p.price)}</span>` : ''}
                                                <span class="fw-bold text-primary fs-6">${rp(p.final_price)}</span>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm rounded-3 fw-semibold px-3" onclick="addToCart(${p.id})">
                                                + Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    grid.innerHTML = html;
}

function filterCategory(cat) {
    currentCategory = cat;
    loadProducts();
}

function addToCart(id) {
    cart[id] = (cart[id] || 0) + 1;
    renderCart();
}

function removeFromCart(id) {
    if (cart[id]) {
        delete cart[id];
    }
    renderCart();
}

function renderCart() {
    const cartList = document.getElementById('cart-list');
    const entries = Object.entries(cart);
    let total = 0;

    if (entries.length === 0) {
        cartList.innerHTML = '<li class="list-group-item text-muted text-center py-4 border-0 bg-transparent">Your cart is empty</li>';
        document.getElementById('cart-total').textContent = 'Rp 0';
        return;
    }

    cartList.innerHTML = entries.map(([id, qty]) => {
        const p = knownProducts[id];
        if (!p) return '';
        const lineTotal = p.final_price * qty;
        total += lineTotal;
        return `
            <li class="list-group-item border rounded-3 p-3 mb-2 shadow-sm d-flex justify-content-between align-items-center bg-white">
                <div>
                    <div class="fw-bold text-black" style="font-size: 0.95rem;">${esc(p.name)}</div>
                    <div class="text-muted small mt-1">${qty} x ${rp(p.final_price)}</div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-2">
                    <span class="fw-bold text-primary small me-1">${rp(lineTotal)}</span>
                    <button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" 
                            style="width: 26px; height: 26px; min-width: 26px; line-height: 1; font-size: 1rem;" 
                            onclick="removeFromCart(${id})" 
                            title="Remove Item">&times;</button>
                </div>
            </li>
        `;
    }).join('');

    document.getElementById('cart-total').textContent = rp(total);
}

async function handleCheckout() {
    const items = Object.entries(cart).map(([id, qty]) => ({ product_id: +id, qty }));
    if (items.length === 0) {
        alert('Please add items to your cart before placing an order.');
        return;
    }

    const guestInput = document.getElementById('guestName');
    const customerName = guestInput ? guestInput.value : '';

    try {
        const res = await api('api/orders.php', {
            method: 'POST',
            body: JSON.stringify({ items, customer_name: customerName })
        });
        
        cart = {};
        renderCart();

        document.getElementById('checkout-result').innerHTML = `
            <div class="alert alert-success rounded-3 mt-3 mb-0 small">
                <strong>Order Placed!</strong><br>
                Code: <b>${esc(res.order_code)}</b><br>
                Subtotal: ${rp(res.subtotal)}<br>
                Discount: ${rp(res.discount)}<br>
                Total: <b>${rp(res.total)}</b><br>
                <button class="btn btn-success btn-sm w-100 mt-2 rounded-3 fw-bold" onclick="payOnline('${esc(res.order_code)}')">
                    Pay Online (Sandbox)
                </button>
            </div>
        `;
    } catch (err) {
        document.getElementById('checkout-result').innerHTML = `
            <div class="alert alert-danger rounded-3 mt-3 mb-0 small">${esc(err.message)}</div>
        `;
    }
}

async function payOnline(orderCode) {
    try {
        const res = await api('api/pay.php', {
            method: 'POST',
            body: JSON.stringify({ order_code: orderCode })
        });
        window.location.href = res.redirect_url;
    } catch (err) {
        alert('Payment initialization failed: ' + err.message);
    }
}

document.addEventListener('DOMContentLoaded', initMenu);
</script>

<?php include 'footer.php'; ?>x