<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Tutup Hari - {{ $tanggal->format('d M Y') }}@if(isset($strukData['shift']) && $strukData['shift']) - {{ $strukData['shift']->name }}@endif</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
            color: #000;
            font-size: 12px;
        }

        .struk-container {
            max-width: 80mm;
            margin: 0 auto;
            background: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .decorative-top {
            height: 20px;
            background: repeating-linear-gradient(
                45deg,
                #10b981,
                #10b981 10px,
                #059669 10px,
                #059669 20px
            );
            margin: -15px -15px 15px -15px;
        }

        .decorative-bottom {
            height: 20px;
            background: repeating-linear-gradient(
                45deg,
                #10b981,
                #10b981 10px,
                #059669 10px,
                #059669 20px
            );
            margin: 15px -15px -15px -15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-container {
            margin-bottom: 10px;
        }

        .logo-container img {
            max-width: 60px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .store-address {
            font-size: 10px;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .welcome-message {
            font-size: 10px;
            margin-bottom: 10px;
            font-style: italic;
        }

        .transaction-number {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .items-section {
            margin: 15px 0;
        }

        .item-row {
            margin-bottom: 8px;
            font-size: 11px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-left: 10px;
        }

        .item-quantity {
            color: #666;
        }

        .item-price {
            font-weight: bold;
        }

        .summary-section {
            margin-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .summary-label {
            font-weight: bold;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #000;
        }

        .footer-message {
            font-size: 10px;
            margin-bottom: 10px;
            font-style: italic;
        }

        .barcode {
            width: 200px;
            height: auto;
            margin: 10px auto;
            display: block;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="struk-container">
        {{-- Decorative Top --}}
        <div class="decorative-top"></div>

        {{-- Header --}}
        <div class="header">
            <div class="logo-container">
                <img src="{{ url(asset('logo.png')) }}" alt="Logo" onerror="this.style.display='none'">
            </div>
            <div class="store-name">CLASS BILLIARD</div>
            <div class="store-address">
                Kitchen System<br>
                Laporan Harian
            </div>
            <div class="welcome-message">Selamat datang di sistem kami</div>
            <div class="transaction-number">
                No. {{ $tanggal->format('Ymd') }}-{{ str_pad($strukData['totalOrder'], 3, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <div class="divider"></div>

        {{-- Transaction Info --}}
        <div class="info-row">
            <span>{{ $tanggal->format('Y-m-d') }}</span>
            <span>{{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
        </div>
        @if(isset($strukData['shift']) && $strukData['shift'])
        <div class="info-row">
            <span>Shift :</span>
            <span>{{ $strukData['shift']->name }} ({{ $strukData['shift']->start_time }} - {{ $strukData['shift']->end_time }} WIB)</span>
        </div>
        @endif
        <div class="info-row">
            <span>Kasir :</span>
            <span>{{ Auth::user()->name ?? 'System' }}</span>
        </div>

        <div class="divider"></div>

        {{-- Detail Orders --}}
        <div class="section-title">Detail Order</div>
        <div class="items-section">
            @foreach($strukData['orders'] as $order)
            <div class="item-row">
                <div class="item-name">{{ $loop->iteration }}. Order #{{ $order->id }}</div>
                <div class="item-details">
                    <div>
                        <div>{{ \Carbon\Carbon::parse($order->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} | {{ $order->customer_name }}</div>
                        <div class="item-quantity">
                            @foreach($order->orderItems as $item)
                                {{ $item->quantity }}x {{ $item->menu_name }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        <div style="font-size: 9px; color: #666;">
                            Meja {{ $order->table_number }} | {{ $order->room }} | 
                            @if($order->payment_method == 'cash')
                                CASH
                            @elseif($order->payment_method == 'qris')
                                QRIS
                            @else
                                TRANSFER
                            @endif
                        </div>
                    </div>
                    <div class="item-price">Rp{{ number_format($order->total_price, 0, ',', '.') }}</div>
                </div>
            </div>
            @if(!$loop->last)
            <div style="border-bottom: 1px dotted #ddd; margin: 8px 0;"></div>
            @endif
            @endforeach
        </div>

        <div class="divider"></div>

        {{-- Summary --}}
        <div class="summary-section">
            <div class="summary-row">
                <span>Total QTY :</span>
                <span>{{ $strukData['orders']->sum(function($order) { return $order->orderItems->sum('quantity'); }) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Sub Total</span>
                <span class="summary-value">Rp{{ number_format($strukData['totalPendapatan'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-row total-row">
                <span>Total</span>
                <span>Rp{{ number_format($strukData['totalPendapatan'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Items Sold Summary --}}
        @if(count($strukData['itemsSold']) > 0)
        <div class="section-title">Item Terjual</div>
        <div class="items-section">
            @foreach($strukData['itemsSold'] as $item)
            <div class="item-row">
                <div class="item-name">{{ $loop->iteration }}. {{ $item['name'] }}</div>
                <div class="item-details">
                    <div class="item-quantity">{{ $item['quantity'] }} pcs</div>
                    <div class="item-price">Rp{{ number_format($item['total_price'], 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="divider"></div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-message">
                Terima kasih telah menggunakan sistem kami
            </div>
            <div style="font-size: 9px; color: #666; margin-top: 10px;">
                Dicetak: {{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB
            </div>
            <div>
                <img src="{{ url(asset('barcode.png')) }}" alt="Barcode" class="barcode" onerror="this.style.display='none'">
            </div>
        </div>

        {{-- Decorative Bottom --}}
        <div class="decorative-bottom"></div>
    </div>
</body>
</html>

