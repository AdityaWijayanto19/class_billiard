<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Billiard Class</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        :root {
            --gold: #FFD700;
            --gold-light: #E6C200;
            --gold-dark: #B39700;
            --purple: #8B5CF6;
            --purple-dark: #6366F1;
            --bg-dark: #0F1117;
            --bg-card: #1a1a2e;
        }

        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%);
            color: #fff;
        }

        .order-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%);
        }

        .order-header {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
            border: 1.5px solid rgba(255, 215, 0, 0.2);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.1);
        }

        .order-header h1 {
            color: #fff;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .order-header p {
            color: rgba(255, 215, 0, 0.7);
            margin-bottom: 0.25rem;
        }

        .order-header strong {
            color: var(--gold);
        }

        .order-status {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            margin-top: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1.5px solid rgba(59, 130, 246, 0.4);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1.5px solid rgba(34, 197, 94, 0.4);
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
        }

        .order-items-section {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.9) 0%, rgba(30, 30, 50, 0.9) 100%);
            border: 1.5px solid rgba(255, 215, 0, 0.15);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .order-items-section h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
            border-radius: 1.2rem;
            margin-bottom: 1rem;
            border: 1.5px solid rgba(255, 215, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .order-item:hover {
            border-color: rgba(255, 215, 0, 0.3);
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 215, 0, 0.15);
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .item-image {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 1rem;
            border: 2px solid rgba(255, 215, 0, 0.2);
            box-shadow: 0 4px 16px rgba(255, 215, 0, 0.1);
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.3px;
        }

        .item-price {
            color: var(--gold);
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
        }

        .item-quantity {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        .order-summary {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.9) 0%, rgba(30, 30, 50, 0.9) 100%);
            border: 1.5px solid rgba(255, 215, 0, 0.15);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.2rem;
            padding-bottom: 1.2rem;
            border-bottom: 1.5px solid rgba(255, 215, 0, 0.1);
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        .summary-value {
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
        }

        .summary-total {
            font-size: 1.3rem;
        }

        .summary-total .summary-label {
            color: #fff;
            font-weight: 700;
        }

        .summary-total .summary-value {
            color: var(--gold);
            font-size: 1.5rem;
            font-weight: 900;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border-radius: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: #000;
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(255, 215, 0, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
            color: #a78bfa;
            border: 1.5px solid rgba(139, 92, 246, 0.4);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.3) 0%, rgba(99, 102, 241, 0.3) 100%);
            border-color: rgba(139, 92, 246, 0.6);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Popup */
        .order-completed-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.95) 0%, rgba(16, 185, 129, 0.95) 100%);
            border: 2px solid rgba(34, 197, 94, 0.6);
            border-radius: 1.8rem;
            padding: 3.5rem;
            z-index: 10000;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(34, 197, 94, 0.3);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: popupSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(10px);
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .order-completed-popup h2 {
            color: #fff;
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .order-completed-popup p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .order-completed-popup button {
            padding: 0.875rem 2.5rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(200, 200, 200, 0.9) 100%);
            color: #10b981;
            border: none;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .order-completed-popup button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .order-detail-container {
                padding: 1rem;
            }

            .order-header,
            .order-items-section,
            .order-summary {
                padding: 1.5rem;
                border-radius: 1.2rem;
                margin-bottom: 1.5rem;
            }

            .order-header h1 {
                font-size: 1.5rem;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
                padding: 1.2rem;
            }

            .item-image {
                width: 120px;
                height: 120px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="order-detail-container">
        <!-- Order Header -->
        <div class="order-header">
            <h1>Order Details</h1>
            <p>Order ID: <strong>#{{ $order->id }}</strong></p>
            <p>Table: <strong>{{ $order->table_number ?? 'N/A' }}</strong></p>
            <p>Room: <strong>{{ $order->room ?? 'N/A' }}</strong></p>
            <span class="order-status {{ $order->status === 'completed' ? 'status-completed' : 'status-processing' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <!-- Order Items -->
        <div class="order-items-section">
            <h2>Order Items</h2>
            @forelse($order->orderItems as $item)
                <div class="order-item">
                    @if($item->menu && $item->menu->image_url)
                        <img src="{{ $item->menu->image_url }}" alt="{{ $item->menu_name }}" class="item-image">
                    @else
                        <div class="item-image" style="display: flex; align-items: center; justify-content: center; background: rgba(139, 92, 246, 0.1);"><i class="ri-image-line" style="font-size: 2.5rem; color: #8B5CF6;"></i></div>
                    @endif
                    <div class="item-info">
                        <div class="item-name">{{ $item->menu_name }}</div>
                        <div class="item-price">Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                        <div class="item-quantity">Quantity: {{ $item->quantity }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--gold); font-weight: 700; font-size: 1.15rem;">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 2rem; color: rgba(255, 255, 255, 0.5);">
                    <p>No items in this order</p>
                </div>
            @endforelse
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">Rp{{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Discount</span>
                <span class="summary-value">Rp0</span>
            </div>
            <div class="summary-row summary-total">
                <span class="summary-label">Total</span>
                <span class="summary-value">Rp{{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">Back to Menu</a>
            @if($order->status !== 'completed')
                <button class="btn btn-secondary" id="cancelOrderBtn">Cancel Order</button>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('cancelOrderBtn')?.addEventListener('click', async function() {
            if(confirm('Are you sure you want to cancel this order?')) {
                try {
                    const response = await fetch('{{ route("orders.cancel", $order->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    
                    if(response.ok) {
                        const popup = document.createElement('div');
                        popup.className = 'order-completed-popup';
                        popup.style.background = 'linear-gradient(135deg, rgba(34, 197, 94, 0.95) 0%, rgba(16, 185, 129, 0.95) 100%)';
                        popup.innerHTML = `
                            <h2>Order Cancelled</h2>
                            <p>Your order has been successfully cancelled.</p>
                            <button onclick="window.location.href='{{ route('orders.create') }}'">Back to Menu</button>
                        `;
                        document.body.appendChild(popup);
                    } else {
                        alert(data.message || 'Failed to cancel order');
                    }
                } catch (error) {
                    alert('Error cancelling order');
                }
            }
        });
    </script>
</body>
</html>

