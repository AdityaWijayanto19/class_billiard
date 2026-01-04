<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Billiard - Create Order</title>
    <!-- Google Font Barlow -->
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.min.css" rel="stylesheet">
    <!-- Tailwind CSS 4.0 CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-bg-dark: #0a0a0a;
            --color-bg-sidebar: #1a1a1a;
            --color-primary: #FFD700;
            --color-text-gray: #abbbc2;
            --color-border-base: #393c49;
            --font-barlow: "Barlow", sans-serif;
        }

        body {
            background-color: var(--color-bg-dark);
            color: white;
            font-family: var(--font-barlow);
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #393c49; border-radius: 10px; }
        
        * {
            box-sizing: border-box;
        }
    </style>
</head>

<body class="antialiased">

    <div class="flex h-screen w-screen overflow-hidden">

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden transition-all duration-300"
            style="width: calc(100% - 0px); padding-top: 8rem; box-sizing: border-box;" id="mainContent">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-0 px-4 md:px-8 fixed top-0 left-0 right-0 bg-bg-dark z-30 py-4"
                style="width: calc(100% - 0px); transition: width 0.3s ease;">
                <div class="min-w-0">
                    <h1 class="text-2xl md:text-3xl font-semibold mb-1">Class Billiard</h1>
                    <p class="text-text-gray font-medium text-xs md:text-base">{{ \Carbon\Carbon::now()->format('l, d M Y') }}</p>
                </div>
                <div class="relative w-full md:w-72">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-gray">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input type="text" id="searchInput" placeholder="Search for food, menu, etc.."
                        class="w-full bg-bg-sidebar border border-border-base rounded-lg py-2.5 md:py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-primary placeholder:text-text-gray">
                </div>
            </header>

            <!-- Tabs Navigation -->
            <div class="flex gap-8 border-b border-border-base mb-8 overflow-x-auto no-scrollbar px-8 fixed top-24 left-0 right-0 bg-bg-dark z-20" style="width: calc(100% - 0px); transition: width 0.3s ease;">
                <button
                    class="category-tab pb-3 text-primary border-b-2 border-primary font-semibold text-sm whitespace-nowrap"
                    data-category="all">All</button>
                @foreach($categories as $category)
                    <button
                        class="category-tab pb-3 text-white font-semibold text-sm opacity-60 hover:opacity-100 whitespace-nowrap"
                        data-category="{{ $category->slug }}">{{ $category->name }}</button>
                @endforeach
            </div>

            <!-- Section Title -->
            <div class="flex justify-between items-center mb-16 px-8 pt-8">
                <h2 class="text-xl font-semibold">Choose Dishes</h2>
            </div>

            <!-- Grid of Dishes (Fixed card size, responsive grid) -->
            <div class="px-8 pb-20">
                <div class="grid gap-x-6 gap-y-12 w-full" id="menuGrid" style="grid-template-columns: repeat(auto-fit, 240px); justify-content: flex-start; box-sizing: border-box;">
                    @php
                        $allMenus = collect();
                        foreach ($categories as $category) {
                            foreach ($category->menus as $menu) {
                                $allMenus->push((object) [
                                    'menu' => $menu,
                                    'category' => $category
                                ]);
                            }
                        }
                        $urlParams = request()->query();
                        $hasBarcodeParams = isset($urlParams['table']) || isset($urlParams['room']) || isset($urlParams['order_id']);
                    @endphp

                    @forelse($allMenus as $item)
                        @php
                            $menu = $item->menu;
                            $category = $item->category;
                        @endphp
                        <div class="menu-card bg-bg-sidebar rounded-xl pt-16 pb-4 px-4 text-center relative group cursor-pointer"
                            data-category="{{ $category->slug }}" data-name="{{ $menu->name }}"
                            data-price="{{ $menu->price }}"
                            data-image="{{ $menu->image_url }}"
                            data-label="{{ $menu->labels ?? '' }}">
                            <img src="{{ $menu->image_url ?: 'https://via.placeholder.com/400' }}"
                                alt="{{ $menu->name }}"
                                class="w-48 h-48 rounded-full mx-auto -mt-36 object-cover group-hover:scale-105 transition-transform">
                            <h3 class="text-[15px] font-medium mb-2 px-4 -mt-6 leading-snug line-clamp-2">{{ $menu->name }}</h3>
                            @if($menu->short_description)
                                <p class="text-xs text-text-gray mb-2 px-2 line-clamp-2">{{ $menu->short_description }}</p>
                            @endif
                            <p class="text-sm mb-3 font-medium">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                            <div class="flex gap-2 items-center justify-between">
                                <div class="flex gap-1 flex-wrap flex-1">
                                    @if($menu->labels)
                                        @php
                                            // Handle both JSON array and string formats
                                            $labelText = $menu->labels;
                                            if (str_starts_with($labelText, '[')) {
                                                $labelText = json_decode($labelText, true)[0] ?? $labelText;
                                            }
                                            $label = strtolower($labelText);
                                            if(strpos($label, 'best seller') !== false || strpos($label, 'rekomendasi') !== false) {
                                                $bgClass = 'bg-primary/20';
                                                $textClass = 'text-primary';
                                            } elseif(strpos($label, 'new') !== false || strpos($label, 'baru') !== false) {
                                                $bgClass = 'bg-emerald-500/20';
                                                $textClass = 'text-emerald-500';
                                            } else {
                                                $bgClass = 'bg-red-500/20';
                                                $textClass = 'text-red-500';
                                            }
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded {{ $bgClass }} {{ $textClass }}">{{ $labelText }}</span>
                                    @endif
                                </div>
                                <button
                                    class="add-to-cart bg-primary text-white text-black w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg hover:brightness-110 transition-all flex-shrink-0">+</button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-text-gray">
                            <p>Belum ada menu yang tersedia.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- RIGHT ORDER PANEL (Push content, not overlay) -->
        <aside id="orderPanel"
            class="bg-bg-sidebar border-l border-border-base shadow-2xl transition-all duration-300 overflow-hidden flex flex-col z-20"
            style="width: 0; min-width: 0;">
            <div class="p-6 flex flex-col flex-1" style="width: 420px;">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Keranjang</h2>
                    <button id="closeOrderPanel"
                        class="text-primary opacity-50 hover:opacity-100 cursor-pointer transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" x2="6" y1="6" y2="18" />
                            <line x1="6" x2="18" y1="6" y2="18" />
                        </svg>
                    </button>
                </div>

                <!-- Table Header -->
                <div class="grid grid-cols-6 text-sm font-semibold border-b border-border-base pb-4 mb-6">
                    <div class="col-span-4">Item</div>
                    <div class="text-center">Qty</div>
                    <div class="text-right">Price</div>
                </div>

                <!-- Order Items List (Scrollable) -->
                <div class="flex-1 overflow-y-auto space-y-6 pr-2" id="orderItemsList">
                    <!-- Items added here -->
                </div>

                <!-- Checkout Summary -->
                <div class="border-t border-border-base pt-6 mt-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-gray font-medium">Discount</span>
                        <span class="font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-gray font-medium">Sub total</span>
                        <span class="font-semibold" id="orderTotal">Rp 0</span>
                    </div>
                    <button id="checkoutBtn"
                        class="w-full bg-primary text-white text-black py-4 rounded-xl font-bold shadow-[0_8px_24px_rgba(250,154,8,0.3)] hover:brightness-110 active:scale-[0.98] transition-all">
                        Continue to Payment
                    </button>
                </div>
            </div>
        </aside>

        <!-- BOTTOM FOOTER BAR (Centered by default, moves left and shrinks when sidebar opens) -->
        <div id="footerBar"
            class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-bg-sidebar border border-border-base rounded-2xl p-4 shadow-2xl z-10 transition-all duration-300"
            style="width: 800px;">
            <div class="flex justify-between items-center gap-8">
                <div class="flex items-center gap-3 cursor-pointer" onclick="togglePanel()">
                    <div class="bg-primary text-white text-black rounded-full w-10 h-10 flex items-center justify-center font-bold text-sm"
                        id="itemCount">0</div>
                    <span class="text-white font-semibold">Items in Order</span>
                </div>
                <button
                    class="bg-primary text-white text-black px-6 py-3 rounded-lg font-bold hover:brightness-110 transition-all flex items-center gap-2"
                    onclick="togglePanel()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                    View Cart
                </button>
            </div>
        </div>

        <!-- CHECKOUT MODAL -->
        <div id="checkoutModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden"
            onclick="if(event.target === this) closeCheckoutModal()">
            <div class="bg-bg-sidebar border border-border-base rounded-2xl shadow-2xl w-96 p-8 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold">Informasi Pesanan</h3>
                    <button type="button" onclick="closeCheckoutModal()"
                        class="text-text-gray hover:text-white transition">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form id="checkoutForm" class="space-y-4">
                    <!-- Nama Pelanggan -->
                    <div>
                        <label class="block text-xs font-semibold text-text-gray mb-2">Nama Pelanggan</label>
                        <input type="text" id="customerName" name="customer_name" required
                            class="w-full bg-bg-dark border border-border-base rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:border-primary text-white placeholder:text-text-gray"
                            placeholder="Masukkan nama Anda">
                    </div>

                    <!-- Nomor Meja (Auto-filled from barcode) -->
                    <div>
                        <label class="block text-xs font-semibold text-text-gray mb-2">Nomor Meja</label>
                        <input type="text" id="tableNumber" name="table_number" required
                            class="w-full bg-bg-dark border border-border-base rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:border-primary text-white"
                            placeholder="Auto-filled">
                    </div>

                    <!-- Ruangan (Auto-filled from barcode) -->
                    <div>
                        <label class="block text-xs font-semibold text-text-gray mb-2">Ruangan</label>
                        <input type="text" id="room" name="room" required
                            class="w-full bg-bg-dark border border-border-base rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:border-primary text-white"
                            placeholder="Auto-filled">
                    </div>

                    <!-- Order ID (Hidden) -->
                    <input type="hidden" id="orderId" name="order_id">

                    <!-- Total Items -->
                    <div class="bg-bg-dark/50 border border-border-base rounded-lg p-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-text-gray">Total Items:</span>
                            <span class="font-bold" id="checkoutItemCount">0</span>
                        </div>
                        <div class="flex justify-between text-base font-bold">
                            <span>Total:</span>
                            <span id="checkoutTotal">Rp 0</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-primary text-black py-3 rounded-xl font-bold hover:brightness-110 transition-all">
                        Konfirmasi Pesanan
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Category filtering
        document.querySelectorAll('.category-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                const category = this.getAttribute('data-category');

                // Update tab active state
                document.querySelectorAll('.category-tab').forEach(b => {
                    b.classList.remove('text-primary', 'border-b-2', 'border-primary');
                    b.classList.add('opacity-60');
                });
                this.classList.add('text-primary', 'border-b-2', 'border-primary');
                this.classList.remove('opacity-60');

                // Filter cards
                document.querySelectorAll('.menu-card').forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (category === 'all' || cardCategory === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Function to get label color based on text
        function getLabelColorClasses(label) {
            if (!label || !label.trim()) return { bg: '', text: '' };
            
            // Handle both JSON array and string formats
            let labelText = label;
            if (labelText.startsWith('[')) {
                try {
                    labelText = JSON.parse(labelText)[0] || labelText;
                } catch(e) {
                    // If JSON parsing fails, use original
                }
            }
            
            const labelLower = labelText.toLowerCase();
            if (labelLower.includes('best seller') || labelLower.includes('rekomendasi')) {
                return { bg: 'bg-primary/20', text: 'text-primary', value: labelText }; // Gold
            } else if (labelLower.includes('new')) {
                return { bg: 'bg-emerald-500/20', text: 'text-emerald-500', value: labelText }; // Emerald/Green
            } else {
                return { bg: 'bg-red-500/20', text: 'text-red-500', value: labelText }; // Red
            }
        }

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function () {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                card.style.display = name.includes(search) ? 'block' : 'none';
            });
        });

        // Add to cart - using event delegation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-cart')) {
                const btn = e.target.closest('.add-to-cart');
                const card = btn.closest('.menu-card');
                if (!card) return;
                
                const name = card.getAttribute('data-name');
                const price = parseFloat(card.getAttribute('data-price'));
                const image = card.getAttribute('data-image');
                const label = card.getAttribute('data-label') || '';

                console.log('Add to cart clicked:', { name, price, image, label });
                addToOrder(name, price, image, label);
            }
        });

        function addToOrder(name, price, image, label = '') {
            const ordersList = document.getElementById('orderItemsList');
            console.log('addToOrder called with label:', label);

            // Check if item already exists
            let existingItem = null;
            let existingItemId = null;

            ordersList.querySelectorAll('.space-y-3').forEach(item => {
                const itemName = item.querySelector('.font-medium').textContent;
                if (itemName === name) {
                    existingItem = item;
                    existingItemId = item.id;
                }
            });

            if (existingItem) {
                // Item exists - increase quantity
                const qtyDisplay = existingItem.querySelector('.qty-display');
                const priceDisplay = existingItem.querySelector('.price-display');
                const currentQty = parseInt(qtyDisplay.textContent);
                const newQty = currentQty + 1;

                qtyDisplay.textContent = newQty;
                priceDisplay.textContent = 'Rp ' + (price * newQty).toLocaleString('id-ID');
            } else {
                // Item doesn't exist - add new
                const itemId = 'item-' + Date.now();
                
                let labelHTML = '';
                if(label && label.trim()) {
                    const colors = getLabelColorClasses(label);
                    labelHTML = `<span class="text-xs px-2 py-0.5 rounded ${colors.bg} ${colors.text}">${colors.value}</span>`;
                }

                const itemHTML = `
                <div class="space-y-3" id="${itemId}">
                    <div class="grid grid-cols-6 items-center">
                        <div class="col-span-4 flex items-center gap-3">
                            <img src="${image || 'https://via.placeholder.com/100'}" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <p class="text-sm font-medium leading-tight line-clamp-1">${name}</p>
                                <p class="text-xs text-text-gray mt-1 font-medium">Rp ${(price).toLocaleString('id-ID')}</p>
                                ${labelHTML ? `<div class="flex gap-1 mt-1">${labelHTML}</div>` : ''}
                            </div>
                        </div>
                        <div class="text-center flex items-center justify-center gap-1">
                            <button onclick="decreaseQty('${itemId}');" class="bg-bg-dark border border-border-base rounded-lg w-8 h-8 flex items-center justify-center text-primary hover:bg-primary/10 transition-colors">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <div class="bg-bg-dark border border-border-base rounded-lg w-10 h-10 flex items-center justify-center font-semibold qty-display">1</div>
                            <button onclick="increaseQty('${itemId}');" class="bg-bg-dark border border-border-base rounded-lg w-8 h-8 flex items-center justify-center text-primary hover:bg-primary/10 transition-colors">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                        <div class="text-right font-medium text-sm price-display">Rp ${(price).toLocaleString('id-ID')}</div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="removeItem('${itemId}');" class="border border-primary p-3 rounded-lg text-primary hover:bg-primary/10 transition-colors ml-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                    </div>
                </div>
            `;

                ordersList.insertAdjacentHTML('beforeend', itemHTML);
            }

            updateTotal();
            updateFooterBar();
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('#orderItemsList .price-display').forEach(priceEl => {
                const priceText = priceEl.textContent.replace('Rp ', '').replace(/\./g, '');
                total += parseFloat(priceText) || 0;
            });
            document.getElementById('orderTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function removeItem(itemId) {
            const itemEl = document.getElementById(itemId);
            if (itemEl) {
                itemEl.remove();
            }
            orderItems = orderItems.filter(item => item.id !== itemId);
            updateTotal();

            // Double check items count
            const itemsCount = document.querySelectorAll('#orderItemsList > div').length;
            console.log('Items after delete:', itemsCount);
            updateFooterBar();
        }

        function increaseQty(itemId) {
            const itemEl = document.getElementById(itemId);
            if (itemEl) {
                const qtyDisplay = itemEl.querySelector('.qty-display');
                const priceDisplay = itemEl.querySelector('.price-display');
                const priceText = itemEl.querySelector('p.text-xs.text-text-gray')?.textContent || '0';
                const price = parseFloat(priceText.replace('Rp ', '').replace(/\./g, ''));
                
                const currentQty = parseInt(qtyDisplay.textContent);
                const newQty = currentQty + 1;
                
                qtyDisplay.textContent = newQty;
                priceDisplay.textContent = 'Rp ' + (price * newQty).toLocaleString('id-ID');
                
                updateTotal();
                updateFooterBar();
            }
        }

        function decreaseQty(itemId) {
            const itemEl = document.getElementById(itemId);
            if (itemEl) {
                const qtyDisplay = itemEl.querySelector('.qty-display');
                const priceDisplay = itemEl.querySelector('.price-display');
                const priceText = itemEl.querySelector('p.text-xs.text-text-gray')?.textContent || '0';
                const price = parseFloat(priceText.replace('Rp ', '').replace(/\./g, ''));
                
                const currentQty = parseInt(qtyDisplay.textContent);
                const newQty = currentQty - 1;
                
                if (newQty <= 0) {
                    // Remove item if qty reaches 0
                    removeItem(itemId);
                } else {
                    qtyDisplay.textContent = newQty;
                    priceDisplay.textContent = 'Rp ' + (price * newQty).toLocaleString('id-ID');
                    
                    updateTotal();
                    updateFooterBar();
                }
            }
        }

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.payment-method').forEach(b => {
                    b.classList.remove('bg-primary', 'text-white', 'text-black');
                    b.classList.add('border', 'border-border-base', 'text-primary');
                });
                this.classList.add('bg-primary', 'text-white', 'text-black');
                this.classList.remove('border', 'border-border-base', 'text-primary');
            });
        });

        // Toggle Order Panel
        let orderPanelOpen = false;
        let orderItems = []; // Initialize orderItems array
        const orderPanel = document.getElementById('orderPanel');
        const footerBar = document.getElementById('footerBar');
        const closeOrderPanel = document.getElementById('closeOrderPanel');
        let orderItemCount = 0;

        function togglePanel() {
            orderPanelOpen = !orderPanelOpen;
            const main = document.querySelector('main');
            const header = document.querySelector('header');
            const footerBar = document.getElementById('footerBar');

            if (orderPanelOpen) {
                // Open sidebar - push content left, footer moves left too
                orderPanel.style.width = '420px';
                main.style.maxWidth = 'calc(100vw - 420px)';
                header.style.maxWidth = 'calc(100vw - 420px)';
                // Footer: move to center of available space (100vw - 420px) / 2 = 50vw - 210px
                footerBar.style.left = 'calc(50vw - 210px)';
                footerBar.style.width = '700px';
                document.body.style.overflowY = 'hidden';
            } else {
                // Close sidebar - reset everything
                orderPanel.style.width = '0';
                main.style.maxWidth = '100%';
                header.style.maxWidth = '100%';
                footerBar.style.left = '50%';
                footerBar.style.width = '800px';
                document.body.style.overflowY = 'auto';
            }
        }

        closeOrderPanel?.addEventListener('click', togglePanel);

        // Show/hide footer based on items
        function updateFooterBar() {
            const items = document.querySelectorAll('#orderItemsList > div').length;
            console.log('Current items count:', items, 'orderPanelOpen:', orderPanelOpen);

            orderItemCount = items;
            document.getElementById('itemCount').textContent = items;

            if (items > 0) {
                // Show footer
                footerBar.classList.remove('hidden');
            } else {
                // Hide footer
                footerBar.classList.add('hidden');

                // Close sidebar jika dibuka
                if (orderPanelOpen) {
                    console.log('Force closing panel...');
                    const main = document.querySelector('main');
                    const header = document.querySelector('header');
                    orderPanel.style.width = '0';
                    main.style.maxWidth = '100%';
                    header.style.maxWidth = '100%';
                    footerBar.style.left = '50%';
                    footerBar.style.width = '800px';
                    orderPanelOpen = false;
                    document.body.style.overflowY = 'auto';
                }
            }
        }

        // Checkout - Show modal
        document.getElementById('checkoutBtn')?.addEventListener('click', function () {
            const items = document.querySelectorAll('#orderItemsList > div').length;
            if (items === 0) {
                alert('Silahkan tambahkan item ke pesanan Anda');
                return;
            }

            // Update checkout modal totals
            document.getElementById('checkoutItemCount').textContent = items;
            document.getElementById('checkoutTotal').textContent = document.getElementById('orderTotal').textContent;

            // Get barcode params from URL
            const urlParams = new URLSearchParams(window.location.search);
            const tableParam = urlParams.get('table');
            const roomParam = urlParams.get('room');
            const orderIdParam = urlParams.get('order_id');

            // Set auto-filled values and make readonly if from barcode
            const tableInput = document.getElementById('tableNumber');
            const roomInput = document.getElementById('room');
            const orderIdInput = document.getElementById('orderId');

            if (tableParam) {
                tableInput.value = tableParam;
                tableInput.setAttribute('readonly', 'readonly');
            }

            if (roomParam) {
                roomInput.value = roomParam;
                roomInput.setAttribute('readonly', 'readonly');
            }

            if (orderIdParam) {
                orderIdInput.value = orderIdParam;
            }

            // Show modal
            document.getElementById('checkoutModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        let isSubmittingOrder = false;
        const pendingOrderStorageKey = 'pending_order_request_v1';

        function generateIdempotencyKey() {
            if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
                return crypto.randomUUID();
            }

            const bytes = new Uint8Array(16);
            if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
                crypto.getRandomValues(bytes);
            } else {
                for (let i = 0; i < bytes.length; i++) bytes[i] = Math.floor(Math.random() * 256);
            }

            bytes[6] = (bytes[6] & 0x0f) | 0x40;
            bytes[8] = (bytes[8] & 0x3f) | 0x80;

            const hex = [...bytes].map(b => b.toString(16).padStart(2, '0')).join('');
            return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
        }

        // Handle form submission
        document.getElementById('checkoutForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmittingOrder) return;
            isSubmittingOrder = true;

            const submitButton = e.target.querySelector('button[type="submit"]');
            const submitButtonText = submitButton ? submitButton.textContent : null;

            const restoreSubmitState = () => {
                isSubmittingOrder = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    if (submitButtonText !== null) submitButton.textContent = submitButtonText;
                }
            };

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses...';
            }

            const customerName = document.getElementById('customerName').value.trim();
            const tableNumber = document.getElementById('tableNumber').value.trim();
            const room = document.getElementById('room').value.trim();
            const orderId = document.getElementById('orderId').value.trim();

            if (!customerName) {
                alert('Mohon masukkan nama Anda');
                restoreSubmitState();
                return;
            }

            if (!tableNumber) {
                alert('Mohon masukkan nomor meja');
                restoreSubmitState();
                return;
            }

            if (!room) {
                alert('Mohon masukkan ruangan');
                restoreSubmitState();
                return;
            }

            // Collect items from order list
            const items = [];
            document.querySelectorAll('#orderItemsList > div').forEach(itemDiv => {
                const name = itemDiv.querySelector('.font-medium').textContent.trim();
                const priceText = itemDiv.querySelector('.price-display').textContent.replace('Rp ', '').replace(/\./g, '');
                const qtyText = itemDiv.querySelector('.qty-display').textContent.trim();
                const imgElement = itemDiv.querySelector('img');
                const image = imgElement ? imgElement.src : '';

                items.push({
                    menu_name: name,
                    price: parseInt(priceText),
                    quantity: parseInt(qtyText),
                    image: image
                });
            });

            if (items.length === 0) {
                alert('Keranjang masih kosong');
                restoreSubmitState();
                return;
            }

            const paymentMethod = 'cash';
            const normalizedItems = items
                .map(item => ({
                    menu_name: item.menu_name,
                    price: item.price,
                    quantity: item.quantity
                }))
                .sort((a, b) => {
                    const nameCompare = a.menu_name.localeCompare(b.menu_name);
                    if (nameCompare !== 0) return nameCompare;
                    if (a.price !== b.price) return a.price - b.price;
                    return a.quantity - b.quantity;
                });

            const fingerprint = JSON.stringify({
                customerName,
                tableNumber,
                room,
                orderId,
                paymentMethod,
                items: normalizedItems
            });

            let idempotencyKey = null;
            try {
                const stored = localStorage.getItem(pendingOrderStorageKey);
                if (stored) {
                    const parsed = JSON.parse(stored);
                    if (parsed && parsed.fingerprint === fingerprint && typeof parsed.key === 'string') {
                        idempotencyKey = parsed.key;
                    }
                }
            } catch (err) {
                idempotencyKey = null;
            }

            if (!idempotencyKey) {
                idempotencyKey = generateIdempotencyKey();
                try {
                    localStorage.setItem(pendingOrderStorageKey, JSON.stringify({
                        key: idempotencyKey,
                        fingerprint,
                        createdAt: Date.now()
                    }));
                } catch (err) {}
            }

            // Prepare JSON payload (faster parsing than multipart/form-data)
            const payload = {
                customer_name: customerName,
                table_number: tableNumber,
                room: room,
                payment_method: paymentMethod,
                items: items.map(i => ({ name: i.menu_name, price: i.price, quantity: i.quantity, image: i.image }))
            };
            if (orderId) payload.order_id = orderId;

            // Submit to backend (send JSON) and measure timing
            try {
                if (submitButton) submitButton.textContent = 'Mengirim...';
                console.time('orderSubmit');

                const response = await fetch('{{ route("orders.store") }}', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Idempotency-Key': idempotencyKey
                    }
                });

                const contentType = response.headers.get("content-type");
                console.timeEnd('orderSubmit');
                if (!contentType || !contentType.includes("application/json")) {
                    console.error('Invalid response type:', contentType);
                    console.error('Response text:', await response.text());
                    alert('Error: Server returned invalid response');
                    return;
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok && data.success) {
                    // Check order_id or id field
                    const orderId = data.order_id || data.id;
                    if (orderId) {
                        try { localStorage.removeItem(pendingOrderStorageKey); } catch (err) {}
                        window.location.href = '/orders/' + orderId;
                    } else {
                        alert('Order created but ID missing. Response: ' + JSON.stringify(data));
                    }
                } else {
                    alert(data.message || 'Gagal membuat pesanan. Error: ' + (data.error || 'Unknown'));
                    console.error('Error response:', data);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                restoreSubmitState();
            }
        });

        // Close modal when clicking outside
        document.getElementById('checkoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCheckoutModal();
            }
        });
    </script>

</body>

</html>
