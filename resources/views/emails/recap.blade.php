<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutup Hari</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5;">
    <div style="background: white; padding: 20px; max-width: 400px; margin: 0 auto;">
        @if($report)
        {{-- Struk/Nota Format --}}
        <div style="font-family: 'Courier New', monospace; color: #000;">
            {{-- Header --}}
            <div style="text-align: center; margin-bottom: 15px;">
                <h2 style="font-size: 20px; font-weight: bold; margin: 0 0 5px 0; color: #000;">BILLIARD CLASS</h2>
                <p style="font-size: 11px; margin: 5px 0; color: #000;">Jl. Alpukat, Madurejo, Kec. Arut Sel., Kabupaten Kotawaringin Barat, Kalimantan Tengah 74117</p>
                <p style="font-size: 10px; margin: 5px 0; color: #000; letter-spacing: 1px;">{{ str_pad($report->id, 20, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>

            {{-- Transaction Info --}}
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 11px;">
                <div style="text-align: left;">
                    <p style="margin: 2px 0; color: #000;">{{ \Carbon\Carbon::parse($report->report_date)->setTimezone('Asia/Jakarta')->format('Y-m-d') }}</p>
                    <p style="margin: 2px 0; color: #000;">{{ \Carbon\Carbon::parse($report->created_at)->utc()->setTimezone('Asia/Jakarta')->format('H:i:s') }}</p>
                    <p style="margin: 2px 0; color: #000;">No.{{ str_pad($report->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 2px 0; color: #000;">{{ $report->created_by }}</p>
                </div>
            </div>

            <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>

            {{-- Items List --}}
            <div style="margin-bottom: 15px;">
                @php
                    $orderSummary = is_array($report->order_summary) ? $report->order_summary : json_decode($report->order_summary, true);
                @endphp
                @foreach($orderSummary as $order)
                    @foreach($order['items'] as $item)
                    <div style="margin-bottom: 8px; font-size: 11px;">
                        <div style="margin-bottom: 3px; color: #000;">{{ $item['menu_name'] }}</div>
                        <div style="display: flex; justify-content: space-between; color: #000;">
                            <span>{{ $item['quantity'] }} x {{ number_format($item['price'], 0, ',', '.') }}</span>
                            <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>

            <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>

            {{-- Summary --}}
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; color: #000;">
                    <span>Total:</span>
                    <span>Rp {{ number_format($report->total_revenue, 0, ',', '.') }}</span>
                </div>
                @if($report->cash_revenue > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; color: #000;">
                    <span>Bayar (Cash):</span>
                    <span>Rp {{ number_format($report->cash_revenue, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($report->qris_revenue > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; color: #000;">
                    <span>Bayar (QRIS):</span>
                    <span>Rp {{ number_format($report->qris_revenue, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($report->transfer_revenue > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; color: #000;">
                    <span>Bayar (Transfer):</span>
                    <span>Rp {{ number_format($report->transfer_revenue, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 11px; color: #000;">
                    <span>Total Order:</span>
                    <span>{{ number_format($report->total_orders) }} order</span>
                </div>
            </div>

            {{-- Footer --}}
            <div style="text-align: center; font-size: 10px; margin-top: 15px; color: #000;">
                <p style="margin: 3px 0;">Tutup Hari</p>
                <p style="margin: 3px 0;">Billiard Class</p>
            </div>
        </div>
        @else
        {{-- Fallback jika report tidak ada --}}
        <div style="background: #f9fafb; padding: 30px; border-radius: 10px; border: 1px solid #e5e7eb;">
            <p style="margin: 0 0 20px 0; font-size: 16px;">Halo,</p>
            <p style="margin: 0 0 20px 0;">Berikut adalah tutup hari untuk periode:</p>
            <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 4px solid #8b5cf6; margin: 20px 0;">
                <p style="margin: 0; font-size: 18px; font-weight: bold; color: #8b5cf6;">{{ $reportPeriod }}</p>
            </div>
            <p style="margin: 20px 0;">File Excel tutup hari terlampir dalam email ini.</p>
        </div>
        @endif
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #9ca3af; font-size: 12px;">
        <p style="margin: 0;">Email ini dikirim secara otomatis. Mohon jangan membalas email ini.</p>
    </div>
</body>
</html>

