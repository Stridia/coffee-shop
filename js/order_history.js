// js/order_history.js
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

async function api(url, opts = {}) {
    const res = await fetch(url, { headers: { 'Content-Type': 'application/json' }, ...opts });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
}

async function loadOrderHistory() {
    const listContainer = document.getElementById('order-history-list');
    const emptyContainer = document.getElementById('empty-history');

    try {
        const orders = await api('api/orders.php');

        if (!orders || orders.length === 0) {
            listContainer.style.display = 'none';
            emptyContainer.style.display = 'block';
            return;
        }

        emptyContainer.style.display = 'none';
        listContainer.style.display = 'flex';

        listContainer.innerHTML = orders.map(o => {
            const statusBadgeClass = {
                'pending': 'bg-warning-subtle text-warning border-warning-subtle',
                'paid': 'bg-info-subtle text-info border-info-subtle',
                'done': 'bg-success-subtle text-success border-success-subtle'
            }[o.status] || 'bg-secondary-subtle text-secondary';

            const formattedDate = o.order_date ? new Date(o.order_date).toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short'
            }) : '-';

            return `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">${esc(o.order_code)}</span>
                                <small class="text-muted" style="font-size: 0.78rem;">${formattedDate}</small>
                            </div>
                            <span class="badge ${statusBadgeClass} border text-capitalize px-3 py-2 rounded-pill small">
                                ${esc(o.status)}
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3 flex-grow-1">
                                <small class="text-muted fw-semibold d-block mb-1">Items Ordered:</small>
                                <p class="text-dark small mb-0" style="line-height: 1.5;">
                                    ${esc(o.items || 'No item details')}
                                </p>
                            </div>

                            <div class="border-top pt-3 mt-auto">
                                <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                    <span>Subtotal:</span>
                                    <span>${rp(o.subtotal || o.total)}</span>
                                </div>
                                ${o.discount > 0 ? `
                                <div class="d-flex justify-content-between align-items-center small text-success mb-1">
                                    <span>Member Discount:</span>
                                    <span>-${rp(o.discount)}</span>
                                </div>` : ''}
                                <div class="d-flex justify-content-between align-items-center fw-bold fs-6 text-black mt-2">
                                    <span>Total Paid:</span>
                                    <span class="text-primary">${rp(o.total)}</span>
                                </div>

                                ${o.status === 'pending' ? `
                                <button class="btn btn-success btn-sm w-100 mt-3 rounded-3 fw-bold py-2" onclick="payOnline('${esc(o.order_code)}')">
                                    Pay Online (Sandbox)
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (err) {
        listContainer.innerHTML = `<div class="alert alert-danger rounded-3 w-100">${esc(err.message)}</div>`;
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

document.addEventListener('DOMContentLoaded', loadOrderHistory);