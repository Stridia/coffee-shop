// js/staff_dashboard.js
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

let currentUser = null;

async function api(url, opts = {}) {
    const isForm = opts.body instanceof FormData;
    const headers = isForm ? {} : { 'Content-Type': 'application/json' };
    const res = await fetch(url, { headers, ...opts });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
}

async function initDashboard() {
    try {
        currentUser = await api('api/auth.php?action=me');
        await loadOrders();

        if (currentUser.role === 'owner') {
            await loadSalesAnalytics();
        }
    } catch (err) {
        console.error('Failed to initialize dashboard:', err);
    }
}

async function loadOrders() {
    const tbody = document.getElementById('orders-table-body');
    try {
        const rows = await api('api/orders.php');
        
        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map(o => {
            const statusClass = {
                'pending': 'bg-warning-subtle text-warning border-warning-subtle',
                'paid': 'bg-info-subtle text-info border-info-subtle',
                'done': 'bg-success-subtle text-success border-success-subtle'
            }[o.status] || 'bg-secondary-subtle text-secondary';

            return `
                <tr>
                    <td class="ps-4 fw-bold text-dark">${esc(o.order_code)}</td>
                    <td class="text-muted small">${esc(o.order_date || o.created_at || '-')}</td>
                    <td class="fw-semibold text-black">${esc(o.customer_name)}</td>
                    <td class="small text-muted" style="max-width: 250px;">${esc(o.items || '-')}</td>
                    <td class="fw-bold text-primary">${rp(o.total)}</td>
                    <td class="small">${esc(o.cashier || 'Unassigned')}</td>
                    <td class="pe-4 text-end">
                        <select class="form-select form-select-sm rounded-3 d-inline-block w-auto ${statusClass}" 
                                data-id="${o.id}" 
                                onchange="updateOrderStatus(${o.id}, this.value)">
                            <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="paid" ${o.status === 'paid' ? 'selected' : ''}>Paid</option>
                            <option value="done" ${o.status === 'done' ? 'selected' : ''}>Done</option>
                        </select>
                    </td>
                </tr>
            `;
        }).join('');
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Failed to load orders: ${esc(err.message)}</td></tr>`;
    }
}

async function updateOrderStatus(orderId, newStatus) {
    try {
        await api(`api/orders.php?id=${orderId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: newStatus })
        });
        await loadOrders();
    } catch (err) {
        alert('Failed to update order status: ' + err.message);
        await loadOrders();
    }
}

async function loadSalesAnalytics() {
    try {
        const salesData = await api('api/sales.php');

        // 1. Daily Revenue Bar Chart
        const c1 = document.getElementById('dailyRevenueChart');
        if (c1 && salesData.daily) {
            new Chart(c1, {
                type: 'bar',
                data: {
                    labels: salesData.daily.map(r => r.day),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: salesData.daily.map(r => +r.revenue),
                        backgroundColor: '#0d6efd',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: value => 'Rp ' + Number(value).toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        }

        // 2. Top Selling Products Pie Chart
        const c2 = document.getElementById('topProductsChart');
        if (c2 && salesData.top) {
            new Chart(c2, {
                type: 'doughnut',
                data: {
                    labels: salesData.top.map(r => r.product_name),
                    datasets: [{
                        data: salesData.top.map(r => +r.qty),
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6c757d']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    } catch (err) {
        console.error('Failed to load sales analytics:', err);
    }
}

document.addEventListener('DOMContentLoaded', initDashboard);