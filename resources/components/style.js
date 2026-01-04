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

// Initialize page with "makanan" category active
document.addEventListener('DOMContentLoaded', () => {
    // Filter to show makanan items on page load
    filterMenuItems('makanan');
});

// Cart functionality
let cart = [];

function addToCart(name, price) {
    // Find the menu item to get its category and image
    const menuItem = Array.from(document.querySelectorAll('.menu-item-card')).find(card => {
        const btn = card.querySelector('.add-to-cart-btn');
        return btn && btn.getAttribute('data-name') === name;
    });
    
    const category = menuItem ? menuItem.getAttribute('data-category') : 'all';
    
    // Get image source (extract relative path from full URL)
    let imageSrc = '';
    if (menuItem) {
        const img = menuItem.querySelector('.menu-item-image');
        if (img) {
            const fullSrc = img.src;
            // Extract relative path from full URL
            // e.g., "http://localhost/assets/img/file.png" -> "assets/img/file.png"
            const url = new URL(fullSrc);
            imageSrc = url.pathname.startsWith('/') ? url.pathname.substring(1) : url.pathname;
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

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners to all "Tambah" buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            const name = this.getAttribute('data-name');
            const price = parseInt(this.getAttribute('data-price'));
            
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
            
            // Prepare order data
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

            // Submit order
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
                        showNotification('Pesanan Anda telah ditambahkan ke order yang sudah ada. Silakan cek detail order Anda.');
                    } else {
                        showNotification('Pesanan berhasil dibuat!');
                    }
                    
                    // Clear cart
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
                    
                    // Redirect jika ada redirect_url
                    if (result.redirect_url) {
                        setTimeout(() => {
                            window.location.href = result.redirect_url;
                        }, 1500);
                    }
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

// Category filter functionality
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');
        
        const category = btn.dataset.category;
        filterMenuItems(category);
    });
});

