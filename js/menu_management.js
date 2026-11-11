// js/manage_menu.js
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

let currentCategory = '';
let knownProducts = {};
let categoriesList = [];
let targetDeleteId = null;
let deleteModalInstance = null;

async function api(url, opts = {}) {
    const isForm = opts.body instanceof FormData;
    const headers = isForm ? {} : { 'Content-Type': 'application/json' };
    const res = await fetch(url, { headers, ...opts });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
}

// Compact Live Image URL Validation & Preview
function updateImagePreview(url) {
    const container = document.getElementById('image-preview-container');
    const img = document.getElementById('image-preview');
    const errorMsg = document.getElementById('image-preview-error');
    
    url = url ? url.trim() : '';

    if (!url) {
        container.classList.add('d-none');
        container.classList.remove('d-flex');
        img.src = '';
        errorMsg.classList.add('d-none');
        return;
    }

    const tempImg = new Image();
    tempImg.onload = function() {
        img.src = url;
        img.classList.remove('d-none');
        errorMsg.classList.add('d-none');
        container.classList.remove('d-none');
        container.classList.add('d-flex');
    };
    tempImg.onerror = function() {
        img.classList.add('d-none');
        errorMsg.classList.remove('d-none');
        container.classList.remove('d-none');
        container.classList.add('d-flex');
    };
    tempImg.src = url;
}

async function loadProducts() {
    const grid = document.getElementById('product-grid');
    try {
        const query = currentCategory ? '?category=' + encodeURIComponent(currentCategory) : '';
        const data = await api('api/products.php' + query);
        
        knownProducts = {};
        data.products.forEach(p => knownProducts[p.id] = p);
        categoriesList = data.categories || [];

        // Render Filter Buttons
        const catContainer = document.getElementById('category-filters');
        catContainer.innerHTML = `<button class="btn btn-sm rounded-pill px-3 ${currentCategory === '' ? 'btn-dark fw-bold' : 'btn-outline-dark'}" onclick="filterCategory('')">All</button>` +
            categoriesList.map(c => `
                <button class="btn btn-sm rounded-pill px-3 ${currentCategory === c.name ? 'btn-dark fw-bold' : 'btn-outline-dark'}" onclick="filterCategory('${esc(c.name)}')">
                    ${esc(c.name)}
                </button>
            `).join('');

        // Populate Form Category Select Options
        const typeSelect = document.getElementById('prod-type');
        const selectedValue = typeSelect.value;
        typeSelect.innerHTML = '<option value="" disabled selected>Select Category</option>' +
            categoriesList.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
        if (selectedValue) typeSelect.value = selectedValue;

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
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="fw-bold text-primary fs-6">${rp(p.price)}</span>
                                                <span class="badge ${p.status === 'available' ? 'bg-outline-success-subtle text-success border border-success-subtle' : 'bg-outline-danger-subtle text-danger border border-danger-subtle'} small">
                                                    ${esc(p.status || 'available')}
                                                </span>
                                            </div>
                                            <div class="d-flex gap-2 mt-auto">
                                                <button class="btn btn-warning btn-sm rounded-3 fw-semibold flex-fill text-dark" onclick="startEdit(${p.id})">
                                                    Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm rounded-3 fw-semibold flex-fill" onclick="openDeleteModal(${p.id})">
                                                    Delete
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
    } catch (err) {
        grid.innerHTML = `<div class="alert alert-danger rounded-3 w-100">Failed to load menu: ${esc(err.message)}</div>`;
    }
}

function filterCategory(cat) {
    currentCategory = cat;
    loadProducts();
}

// Populate form for Editing
function startEdit(id) {
    const p = knownProducts[id];
    if (!p) return;

    document.getElementById('product-id').value = p.id;
    document.getElementById('prod-name').value = p.name;
    document.getElementById('prod-price').value = p.price;
    document.getElementById('prod-status').value = p.status || 'available';
    document.getElementById('prod-desc').value = p.description || '';
    
    const imageUrl = p.image || '';
    document.getElementById('prod-image').value = imageUrl;
    updateImagePreview(imageUrl);

    const matchedCategory = categoriesList.find(c => c.name === p.category);
    if (matchedCategory) {
        document.getElementById('prod-type').value = matchedCategory.id;
    }

    document.getElementById('form-card-title').textContent = 'Edit Menu';
    document.getElementById('submit-btn').textContent = 'Update Menu';
    document.getElementById('submit-btn').className = 'btn btn-warning w-100 py-2 rounded-3 fw-bold text-dark';
    document.getElementById('btn-cancel-edit').style.display = 'inline-block';
    document.getElementById('form-msg').innerHTML = '';

    document.getElementById('menuForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Reset Form to Create Mode
function resetForm() {
    document.getElementById('menuForm').reset();
    document.getElementById('product-id').value = '';
    document.getElementById('form-card-title').textContent = 'Add New Menu';
    document.getElementById('submit-btn').textContent = '+ Add Menu';
    document.getElementById('submit-btn').className = 'btn btn-primary w-100 py-2 rounded-3 fw-bold';
    document.getElementById('btn-cancel-edit').style.display = 'none';
    document.getElementById('form-msg').innerHTML = '';
    updateImagePreview('');
}

// Form Submit Handler
document.getElementById('menuForm').onsubmit = async function (e) {
    e.preventDefault();
    const msgBox = document.getElementById('form-msg');
    const productId = document.getElementById('product-id').value;
    const formData = new FormData(this);

    const payload = {
        name: formData.get('name'),
        type_id: parseInt(formData.get('type_id')),
        price: parseInt(formData.get('price')),
        status: formData.get('status'),
        description: formData.get('description'),
        image: formData.get('image')
    };

    try {
        if (productId) {
            // Update Menu Item
            await api(`api/products.php?id=${productId}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            msgBox.innerHTML = '<div class="alert alert-success py-2 rounded-3 small border-0 mb-0">Menu updated successfully!</div>';
        } else {
            // Create New Menu Item
            await api('api/products.php', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            msgBox.innerHTML = '<div class="alert alert-success py-2 rounded-3 small border-0 mb-0">Menu added successfully!</div>';
        }

        resetForm();
        await loadProducts();
    } catch (err) {
        msgBox.innerHTML = `<div class="alert alert-danger py-2 rounded-3 small border-0 mb-0">${esc(err.message)}</div>`;
    }
};

// Delete Confirmation Modal Logic
function openDeleteModal(id) {
    const p = knownProducts[id];
    if (!p) return;

    targetDeleteId = id;
    document.getElementById('delete-product-name').textContent = p.name;
    
    if (!deleteModalInstance) {
        deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    }
    deleteModalInstance.show();
}

document.getElementById('btn-confirm-delete').onclick = async function () {
    if (!targetDeleteId) return;

    try {
        await api(`api/products.php?id=${targetDeleteId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: 'unavailable', price: knownProducts[targetDeleteId].price })
        });

        if (deleteModalInstance) deleteModalInstance.hide();
        targetDeleteId = null;
        await loadProducts();
    } catch (err) {
        alert('Failed to delete item: ' + err.message);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});