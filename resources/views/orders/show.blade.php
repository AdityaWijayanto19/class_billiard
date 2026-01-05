<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail - Class Billiard</title>
    <!-- Google Font Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            --font-montserrat: "Montserrat", sans-serif;
        }

        body {
            background-color: var(--color-bg-dark);
            color: white;
            font-family: var(--font-montserrat);
        }

        .premium-glow {
            box-shadow: 0 0 40px rgba(255, 215, 0, 0.1);
        }
        
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #393c49; border-radius: 10px; }
    </style>
</head>

<body class="antialiased min-h-screen pb-12">

    <!-- NAVBAR -->
    <x-navbar />

    <div class="max-w-4xl mx-auto px-4 pt-32">
        <!-- Back Button -->
        <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 text-text-gray hover:text-primary transition-all mb-8 group">
            <i class="ri-arrow-left-line text-xl group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-sm font-bold uppercase tracking-widest">Back to Menu</span>
        </a>

        <!-- Order Header Card -->
        <div class="bg-bg-sidebar rounded-3xl p-6 md:p-10 border border-white/5 premium-glow mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 blur-3xl -mr-16 -mt-16 rounded-full"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black mb-2 tracking-tight">Order #{{ $order->id }}</h1>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                        <div class="flex items-center gap-2 text-text-gray">
                            <i class="ri-calendar-line text-primary"></i>
                            <span class="text-xs md:text-sm font-medium">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-text-gray">
                            <i class="ri-map-pin-line text-primary"></i>
                            <span class="text-xs md:text-sm font-medium">{{ $order->table_number }} - {{ $order->room }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    @php
                        $statusData = [
                            'pending' => ['label' => 'Pending', 'class' => 'bg-amber-500/10 text-amber-500 border-amber-500/20'],
                            'processing' => ['label' => 'Processing', 'class' => 'bg-blue-500/10 text-blue-500 border-blue-500/20'],
                            'completed' => ['label' => 'Completed', 'class' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'],
                            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-500/10 text-red-500 border-red-500/20'],
                        ];
                        $status = $statusData[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-gray-500/10 text-gray-500 border-gray-500/20'];
                    @endphp
                    <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest border {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="mb-8">
            <h2 class="text-lg font-black uppercase tracking-widest mb-6 text-text-gray">Your Items</h2>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="bg-bg-sidebar/50 backdrop-blur-sm border border-white/5 rounded-2xl p-4 flex items-center gap-4 transition-all hover:bg-bg-sidebar hover:border-white/10 group">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-xl overflow-hidden border border-white/10 flex-shrink-0 group-hover:scale-105 transition-transform">
                        @if($item->menu && $item->menu->image_url)
                            <img src="{{ $item->menu->image_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-white/5 flex items-center justify-center">
                                <i class="ri-image-line text-2xl text-text-gray"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm md:text-base font-bold mb-1 line-clamp-1 tracking-tight">{{ $item->menu_name }}</h3>
                        <p class="text-[10px] md:text-xs text-text-gray font-medium">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm md:text-base font-black text-primary">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Summary Section -->
        <div class="bg-bg-sidebar rounded-3xl p-6 md:p-8 border border-white/5 premium-glow relative overflow-hidden mb-8">
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-primary/5 blur-3xl -ml-16 -mb-16 rounded-full"></div>
            
            <div class="space-y-4 relative z-10">
                <div class="flex justify-between items-center text-text-gray">
                    <span class="text-xs md:text-sm font-bold uppercase tracking-widest">Subtotal</span>
                    <span class="font-bold">Rp {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-text-gray">
                    <span class="text-xs md:text-sm font-bold uppercase tracking-widest">Discount</span>
                    <span class="font-bold">Rp 0</span>
                </div>
                <div class="pt-4 border-t border-white/5 flex justify-between items-end">
                    <span class="text-sm md:text-base font-bold uppercase">Total Amount</span>
                    <span class="text-2xl md:text-4xl font-black text-primary">Rp {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($order->status === 'pending')
        <div class="flex flex-col gap-4">
            <button id="cancelOrderBtn" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 py-4 rounded-2xl font-black uppercase tracking-widest text-sm transition-all active:scale-95">
                Cancel Order
            </button>
        </div>
        @endif
    </div>

    <!-- Script for Cancel -->
    <script>
        document.getElementById('cancelOrderBtn')?.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to cancel this order?')) return;
            
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Processing...';

            try {
                const response = await fetch('{{ route("orders.cancel", $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || 'Failed to cancel order');
                    btn.disabled = false;
                    btn.textContent = 'Cancel Order';
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Cancel Order';
            }
        });
    </script>

</body>

</html>


