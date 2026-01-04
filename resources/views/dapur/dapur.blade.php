@extends('layouts.dapur')

@section('title', 'Dapur - Billiard Class')

{{-- Include shift calculation PHP block --}}
@include('dapur.partials.shift-calculation')

{{-- Include shift meta tags --}}
@include('dapur.partials.shift-meta')

{{-- Include theme initialization script --}}
@include('dapur.partials.theme-manager')

{{-- Include dynamic color variables --}}
@include('dapur.partials.color-variables')

{{-- Include common styles --}}
@include('dapur.partials.common-styles')

{{-- Include sidebar & main content styles --}}
@include('dapur.partials.sidebar-main-styles')

{{-- Include order card styles --}}
@include('dapur.partials.order-card-styles')

{{-- Include notification styles --}}
@include('dapur.partials.notification-styles')

@push('styles')
<style>
    /* Reports specific responsive styles */
    @media (max-width: 768px) {
        /* Reports summary mobile */
        #reportsSummary {
            flex-direction: column;
        }

        #reportsSummary > div {
            width: 100%;
            min-width: auto;
        }

        /* Export buttons mobile */
        #exportExcelContainer {
            flex-direction: column;
        }

        #exportExcelContainer button {
            width: 100%;
        }

        /* Table responsive */
        .reports-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 600px;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .filter-input-group {
            flex-wrap: wrap;
        }

        #reportsSummary {
            flex-wrap: wrap;
        }

        #reportsSummary > div {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 200px;
        }
    }
</style>
@endpush

@section('content')
    {{-- Logout Form --}}
    @include('dapur.partials.logout-form')

    {{-- Audio element for notification sound --}}
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source id="notificationSoundSource" src="" type="audio/mpeg">
    </audio>

    {{-- Notification Overlay (Mobile Blur) --}}
    <div id="notificationOverlay" class="notification-overlay"></div>

    {{-- Notification Container --}}
    <div id="notificationContainer" class="notification-container"></div>

    <div class="max-w-7xl mx-auto">
        <div id="ordersSection">
        @if($orders->count() > 0)
            <div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4">
                    @foreach($orders as $order)
                        <div class="order-card-modern group relative bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-hover)] rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-[rgba(var(--primary-color-rgb),0.2)]" data-order-id="{{ $order->id }}">
                            {{-- Decorative Pattern --}}
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -mr-16 -mt-16"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full -ml-12 -mb-12"></div>
                            </div>
                            
                            {{-- Card Header --}}
                            <div class="relative px-6 pt-6 pb-4 border-b border-white/20">
                                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                            <i class="ri-restaurant-line text-white text-xl"></i>
                        </div>
                                        <div>
                                            <p class="text-white font-bold text-sm">Order #{{ $order->id }}</p>
                                            <p class="text-white/80 text-xs">{{ \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $minutesElapsed = \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->diffInMinutes(\Carbon\Carbon::now('Asia/Jakarta'));
                                            $isWarning = $minutesElapsed >= 15;
                                        @endphp
                                        @if($order->status === 'pending')
                                            <div class="px-3 py-1.5 bg-yellow-500/30 backdrop-blur-sm rounded-lg border border-yellow-500/50">
                                                <span class="text-yellow-200 text-xs font-bold uppercase tracking-wider">⏳ Belum Selesai</span>
                                            </div>
                                        @elseif($order->status === 'processing')
                                            <div class="px-3 py-1.5 bg-blue-500/30 backdrop-blur-sm rounded-lg border border-blue-500/50">
                                                <span class="text-blue-200 text-xs font-bold uppercase tracking-wider">🟡 Sedang Diproses</span>
                                            </div>
                                        @endif
                                        <div class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg border border-white/30">
                                            <span class="text-white text-xs font-bold uppercase tracking-wider">{{ $order->room }}</span>
                                        </div>
                </div>
            </div>

                                {{-- Time Indicator --}}
                                @php
                                    $minutesElapsed = \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->diffInMinutes(\Carbon\Carbon::now('Asia/Jakarta'));
                                    $secondsElapsed = \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->diffInSeconds(\Carbon\Carbon::now('Asia/Jakarta'));
                                    $isWarning = $minutesElapsed >= 15;
                                    $stopwatchMinutes = floor($secondsElapsed / 60);
                                    $stopwatchSeconds = $secondsElapsed % 60;
                                @endphp
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-1 h-1.5 rounded-full {{ $isWarning ? 'bg-red-500/50' : 'bg-white/20' }}">
                                        <div class="h-full rounded-full {{ $isWarning ? 'bg-red-500' : 'bg-white/40' }}" style="width: {{ min(100, ($minutesElapsed / 30) * 100) }}%"></div>
            </div>
                                    <span class="text-white/70 text-xs font-medium {{ $isWarning ? 'text-red-300 font-bold' : '' }} stopwatch-timer" data-order-id="{{ $order->id }}" data-start-time="{{ \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->timestamp }}">
                                        ⏱ <span class="stopwatch-display">{{ str_pad($stopwatchMinutes, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($stopwatchSeconds, 2, '0', STR_PAD_LEFT) }}</span>
                                    </span>
                                </div>

                                {{-- Menu Items Preview --}}
                                <div class="flex flex-wrap gap-2">
                                    @foreach($order->orderItems->take(4) as $item)
                                        <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                                    <img src="{{ $item->image ? asset($item->image) : asset('assets/img/default.png') }}" 
                                         alt="{{ $item->menu_name }}" 
                                                 class="w-6 h-6 rounded-full object-cover border border-white/50"
                                         onerror="this.src='{{ asset('assets/img/default.png') }}'">
                                            <span class="text-white text-xs font-semibold">{{ $item->quantity }}x</span>
                                        </div>
                                @endforeach
                                    @if($order->orderItems->count() > 4)
                                        <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                                            <span class="text-white text-xs font-semibold">+{{ $order->orderItems->count() - 4 }}</span>
                            </div>
                                    @endif
                            </div>
            </div>

                            {{-- Card Body --}}
                            <div class="relative px-6 py-5 bg-white/5 backdrop-blur-sm">
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                            <i class="ri-user-line text-white text-sm"></i>
                        </div>
                                        <div class="flex-1">
                                            <p class="text-white/70 text-xs font-medium mb-0.5">Nama Pemesan</p>
                                            <p class="text-white font-bold text-sm">{{ $order->customer_name }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                            <i class="ri-table-line text-white text-sm"></i>
                        </div>
                                        <div class="flex-1">
                                            <p class="text-white/70 text-xs font-medium mb-0.5">Meja</p>
                                            <p class="text-white font-bold text-sm">Meja {{ $order->table_number }}</p>
                    </div>
                </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                            <i class="ri-shopping-bag-line text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-white/70 text-xs font-medium mb-1">Pesanan</p>
                                            <div class="flex flex-wrap gap-1.5">
                                @foreach($order->orderItems as $item)
                                                    <span class="inline-block bg-white/20 backdrop-blur-sm px-2 py-1 rounded-md border border-white/30 text-white text-xs font-medium">
                                                        {{ $item->quantity }}x {{ $item->menu_name }}
                                                    </span>
                                @endforeach
                            </div>
                            </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Card Footer --}}
                            <div class="relative px-6 py-4 bg-white/10 backdrop-blur-sm border-t border-white/20 flex items-center justify-between">
                                <div>
                                    <p class="text-white/70 text-xs font-medium mb-0.5">Total Harga</p>
                                    <p class="text-white font-bold text-lg">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                                @if($order->status === 'pending')
                                    <button class="start-cooking-btn bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="{{ $order->id }}">
                                        <i class="ri-play-circle-line text-base group-hover/btn:scale-110 transition-transform"></i>
                                        <span>Mulai Masak</span>
                                    </button>
                                @elseif($order->status === 'processing')
                                    <button class="complete-order-btn bg-white text-[var(--primary-color)] px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="{{ $order->id }}">
                                        <i class="ri-checkbox-circle-line text-base group-hover/btn:rotate-12 transition-transform"></i>
                                        <span>Selesai</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg">
                    <p>Belum ada pesanan</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/dapur.js') }}"></script>
    <script>
        // Sidebar Toggle


        // Notification and Sound Functions
        const notificationSound = document.getElementById('notificationSound');
        const notificationContainer = document.getElementById('notificationContainer');
        const notificationOverlay = document.getElementById('notificationOverlay');
        let currentOrderIds = new Set();
        let isFirstLoad = true;
        let activeNotifications = new Set();

        // Load active notification sound from localStorage (set by dapur audio settings)
        async function loadActiveNotificationSound() {
            try {
                const savedAudio = localStorage.getItem('kitchenNotificationAudio');
                const audioType = localStorage.getItem('kitchenNotificationAudioType');
                
                const source = notificationSound ? notificationSound.querySelector('#notificationSoundSource') : null;
                
                if (!savedAudio || !source) {
                    // No audio selected, clear source
                    if (source) {
                        source.src = '';
                        if (notificationSound) {
                            notificationSound.pause();
                            notificationSound.currentTime = 0;
                        }
                    }
                    return;
                }
                
                if (audioType === 'database') {
                    // Load from database
                    const response = await fetch('/notification-sounds');
                    const sounds = await response.json();
                    const sound = sounds.find(s => s.filename === savedAudio);
                    if (sound) {
                        if (sound.file_path.startsWith('sounds/')) {
                            source.src = '{{ asset("storage") }}/' + sound.file_path;
                        } else {
                            source.src = '{{ asset("assets/sounds") }}/' + sound.filename;
                        }
                        notificationSound.load();
                    } else {
                        // Sound not found in database, clear
                        source.src = '';
                        if (notificationSound) {
                            notificationSound.pause();
                            notificationSound.currentTime = 0;
                        }
                        // Clear invalid localStorage
                        localStorage.removeItem('kitchenNotificationAudio');
                        localStorage.removeItem('kitchenNotificationAudioType');
                    }
                } else if (audioType === 'file') {
                    // File was selected directly, but file object is not persistent
                    // User needs to reselect on page reload
                    source.src = '';
                    if (notificationSound) {
                        notificationSound.pause();
                        notificationSound.currentTime = 0;
                    }
                    // Clear invalid localStorage
                    localStorage.removeItem('kitchenNotificationAudio');
                    localStorage.removeItem('kitchenNotificationAudioType');
                }
            } catch (error) {
                console.error('Error loading active notification sound:', error);
                // On error, clear audio source
                const source = notificationSound ? notificationSound.querySelector('#notificationSoundSource') : null;
                if (source) {
                    source.src = '';
                }
            }
        }

        // Load active sound on page load
        loadActiveNotificationSound();

        // Sound unlock state (browsers block autoplay) and pending play flag
        let isSoundUnlocked = false;
        let pendingNotificationPlay = false;

        // Try to unlock audio on first user interaction
        function setupSoundUnlock() {
            const tryUnlock = async () => {
                if (isSoundUnlocked) return;
                if (!notificationSound || !notificationSound.querySelector('#notificationSoundSource') || !notificationSound.querySelector('#notificationSoundSource').src) return;

                try {
                    notificationSound.muted = true;
                    await notificationSound.play();
                    notificationSound.pause();
                    notificationSound.currentTime = 0;
                    notificationSound.muted = false;
                    isSoundUnlocked = true;

                    // If there was a pending play requested earlier, play now
                    if (pendingNotificationPlay) {
                        pendingNotificationPlay = false;
                        notificationSound.play().catch(() => {});
                    }
                } catch (e) {
                    // ignore - will try again on next interaction
                }
            };

            ['click', 'touchstart', 'keydown'].forEach(evt => {
                document.addEventListener(evt, tryUnlock, { once: true, capture: true });
            });
        }

        // Function to update overlay visibility
        function updateNotificationOverlay() {
            if (notificationOverlay && notificationContainer) {
                // Check if there are any visible notifications
                const visibleNotifications = notificationContainer.querySelectorAll('.notification:not(.hide)');
                
                if (visibleNotifications.length > 0) {
                    notificationOverlay.classList.add('show');
                } else {
                    notificationOverlay.classList.remove('show');
                    // Force hide with visibility
                    notificationOverlay.style.visibility = 'hidden';
                }
            }
        }

        // Function to show notification
        function showNotification(order) {
            const notification = document.createElement('div');
            notification.className = 'notification bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-hover)] text-white p-5 rounded-xl shadow-[0_8px_24px_rgba(var(--primary-color-rgb),0.4)] mb-4 flex items-center gap-4 min-w-[300px] relative z-[9999] animate-[slideInRight_0.5s_ease-out] sm:min-w-0 sm:w-full sm:p-4';
            notification.id = `notification-${order.id}`;
            
            const itemsText = order.order_items.map(item => 
                `${item.quantity}x ${item.menu_name}`
            ).join(', ');
            
            notification.innerHTML = `
                <div class="notification-icon">
                    <i class="ri-notification-3-line"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Pesanan Baru!</div>
                    <div class="notification-message">
                        <strong>${order.customer_name}</strong> - Meja ${order.table_number} (${order.room})<br>
                        ${itemsText}<br>
                        <strong>Total: Rp${parseInt(order.total_price).toLocaleString('id-ID')}</strong>
                    </div>
                </div>
                <button class="notification-close" onclick="closeNotification(${order.id})">
                    <i class="ri-close-line"></i>
                </button>
            `;
            
            if (notificationContainer) {
                notificationContainer.appendChild(notification);
            }
            activeNotifications.add(order.id);
            
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                updateNotificationOverlay();
            }, 10);
            
            // Auto close after 8 seconds
            setTimeout(() => {
                closeNotification(order.id);
            }, 8000);
            
            // Play sound only if audio is selected
            if (notificationSound) {
                const source = notificationSound.querySelector('#notificationSoundSource');
                // Only play if source has a valid src
                if (source && source.src && source.src !== '' && source.src !== window.location.href) {
                    // If unlocked, play immediately; otherwise mark pending and prompt unlock via next interaction
                    if (isSoundUnlocked) {
                        notificationSound.currentTime = 0;
                        notificationSound.play().catch(() => {});
                    } else {
                        pendingNotificationPlay = true;
                    }
                }
            }
        }

        // Function to close notification (global scope)
        window.closeNotification = function(orderId) {
            const notification = document.getElementById(`notification-${orderId}`);
            if (notification) {
                notification.classList.add('hide');
                activeNotifications.delete(orderId);
                
                // Update overlay immediately
                updateNotificationOverlay();
                
                setTimeout(() => {
                    notification.remove();
                    
                    // Final check - ensure overlay is hidden if no notifications
                    const remainingNotifications = notificationContainer.querySelectorAll('.notification:not(.hide)');
                    if (remainingNotifications.length === 0 && notificationOverlay) {
                        notificationOverlay.classList.remove('show');
                        notificationOverlay.style.visibility = 'hidden';
                        notificationOverlay.style.opacity = '0';
                    }
                }, 500);
            }
        }

        // Close overlay when clicked (mobile) - but prevent event bubbling
        if (notificationOverlay) {
            notificationOverlay.addEventListener('click', function(e) {
                // Only close if clicking directly on overlay, not on notification
                if (e.target === notificationOverlay) {
                    // Close all notifications
                    const notificationsToClose = Array.from(activeNotifications);
                    notificationsToClose.forEach(orderId => {
                        window.closeNotification(orderId);
                    });
                }
            });
        }
        
        // Prevent notification clicks from closing overlay
        if (notificationContainer) {
            notificationContainer.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Function to render order card
        function renderOrderCard(order) {
            const items = order.order_items || [];
            const orderDate = new Date(order.created_at);
            const orderTime = orderDate.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Calculate minutes elapsed
            const now = new Date();
            const minutesElapsed = Math.floor((now - orderDate) / (1000 * 60));
            const isWarning = minutesElapsed >= 15;
            
            const previewItems = items.slice(0, 4);
            const remainingCount = items.length > 4 ? items.length - 4 : 0;
            
            return `
                <div class="order-card-modern group relative bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-hover)] rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-[rgba(var(--primary-color-rgb),0.2)]" data-order-id="${order.id}">
                    <!-- Decorative Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full -ml-12 -mb-12"></div>
                    </div>
                    
                    <!-- Card Header -->
                    <div class="relative px-6 pt-6 pb-4 border-b border-white/20">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                    <i class="ri-restaurant-line text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">Order #${order.id}</p>
                                    <p class="text-white/80 text-xs">${orderTime} WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${order.status === 'pending' ? `
                                    <div class="px-3 py-1.5 bg-yellow-500/30 backdrop-blur-sm rounded-lg border border-yellow-500/50">
                                        <span class="text-yellow-200 text-xs font-bold uppercase tracking-wider">⏳ Belum Selesai</span>
                                    </div>
                                ` : order.status === 'processing' ? `
                                    <div class="px-3 py-1.5 bg-blue-500/30 backdrop-blur-sm rounded-lg border border-blue-500/50">
                                        <span class="text-blue-200 text-xs font-bold uppercase tracking-wider">🟡 Sedang Diproses</span>
                                    </div>
                                ` : ''}
                                <div class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg border border-white/30">
                                    <span class="text-white text-xs font-bold uppercase tracking-wider">${order.room}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Time Indicator -->
                        ${(() => {
                            const progressPercent = Math.min(100, (minutesElapsed / 30) * 100);
                            const totalSeconds = Math.floor((now - orderDate) / 1000);
                            const stopwatchMinutes = Math.floor(totalSeconds / 60);
                            const stopwatchSeconds = totalSeconds % 60;
                            const startTimestamp = Math.floor(orderDate.getTime() / 1000);
                            return `
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-1 h-1.5 rounded-full ${isWarning ? 'bg-red-500/50' : 'bg-white/20'}">
                                        <div class="h-full rounded-full ${isWarning ? 'bg-red-500' : 'bg-white/40'}" style="width: ${progressPercent}%"></div>
                                    </div>
                                    <span class="text-white/70 text-xs font-medium ${isWarning ? 'text-red-300 font-bold' : ''} stopwatch-timer" data-order-id="${order.id}" data-start-time="${startTimestamp}">
                                        ⏱ <span class="stopwatch-display">${String(stopwatchMinutes).padStart(2, '0')}:${String(stopwatchSeconds).padStart(2, '0')}</span>
                                    </span>
                                </div>
                            `;
                        })()}
                        
                        <!-- Menu Items Preview -->
                        <div class="flex flex-wrap gap-2">
                            ${previewItems.map(item => `
                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                            <img src="${item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image) : '/assets/img/default.png'}" 
                                 alt="${item.menu_name}" 
                                         class="w-6 h-6 rounded-full object-cover border border-white/50"
                                 onerror="this.src='/assets/img/default.png'">
                                    <span class="text-white text-xs font-semibold">${item.quantity}x</span>
                                </div>
                        `).join('')}
                            ${remainingCount > 0 ? `
                                <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                                    <span class="text-white text-xs font-semibold">+${remainingCount}</span>
                    </div>
                            ` : ''}
                    </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="relative px-6 py-5 bg-white/5 backdrop-blur-sm">
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-user-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-0.5">Nama Pemesan</p>
                                    <p class="text-white font-bold text-sm">${order.customer_name}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-table-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-0.5">Meja</p>
                                    <p class="text-white font-bold text-sm">Meja ${order.table_number}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-shopping-bag-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-1">Pesanan</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        ${items.map(item => `
                                            <span class="inline-block bg-white/20 backdrop-blur-sm px-2 py-1 rounded-md border border-white/30 text-white text-xs font-medium">
                                                ${item.quantity}x ${item.menu_name}
                                            </span>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="relative px-6 py-4 bg-white/10 backdrop-blur-sm border-t border-white/20 flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-xs font-medium mb-0.5">Total Harga</p>
                            <p class="text-white font-bold text-lg">Rp${parseInt(order.total_price).toLocaleString('id-ID')}</p>
                        </div>
                        ${order.status === 'pending' ? `
                            <button class="start-cooking-btn bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="${order.id}">
                                <i class="ri-play-circle-line text-base group-hover/btn:scale-110 transition-transform"></i>
                                <span>Mulai Masak</span>
                            </button>
                        ` : order.status === 'processing' ? `
                            <button class="complete-order-btn bg-white text-[var(--primary-color)] px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="${order.id}">
                                <i class="ri-checkbox-circle-line text-base group-hover/btn:rotate-12 transition-transform"></i>
                                <span>Selesai</span>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Variable untuk SSE connection
        let eventSource = null;
        let reconnectTimeout = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 10;

        // Function untuk update orders display
        function updateOrdersDisplay(orders) {
            const ordersSection = document.getElementById('ordersSection');
            if (!ordersSection) return;
            
            if (orders.length === 0) {
                ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
            } else {
                const ordersGrid = ordersSection.querySelector('.grid');
                if (ordersGrid) {
                    ordersGrid.innerHTML = orders.map(order => renderOrderCard(order)).join('');
                } else {
                    ordersSection.innerHTML = `
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4">
                            ${orders.map(order => renderOrderCard(order)).join('')}
                        </div>
                    `;
                }
                // Update stopwatches immediately after rendering
                updateStopwatches();
            }
        }

        // Function untuk handle new orders dari SSE
        function handleNewOrders(newOrders) {
            if (!newOrders || newOrders.length === 0) return;
            const ordersSection = document.getElementById('ordersSection');
            if (!ordersSection) return;

            let ordersGrid = ordersSection.querySelector('.grid');
            if (!ordersGrid) {
                ordersSection.innerHTML = '<div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4"></div>';
                ordersGrid = ordersSection.querySelector('.grid');
            }
            
            // Jika ordersGrid masih null setelah update, hentikan operasi
            if (!ordersGrid) return;
            
            newOrders.forEach(order => {
                // Skip if already tracked in memory
                if (currentOrderIds.has(order.id)) return;

                // Also skip if DOM already contains this order (extra safety)
                if (ordersGrid.querySelector(`[data-order-id="${order.id}"]`)) return;

                // New order detected
                currentOrderIds.add(order.id);

                // Insert new order at the beginning
                const newOrderCard = renderOrderCard(order);
                ordersGrid.insertAdjacentHTML('afterbegin', newOrderCard);

                // Play notification sound for incoming order (unless this is the very first load)
                if (!isFirstLoad) {
                    const source = notificationSound ? notificationSound.querySelector('#notificationSoundSource') : null;
                    if (notificationSound && source && source.src && source.src !== '' && source.src !== window.location.href) {
                        if (isSoundUnlocked) {
                            notificationSound.currentTime = 0;
                            notificationSound.play().catch(() => {});
                        } else {
                            pendingNotificationPlay = true;
                        }
                    }
                }
            });
            
            updateStopwatches();
        }

        // Function untuk fetch initial orders
        async function fetchInitialOrders() {
            try {
                const response = await fetch('/orders/active');
                const data = await response.json();
                
                if (response.ok && data.orders) {
                    const orderIds = new Set(data.orders.map(o => o.id));
                    currentOrderIds = orderIds;
                    updateOrdersDisplay(data.orders);
                    isFirstLoad = false;
                }
            } catch (error) {
                console.error('Error fetching initial orders:', error);
            }
        }

        // Function untuk fetch dan update orders (untuk refresh setelah action)
        async function fetchAndUpdateOrders() {
            try {
                const response = await fetch('/orders/active');
                const data = await response.json();
                
                if (response.ok && data.orders) {
                    const orderIds = new Set(data.orders.map(o => o.id));
                    currentOrderIds = orderIds;
                    updateOrdersDisplay(data.orders);
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        // Function untuk connect ke SSE
        function connectSSE() {
            // Close existing connection if any
            if (eventSource) {
                eventSource.close();
            }

            try {
                eventSource = new EventSource('/orders/stream');
                
                eventSource.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.type === 'new_orders' && data.orders) {
                            handleNewOrders(data.orders);
                        }
                    } catch (error) {
                        console.error('Error parsing SSE data:', error);
                    }
                };
                
                eventSource.onerror = function(error) {
                    console.error('SSE connection error:', error);
                    eventSource.close();
                    
                    // Reconnect dengan exponential backoff
                    reconnectAttempts++;
                    if (reconnectAttempts < maxReconnectAttempts) {
                        const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000); // Max 30 seconds
                        reconnectTimeout = setTimeout(() => {
                            console.log('Reconnecting to SSE...');
                            connectSSE();
                        }, delay);
                    } else {
                        console.error('Max reconnect attempts reached. Falling back to polling.');
                        // Fallback ke polling jika SSE gagal
                        startPollingFallback();
                    }
                };
                
                eventSource.onopen = function() {
                    console.log('SSE connection established');
                    reconnectAttempts = 0; // Reset reconnect attempts on successful connection
                };
                
            } catch (error) {
                console.error('Error creating SSE connection:', error);
                // Fallback ke polling jika SSE tidak didukung
                startPollingFallback();
            }
        }

        // Fallback polling jika SSE tidak tersedia
        let pollingInterval = null;
        function startPollingFallback() {
            if (pollingInterval) return; // Already polling
            
            console.log('Using polling fallback');
            pollingInterval = setInterval(async () => {
                try {
                    const response = await fetch('/orders/active');
                    const data = await response.json();
                    
                    if (response.ok && data.orders) {
                        const newOrderIds = new Set(data.orders.map(o => o.id));
                        let hasNewOrder = false;
                        
                        data.orders.forEach(order => {
                            if (!currentOrderIds.has(order.id)) {
                                hasNewOrder = true;
                                showNotification(order);
                            }
                        });
                        
                        currentOrderIds = newOrderIds;
                        updateOrdersDisplay(data.orders);
                }
            } catch (error) {
                    console.error('Polling error:', error);
            }
            }, 2000); // Poll every 2 seconds as fallback
        }

        // Stopwatch timer function
        function updateStopwatches() {
            const now = Math.floor(Date.now() / 1000);
            document.querySelectorAll('.stopwatch-timer').forEach(timer => {
                const startTime = parseInt(timer.getAttribute('data-start-time'));
                const elapsedSeconds = now - startTime;
                const minutes = Math.floor(elapsedSeconds / 60);
                const seconds = elapsedSeconds % 60;
                
                const display = timer.querySelector('.stopwatch-display');
                if (display) {
                    display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    
                    // Update warning color if >= 15 minutes
                    if (minutes >= 15) {
                        timer.classList.add('text-red-300', 'font-bold');
                        timer.classList.remove('text-white/70');
                    } else {
                        timer.classList.remove('text-red-300', 'font-bold');
                        timer.classList.add('text-white/70');
                    }
                    
                    // Update progress bar
                    const progressBar = timer.closest('.flex.items-center')?.querySelector('.h-full.rounded-full');
                    if (progressBar) {
                        const progressPercent = Math.min(100, (minutes / 30) * 100);
                        progressBar.style.width = `${progressPercent}%`;
                        
                        const progressContainer = progressBar.parentElement;
                        if (minutes >= 15) {
                            progressContainer.classList.remove('bg-white/20');
                            progressContainer.classList.add('bg-red-500/50');
                            progressBar.classList.remove('bg-white/40');
                            progressBar.classList.add('bg-red-500');
                        } else {
                            progressContainer.classList.remove('bg-red-500/50');
                            progressContainer.classList.add('bg-white/20');
                            progressBar.classList.remove('bg-red-500');
                            progressBar.classList.add('bg-white/40');
                        }
                    }
                }
            });
        }

        // Start stopwatch updates every second
        setInterval(updateStopwatches, 1000);
        
        
        {{-- Include shift check script --}}
        @include('dapur.partials.shift-check-script')

        // Initialize order IDs on page load
        (function() {
            const initialOrders = @json($orders);
            initialOrders.forEach(order => {
                currentOrderIds.add(order.id);
            });
            
            // Initial fetch dan setup SSE
            fetchInitialOrders().then(() => {
                // Connect to SSE after initial load
                connectSSE();
            });
        })();

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (eventSource) {
                eventSource.close();
            }
            if (reconnectTimeout) {
                clearTimeout(reconnectTimeout);
            }
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });

        // Handle start cooking button
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.start-cooking-btn')) {
                const button = e.target.closest('.start-cooking-btn');
                const orderId = button.getAttribute('data-order-id');
                
                if (!confirm('Mulai memproses pesanan ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/orders/${orderId}/start-cooking`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Refresh orders to update status
                        fetchAndUpdateOrders();
                    } else {
                        alert(result.message || 'Gagal memulai proses pesanan');
                    }
                } catch (error) {
                    console.error('Error starting cooking:', error);
                    alert('Terjadi kesalahan saat memulai proses pesanan');
                }
            }
        });

        // Handle complete order
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.complete-order-btn')) {
                const button = e.target.closest('.complete-order-btn');
                const orderId = button.getAttribute('data-order-id');
                
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
                        // Remove order ID from tracking
                        currentOrderIds.delete(parseInt(orderId));
                        
                        // Remove order from orders section dengan animasi
                        const orderCard = button.closest('[data-order-id]');
                        if (orderCard) {
                            // Tambahkan animasi fade out dan slide
                            orderCard.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out, margin 0.3s ease-out';
                            orderCard.style.opacity = '0';
                            orderCard.style.transform = 'translateX(-20px) scale(0.95)';
                            orderCard.style.marginBottom = '0';
                            
                            setTimeout(() => {
                                orderCard.remove();
                                
                                // Check if no more orders
                                const ordersSection = document.getElementById('ordersSection');
                                if (ordersSection) {
                                    const orderCards = ordersSection.querySelectorAll('[data-order-id]');
                                    if (orderCards.length === 0) {
                                        // Jika tidak ada orders lagi, tampilkan pesan kosong
                                        const grid = ordersSection.querySelector('.grid');
                                        if (grid) {
                                            ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
                                        } else {
                                            ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
                                        }
                                    }
                                }
                            }, 300);
                        } else {
                            // Jika orderCard tidak ditemukan, refresh dari server
                            fetchAndUpdateOrders();
                        }
                    } else {
                        alert(result.message || 'Gagal menyelesaikan pesanan');
                    }
                } catch (error) {
                    console.error('Error completing order:', error);
                    alert('Terjadi kesalahan saat menyelesaikan pesanan');
                }
            }
        });

    </script>

    {{-- Include theme manager script --}}
    @include('dapur.partials.theme-manager')

    @endpush
@endsection

