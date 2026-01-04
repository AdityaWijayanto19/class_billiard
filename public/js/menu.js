// Function to show toast notification
function showNotification(message) {
    // Remove existing notification if any
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create notification element
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
        <i class="ri-information-line"></i>
        <span>${message}</span>
    `;
    
    // Add to body
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Function to filter menu items by category
function filterMenuItems(category) {
    const menuItems = document.querySelectorAll('.menu-item-card');
    menuItems.forEach(item => {
        if (category === 'all') {
            item.classList.remove('hidden');
        } else {
            if (item.dataset.category === category) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        }
    });
}

// Check if order functionality should be enabled (only if accessed via barcode)
function isOrderEnabled() {
    const urlParams = new URLSearchParams(window.location.search);
    const tableParam = urlParams.get('table');
    const roomParam = urlParams.get('room');
    const orderIdParam = urlParams.get('order_id');
    return !!(tableParam || roomParam || orderIdParam);
}

// Initialize page with "all" category active (show all items)
document.addEventListener('DOMContentLoaded', () => {
    // Filter to show all items on page load
    filterMenuItems('all');
    
    // Check if order functionality is enabled
    const orderEnabled = isOrderEnabled();
    
    // If order is disabled, hide all order-related elements
    if (!orderEnabled) {
        // Hide order panel, overlay, bottom bar, and checkout modal if they exist
        const orderPanel = document.getElementById('orderPanel');
        const orderOverlay = document.getElementById('orderPanelOverlay');
        const bottomOrderBar = document.getElementById('bottomOrderBar');
        const checkoutModal = document.getElementById('checkoutModal');
        
        if (orderPanel) orderPanel.style.display = 'none';
        if (orderOverlay) orderOverlay.style.display = 'none';
        if (bottomOrderBar) bottomOrderBar.style.display = 'none';
        if (checkoutModal) checkoutModal.style.display = 'none';
        
        // Disable all add-to-cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.style.display = 'none';
        });
        
        // Don't initialize order functionality
        return;
    }
    
    // Auto-fill nomor meja dan ruangan dari query parameter (jika scan QR code)
    const urlParams = new URLSearchParams(window.location.search);
    const tableParam = urlParams.get('table');
    const roomParam = urlParams.get('room');
    const orderIdParam = urlParams.get('order_id');
    
    // Hanya set readonly jika ada query parameter dari QR code
    if (tableParam) {
        const tableNumberInput = document.getElementById('tableNumber');
        if (tableNumberInput) {
            tableNumberInput.value = tableParam;
            // Set readonly karena berasal dari QR code (tidak bisa diubah)
            tableNumberInput.setAttribute('readonly', 'readonly');
        }
    }
    
    if (roomParam) {
        const roomInput = document.getElementById('room');
        if (roomInput) {
            roomInput.value = roomParam;
            // Set readonly karena berasal dari QR code (tidak bisa diubah)
            roomInput.setAttribute('readonly', 'readonly');
        }
    }
    
    // Jika ada order_id, load order yang sudah ada ke cart (setelah semua element sudah ready)
    // Tapi jangan block event listener, jadi kita jalankan setelah semua event listener terpasang
    if (orderIdParam) {
        // Delay lebih lama untuk memastikan semua event listener sudah terpasang
        setTimeout(() => {
            loadExistingOrder(orderIdParam).then(() => {
                // Re-attach listeners setelah load order selesai
                attachAddToCartListeners();
            }).catch(err => {
                console.error('Error loading order:', err);
                // Tetap attach listeners meskipun error
                attachAddToCartListeners();
            });
        }, 500);
    }
    
    // Jika tidak ada query parameter, field bisa diisi manual (tidak readonly)
});

// Cart functionality
let cart = [];

function addToCart(name, price) {
    // Don't allow adding to cart if order is disabled
    if (!isOrderEnabled()) {
        showNotification('Scan QR Code untuk memesan');
        return;
    }
    
    // Find the menu item to get its category and image
    const menuItem = Array.from(document.querySelectorAll('.menu-item-card')).find(card => {
        const btn = card.querySelector('.add-to-cart-btn');
        return btn && btn.getAttribute('data-name') === name;
    });
    
    const category = menuItem ? menuItem.getAttribute('data-category') : 'all';
    
    // Get image source - prefer data-image attribute, fallback to img element
    let imageSrc = '';
    if (menuItem) {
        const btn = menuItem.querySelector('.add-to-cart-btn');
        // Check if button has data-image attribute
        if (btn && btn.getAttribute('data-image')) {
            const fullSrc = btn.getAttribute('data-image');
            // Extract relative path from full URL if needed
            try {
                const url = new URL(fullSrc);
                imageSrc = url.pathname.startsWith('/') ? url.pathname.substring(1) : url.pathname;
            } catch (e) {
                // If already relative path, use as is
                imageSrc = fullSrc.replace(/^\/+/, '');
            }
        } else {
            // Fallback to img element
        const img = menuItem.querySelector('.menu-item-image');
        if (img) {
            const fullSrc = img.src;
            // Extract relative path from full URL
                try {
            const url = new URL(fullSrc);
            imageSrc = url.pathname.startsWith('/') ? url.pathname.substring(1) : url.pathname;
                } catch (e) {
                    imageSrc = fullSrc.replace(/^\/+/, '');
                }
            }
        }
    }
    
    // Check if item already exists in cart
    const existingItem = cart.find(item => item.name === name && item.price === price);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            name: name,
            price: price,
            quantity: 1,
            category: category,
            image: imageSrc
        });
    }
    
    updateOrderBar();
    
    // Check if panel is open and keep it open, then update it
    const orderPanel = document.getElementById('orderPanel');
    const isPanelOpen = orderPanel && orderPanel.classList.contains('active');
    
    if (isPanelOpen) {
        // Panel is open, keep it open and update it
        updateOrderPanel();
    }
    // If panel is not open, don't do anything (user can open it manually)
}

function updateOrderBar() {
    const bottomOrderBar = document.getElementById('bottomOrderBar');
    const orderCount = document.getElementById('orderCount');
    const orderTotal = document.getElementById('orderTotal');
    
    if (cart.length === 0) {
        bottomOrderBar.style.display = 'none';
        return;
    }
    
    bottomOrderBar.style.display = 'flex';
    
    // Calculate total items
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    orderCount.textContent = `${totalItems} Pesanan`;
    
    // Calculate total price
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    orderTotal.textContent = totalPrice.toLocaleString('id-ID');
}

function openOrderPanel() {
    const orderPanel = document.getElementById('orderPanel');
    const orderPanelOverlay = document.getElementById('orderPanelOverlay');
    const menuSection = document.getElementById('menuSection');
    const menuGrid = document.getElementById('menuItemsGrid');
    const bottomOrderBar = document.getElementById('bottomOrderBar');
    
    // Hide bottom order bar when panel opens
    if (bottomOrderBar) {
        bottomOrderBar.style.display = 'none';
    }
    
    // Prevent body scroll when panel is open
    const scrollY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
    
    orderPanel.classList.add('active');
    orderPanelOverlay.classList.add('active');
    menuSection.classList.add('panel-open');
    menuGrid.classList.add('panel-open');
    updateOrderPanel();
}

function closeOrderPanel() {
    const orderPanel = document.getElementById('orderPanel');
    const orderPanelOverlay = document.getElementById('orderPanelOverlay');
    const menuSection = document.getElementById('menuSection');
    const menuGrid = document.getElementById('menuItemsGrid');
    const bottomOrderBar = document.getElementById('bottomOrderBar');
    
    orderPanel.classList.remove('active');
    orderPanelOverlay.classList.remove('active');
    menuSection.classList.remove('panel-open');
    menuGrid.classList.remove('panel-open');
    
    // Restore body scroll
    const scrollY = document.body.style.top;
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    if (scrollY) {
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }
    
    // Show bottom order bar again if there are items in cart
    if (bottomOrderBar && cart.length > 0) {
        updateOrderBar();
    }
}

function updateOrderPanel() {
    const orderItemsList = document.getElementById('orderItemsList');
    const orderSummaryItems = document.getElementById('orderSummaryItems');
    const orderTotalPrice = document.getElementById('orderTotalPrice');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Clear existing items
    orderItemsList.innerHTML = '';
    orderSummaryItems.innerHTML = '';

    if (cart.length === 0) {
        orderItemsList.innerHTML = '<p style="color: #888; text-align: center; padding: 2rem;">Keranjang masih kosong</p>';
        orderTotalPrice.textContent = 'Rp0';
        checkoutBtn.disabled = true;
        return;
    }

    checkoutBtn.disabled = false;

    // Calculate total price
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    orderTotalPrice.textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;

    // Render order items
    cart.forEach((item, index) => {
        // Use stored image or try to get from menu item as fallback
        let imageSrc = item.image || '';
        if (!imageSrc) {
            const menuItem = Array.from(document.querySelectorAll('.menu-item-card')).find(card => {
                const btn = card.querySelector('.add-to-cart-btn');
                return btn && btn.getAttribute('data-name') === item.name;
            });
            
            if (menuItem) {
                const img = menuItem.querySelector('.menu-item-image');
                if (img) {
                    const fullSrc = img.src;
                    // Extract relative path from full URL
                    const url = new URL(fullSrc);
                    imageSrc = url.pathname.startsWith('/') ? url.pathname.substring(1) : url.pathname;
                    // Update cart item with image for future use
                    item.image = imageSrc;
                }
            }
        }

        // Create order item card
        const itemCard = document.createElement('div');
        itemCard.className = 'order-item-card';
        itemCard.setAttribute('data-category', item.category || 'all');
        itemCard.innerHTML = `
            <img src="${imageSrc}" alt="${item.name}" class="order-item-image" onerror="this.style.display='none'">
            <div class="order-item-details">
                <h4 class="order-item-name">
                    ${item.name}
                    <i class="ri-shield-check-line" style="font-size: 1rem; color: #10b981;"></i>
                </h4>
                <p class="order-item-price">Rp${item.price.toLocaleString('id-ID')}</p>
                <div class="order-item-quantity">
                    <button class="quantity-btn" onclick="decreaseQuantity(${index})">-</button>
                    <span class="quantity-value">${String(item.quantity).padStart(2, '0')}</span>
                    <button class="quantity-btn" onclick="increaseQuantity(${index})">+</button>
                </div>
            </div>
        `;
        orderItemsList.appendChild(itemCard);

        // Add to summary
        const summaryItem = document.createElement('div');
        summaryItem.className = 'summary-item';
        summaryItem.innerHTML = `
            <span>${item.quantity} x ${item.name}</span>
            <span>Rp${(item.price * item.quantity).toLocaleString('id-ID')}</span>
        `;
        orderSummaryItems.appendChild(summaryItem);
    });
}

window.increaseQuantity = function(index) {
    cart[index].quantity += 1;
    updateOrderBar();
    updateOrderPanel();
};

window.decreaseQuantity = function(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity -= 1;
    } else {
        cart.splice(index, 1);
    }
    updateOrderBar();
    updateOrderPanel();
};

function filterOrderItems(category) {
    const orderItems = document.querySelectorAll('.order-item-card');
    orderItems.forEach(item => {
        if (category === 'all') {
            item.style.display = 'flex';
        } else {
            const itemCategory = item.getAttribute('data-category');
            if (itemCategory === category) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        }
    });
}

// Function to attach event listeners to add-to-cart buttons
function attachAddToCartListeners() {
    // Don't attach listeners if order is disabled
    if (!isOrderEnabled()) {
        return;
    }
    
    // Add event listeners to all "Tambah" buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        // Remove existing listener jika ada (dengan clone node)
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        // Add new listener
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent event bubbling
            
            const name = this.getAttribute('data-name');
            const price = parseInt(this.getAttribute('data-price'));
            
            if (!name || !price || isNaN(price)) {
                console.error('Invalid button data:', { name, price });
                return;
            }
            
            // Check if panel is open before adding to cart
            const orderPanel = document.getElementById('orderPanel');
            const wasPanelOpen = orderPanel && orderPanel.classList.contains('active');
            
            addToCart(name, price);
            
            // If panel was open, ensure it stays open
            if (wasPanelOpen && orderPanel) {
                if (!orderPanel.classList.contains('active')) {
                    openOrderPanel();
                } else {
                    updateOrderPanel();
                }
            }
        });
    });
}

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Check if order functionality is enabled
    const orderEnabled = isOrderEnabled();
    
    // Only attach order-related listeners if order is enabled
    if (!orderEnabled) {
        return;
    }
    
    // Attach event listeners immediately
    attachAddToCartListeners();

    // View order button functionality
    const viewOrderBtn = document.getElementById('viewOrderBtn');
    if (viewOrderBtn) {
        viewOrderBtn.addEventListener('click', function() {
            if (cart.length === 0) {
                showNotification('tambah menu dulu yuk');
                return;
            }
            openOrderPanel();
        });
    }

    // Close order panel button
    const closeOrderBtn = document.getElementById('closeOrderBtn');
    if (closeOrderBtn) {
        closeOrderBtn.addEventListener('click', function() {
            closeOrderPanel();
        });
    }

    // Close order panel when clicking overlay
    const orderPanelOverlay = document.getElementById('orderPanelOverlay');
    if (orderPanelOverlay) {
        orderPanelOverlay.addEventListener('click', function() {
            closeOrderPanel();
        });
    }

    // Bottom order bar click functionality
    const bottomOrderBar = document.getElementById('bottomOrderBar');
    if (bottomOrderBar) {
        bottomOrderBar.addEventListener('click', function(e) {
            // Don't trigger if clicking the close button
            if (e.target.closest('.close-bottom-bar-btn')) {
                return;
            }
            
            if (cart.length === 0) return;
            
            const orderPanel = document.getElementById('orderPanel');
            // Open order panel when bottom bar is clicked
            if (orderPanel && !orderPanel.classList.contains('active')) {
                openOrderPanel();
            } else if (orderPanel && orderPanel.classList.contains('active')) {
                // If already open, close it
                closeOrderPanel();
            }
        });
    }

    // Close bottom bar button functionality
    const closeBottomBarBtn = document.getElementById('closeBottomBarBtn');
    if (closeBottomBarBtn) {
        closeBottomBarBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering bottom bar click
            
            // Clear cart
            cart = [];
            
            // Hide bottom order bar
            if (bottomOrderBar) {
                bottomOrderBar.style.display = 'none';
            }
            
            // Close order panel if open
            const orderPanel = document.getElementById('orderPanel');
            if (orderPanel && orderPanel.classList.contains('active')) {
                closeOrderPanel();
            }
            
            // Update order panel to show empty state
            updateOrderPanel();
        });
    }

    // Order panel category filter (removed - categories no longer shown)

    // Payment option buttons
    document.querySelectorAll('.payment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong');
                return;
            }
            
            // Show checkout modal
            const checkoutModal = document.getElementById('checkoutModal');
            if (checkoutModal) {
                checkoutModal.classList.add('active');
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                // Re-apply readonly untuk field yang berasal dari QR code
                // Jika tidak ada query parameter, field bisa diisi manual
                const urlParams = new URLSearchParams(window.location.search);
                const tableParam = urlParams.get('table');
                const roomParam = urlParams.get('room');
                
                const tableNumberInput = document.getElementById('tableNumber');
                const roomInput = document.getElementById('room');
                
                if (tableParam && tableNumberInput) {
                    // Ada query parameter dari QR code, set readonly
                    tableNumberInput.value = tableParam;
                    tableNumberInput.setAttribute('readonly', 'readonly');
                } else if (tableNumberInput) {
                    // Tidak ada query parameter, bisa diisi manual
                    tableNumberInput.removeAttribute('readonly');
                }
                
                if (roomParam && roomInput) {
                    // Ada query parameter dari QR code, set readonly
                    roomInput.value = roomParam;
                    roomInput.setAttribute('readonly', 'readonly');
                } else if (roomInput) {
                    // Tidak ada query parameter, bisa diisi manual
                    roomInput.removeAttribute('readonly');
                }
            }
        });
    }

    // Checkout form submission
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                alert('Keranjang masih kosong');
                return;
            }

            const customerName = document.getElementById('customerName').value.trim();
            const tableNumber = document.getElementById('tableNumber').value.trim();
            const room = document.getElementById('room').value.trim();

            if (!customerName || !tableNumber || !room) {
                alert('Mohon lengkapi semua field');
                return;
            }

            const selectedPayment = document.querySelector('.payment-btn.active')?.getAttribute('data-payment') || 'cash';
            
            // Check if we're adding to existing order
            const urlParamsCheckout = new URLSearchParams(window.location.search);
            const orderIdParamCheckout = urlParamsCheckout.get('order_id');
            
            if (orderIdParamCheckout) {
                // Add items to existing order
                try {
                    await addItemsToExistingOrder(orderIdParamCheckout, cart);
                } catch (error) {
                    console.error('Error adding items:', error);
                    alert('Gagal menambahkan item: ' + (error.message || 'Terjadi kesalahan'));
                }
                return;
            }
            
            // Prepare order data for new order
            const orderData = {
                customer_name: customerName,
                table_number: tableNumber,
                room: room,
                payment_method: selectedPayment,
                items: cart.map(item => ({
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    image: item.image || ''
                }))
            };

            // Submit new order
            try {
                const response = await fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Tampilkan pesan jika order duplicate
                    if (result.is_duplicate) {
                        alert('Pesanan Anda telah ditambahkan ke order yang sudah ada. Silakan cek detail order Anda.');
                    }
                    
                    // Redirect ke halaman order detail
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                        return;
                    }
                    
                    // Fallback: Clear cart jika tidak ada redirect
                    cart = [];
                    updateOrderBar();
                    updateOrderPanel();
                    
                    // Close modal and order panel
                    const checkoutModal = document.getElementById('checkoutModal');
                    if (checkoutModal) {
                        checkoutModal.classList.remove('active');
                    }
                    closeOrderPanel();
                    
                    // Restore body scroll
                    document.body.style.overflow = '';
                    
                    // Reset form
                    checkoutForm.reset();
                    
                    // Re-apply readonly untuk field yang berasal dari QR code
                    // Jika tidak ada query parameter, field bisa diisi manual
                    const urlParams = new URLSearchParams(window.location.search);
                    const tableParam = urlParams.get('table');
                    const roomParam = urlParams.get('room');
                    
                    const tableNumberInput = document.getElementById('tableNumber');
                    const roomInput = document.getElementById('room');
                    
                    if (tableParam && tableNumberInput) {
                        // Ada query parameter dari QR code, set readonly
                        tableNumberInput.value = tableParam;
                        tableNumberInput.setAttribute('readonly', 'readonly');
                    } else if (tableNumberInput) {
                        // Tidak ada query parameter, bisa diisi manual
                        tableNumberInput.removeAttribute('readonly');
                    }
                    
                    if (roomParam && roomInput) {
                        // Ada query parameter dari QR code, set readonly
                        roomInput.value = roomParam;
                        roomInput.setAttribute('readonly', 'readonly');
                    } else if (roomInput) {
                        // Tidak ada query parameter, bisa diisi manual
                        roomInput.removeAttribute('readonly');
                    }
                    
                    // Show success message
                    showNotification('Pesanan berhasil dibuat!');
                } else {
                    alert('Gagal membuat pesanan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    }

    // Close modal buttons
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelCheckoutBtn = document.getElementById('cancelCheckoutBtn');
    const checkoutModal = document.getElementById('checkoutModal');

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            if (checkoutModal) {
                checkoutModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    if (cancelCheckoutBtn) {
        cancelCheckoutBtn.addEventListener('click', function() {
            if (checkoutModal) {
                checkoutModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Close modal when clicking outside
    if (checkoutModal) {
        checkoutModal.addEventListener('click', function(e) {
            if (e.target === checkoutModal) {
                checkoutModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

});

// Load existing order ke cart
async function loadExistingOrder(orderId) {
    try {
        // Show loading indicator (non-blocking)
        const response = await fetch(`/orders/${orderId}/data`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            console.warn('Failed to load order:', response.status);
            return; // Exit early, don't block
        }
        
        const result = await response.json();
        
        if (!result.success || !result.order) {
            console.warn('Invalid order data:', result);
            return; // Exit early, don't block
        }
        
        const order = result.order;
        
        // Clear cart dulu
        cart = [];
        
        // Load items ke cart
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                cart.push({
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    category: 'all', // Default category
                    image: item.image || ''
                });
            });
        }
        
        // Update UI (non-blocking)
        requestAnimationFrame(() => {
            updateOrderBar();
            updateOrderPanel();
        });
        
        // Auto-fill form dengan data order (setelah delay untuk memastikan form sudah ada)
        setTimeout(() => {
            try {
                const customerNameInput = document.getElementById('customerName');
                const tableNumberInput = document.getElementById('tableNumber');
                const roomInput = document.getElementById('room');
                
                if (customerNameInput) {
                    customerNameInput.value = order.customer_name || '';
                }
                if (tableNumberInput) {
                    tableNumberInput.value = order.table_number || '';
                    tableNumberInput.setAttribute('readonly', 'readonly');
                }
                if (roomInput) {
                    roomInput.value = order.room || '';
                    roomInput.setAttribute('readonly', 'readonly');
                }
            } catch (e) {
                console.warn('Error filling form:', e);
            }
        }, 200);
        
        // Show notification
        if (cart.length > 0) {
            setTimeout(() => {
                if (typeof showNotification === 'function') {
                    showNotification('Item pesanan sebelumnya sudah dimuat. Silakan tambah item baru.');
                }
            }, 300);
        }
    } catch (error) {
        console.error('Error loading existing order:', error);
        // Jangan block UI jika error, hanya log saja
        // Pastikan event listeners tetap bekerja
    }
}

// Add items to existing order
async function addItemsToExistingOrder(orderId, itemsToAdd) {
    try {
        // Filter hanya item baru (yang belum ada di order sebelumnya)
        // Untuk sekarang, tambahkan semua item yang ada di cart
        const itemsToAddFiltered = itemsToAdd.filter(item => item.quantity > 0);
        
        if (itemsToAddFiltered.length === 0) {
            throw new Error('Tidak ada item yang bisa ditambahkan');
        }
        
        // Add each item to existing order
        for (const item of itemsToAddFiltered) {
            const response = await fetch(`/orders/${orderId}/add-item`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    menu_name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    image: item.image || ''
                })
            });

            if (!response.ok) {
                throw new Error('Gagal menambahkan item: ' + response.statusText);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal menambahkan item');
            }
        }
        
        // Clear cart
        cart = [];
        updateOrderBar();
        updateOrderPanel();
        
        // Close modal
        const checkoutModal = document.getElementById('checkoutModal');
        if (checkoutModal) {
            checkoutModal.classList.remove('active');
        }
        closeOrderPanel();
        document.body.style.overflow = '';
        
        // Reset form
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.reset();
        }
        
        // Show success and redirect
        if (showNotification) {
            showNotification('Item berhasil ditambahkan ke pesanan!');
        }
        
        setTimeout(() => {
            window.location.href = `/orders/${orderId}`;
        }, 1500);
        
    } catch (error) {
        console.error('Error adding items to order:', error);
        alert('Gagal menambahkan item ke pesanan: ' + (error.message || 'Terjadi kesalahan'));
    }
}

// Category filter functionality (support both .category-btn and .category-filter-btn)
document.querySelectorAll('.category-btn, .category-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.category-btn, .category-filter-btn').forEach(b => {
            b.classList.remove('active');
            // Also remove border classes for filter buttons
            b.classList.remove('border-b-2', 'border-[#fa9a08]');
        });
        // Add active class to clicked button
        btn.classList.add('active');
        // Add border for filter buttons
        if (btn.classList.contains('category-filter-btn')) {
            btn.classList.add('border-b-2', 'border-[#fa9a08]');
        }
        
        const category = btn.dataset.category;
        filterMenuItems(category);
    });
});

