// Tab switching functionality
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const ordersSection = document.getElementById('ordersSection');
    const reportsSection = document.getElementById('reportsSection');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.getAttribute('data-tab');
            
            // Update active tab
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Show/hide sections
            if (tab === 'orders') {
                ordersSection.style.display = 'block';
                reportsSection.style.display = 'none';
            } else if (tab === 'reports') {
                ordersSection.style.display = 'none';
                reportsSection.style.display = 'block';
            }
        });
    });

    // Complete order functionality
    function bindCompleteButtons() {
        const completeButtons = document.querySelectorAll('.complete-btn');
        completeButtons.forEach(btn => {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';

            btn.addEventListener('click', async function() {
                const orderId = this.getAttribute('data-order-id');
                
                if (!confirm('Apakah pesanan ini sudah selesai?')) {
                    return;
                }

                try {
                    const response = await fetch(`/orders/${orderId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Remove order card
                        const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                        if (orderCard) {
                            orderCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            orderCard.style.opacity = '0';
                            orderCard.style.transform = 'scale(0.9)';
                            
                            setTimeout(() => {
                                orderCard.remove();
                                
                                // Check if no orders left
                                const ordersGrid = document.querySelector('.orders-grid');
                                if (ordersGrid && ordersGrid.children.length === 0) {
                                    ordersGrid.innerHTML = '<div class="empty-state"><p>Belum ada pesanan</p></div>';
                                }
                            }, 300);
                        }
                    } else {
                        alert('Gagal menyelesaikan pesanan. Silakan coba lagi.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });
    }

    bindCompleteButtons();

    // Live refresh for active orders
    const ordersGrid = document.querySelector('.orders-grid');
    async function fetchActiveOrders() {
        if (!ordersGrid) return;
        try {
            const response = await fetch('/orders/active');
            const data = await response.json();
            if (!response.ok) return;

            if (!data.orders || data.orders.length === 0) {
                ordersGrid.innerHTML = '<div class="empty-state"><p>Belum ada pesanan</p></div>';
                return;
            }

            let html = '';
            data.orders.forEach(order => {
                const items = order.order_items || [];
                const itemsText = items.map(i => `${i.quantity}x ${i.menu_name}`).join(', ');
                const orderTime = new Date(order.created_at).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                html += `
                    <div class="order-card" data-order-id="${order.id}">
                        <div class="order-images">
                            ${items.map(item => `
                                <img src="${item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image) : '/assets/img/default.png'}"
                                    alt="${item.menu_name}"
                                    class="order-item-image-small"
                                    onerror="this.src='/assets/img/default.png'">
                            `).join('')}
                        </div>
                        <div class="order-details">
                            <p><strong>Waktu Pesan :</strong> ${orderTime}</p>
                            <p><strong>Nama Pemesan :</strong> ${order.customer_name}</p>
                            <p><strong>Pesanan :</strong> ${itemsText}</p>
                            <p><strong>Nomor Meja :</strong> ${order.table_number}</p>
                            <p><strong>Ruangan :</strong> ${order.room}</p>
                            <p><strong>Total Harga :</strong> Rp${parseInt(order.total_price).toLocaleString('id-ID')}</p>
                        </div>
                        <button class="complete-btn" data-order-id="${order.id}">Selesai</button>
                    </div>
                `;
            });

            ordersGrid.innerHTML = html;
            bindCompleteButtons();
        } catch (error) {
            console.error('Error fetching active orders:', error);
        }
    }

    // Poll every 5 seconds
    setInterval(fetchActiveOrders, 5000);
    // Initial fetch
    fetchActiveOrders();

    // Reports filter functionality
    let currentFilterType = 'daily';
    let currentFilterValue = {
        daily: new Date().toISOString().split('T')[0],
        monthly: new Date().toISOString().slice(0, 7),
        yearly: new Date().getFullYear().toString()
    };

    const filterTabButtons = document.querySelectorAll('.filter-tab-btn');
    const dailyFilter = document.getElementById('dailyFilter');
    const monthlyFilter = document.getElementById('monthlyFilter');
    const yearlyFilter = document.getElementById('yearlyFilter');
    const reportsContent = document.getElementById('reportsContent');
    const reportsSummary = document.getElementById('reportsSummary');
    const totalOrdersEl = document.getElementById('totalOrders');
    const totalRevenueEl = document.getElementById('totalRevenue');
    const exportExcelBtn = document.getElementById('exportExcelBtn');

    // Filter tab switching
    filterTabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.getAttribute('data-filter-type');
            currentFilterType = type;

            // Update active tab
            filterTabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Show/hide filter inputs
            dailyFilter.style.display = type === 'daily' ? 'flex' : 'none';
            monthlyFilter.style.display = type === 'monthly' ? 'flex' : 'none';
            yearlyFilter.style.display = type === 'yearly' ? 'flex' : 'none';

            // Reset reports content
            reportsContent.innerHTML = '<div class="empty-state"><p>Pilih filter dan klik "Tampilkan" untuk melihat laporan</p></div>';
            reportsSummary.style.display = 'none';
        });
    });

    // Load reports
    document.querySelectorAll('.load-reports-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const type = this.getAttribute('data-type');
            let date, month, year;

            if (type === 'daily') {
                date = document.getElementById('dailyDate').value;
                currentFilterValue.daily = date;
            } else if (type === 'monthly') {
                month = document.getElementById('monthlyDate').value;
                currentFilterValue.monthly = month;
            } else if (type === 'yearly') {
                year = document.getElementById('yearlyDate').value;
                currentFilterValue.yearly = year;
            }

            try {
                const params = new URLSearchParams({
                    type: type,
                    date: date || currentFilterValue.daily,
                    month: month || currentFilterValue.monthly,
                    year: year || currentFilterValue.yearly
                });

                const response = await fetch(`/reports?${params}`);
                const data = await response.json();

                if (response.ok) {
                    displayReports(data.orders);
                    displaySummary(data.total_orders, data.total_revenue);
                    currentFilterType = type;
                } else {
                    alert('Gagal memuat laporan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });

    // Display reports
    function displayReports(orders) {
        if (orders.length === 0) {
            reportsContent.innerHTML = '<div class="empty-state"><p>Tidak ada data laporan untuk periode ini</p></div>';
            reportsSummary.style.display = 'none';
            return;
        }

        let html = '<div class="reports-grid">';
        orders.forEach(order => {
            const orderDate = new Date(order.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const items = order.order_items.map(item => `${item.quantity}x ${item.menu_name}`).join(', ');

            html += `
                <div class="report-card">
                    <div class="order-images">
                        ${order.order_items.slice(0, 5).map(item => 
                            `<img src="${item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image) : '/assets/img/default.png'}" 
                                  alt="${item.menu_name}" 
                                  class="order-item-image-small"
                                  onerror="this.src='/assets/img/default.png'">`
                        ).join('')}
                    </div>
                    <div class="order-details">
                        <p><strong>Tanggal:</strong> ${orderDate}</p>
                        <p><strong>Nama Pemesan:</strong> ${order.customer_name}</p>
                        <p><strong>Pesanan:</strong> ${items}</p>
                        <p><strong>Nomor Meja:</strong> ${order.table_number}</p>
                        <p><strong>Ruangan:</strong> ${order.room}</p>
                        <p><strong>Total Harga:</strong> Rp${parseInt(order.total_price).toLocaleString('id-ID')}</p>
                        <p><strong>Metode Pembayaran:</strong> ${order.payment_method.toUpperCase()}</p>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        reportsContent.innerHTML = html;
    }

    // Display summary
    function displaySummary(totalOrders, totalRevenue) {
        totalOrdersEl.textContent = totalOrders;
        totalRevenueEl.textContent = 'Rp' + parseInt(totalRevenue).toLocaleString('id-ID');
        reportsSummary.style.display = 'flex';
    }

    // Export Excel
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            const params = new URLSearchParams({
                type: currentFilterType,
                date: currentFilterValue.daily,
                month: currentFilterValue.monthly,
                year: currentFilterValue.yearly
            });

            window.location.href = `/reports/export?${params}`;
        });
    }
});

