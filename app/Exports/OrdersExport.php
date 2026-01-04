<?php

namespace App\Exports;

use App\Models\orders;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $type;

    protected $date;

    protected $month;

    protected $year;

    public function __construct($type = 'daily', $date = null, $month = null, $year = null)
    {
        $this->type = $type;
        $this->date = $date ?? Carbon::today()->format('Y-m-d');
        $this->month = $month ?? Carbon::now()->format('Y-m');
        $this->year = $year ?? Carbon::now()->format('Y');
    }

    public function collection()
    {
        $query = orders::with('orderItems')
            ->where('status', 'completed');

        if ($this->type === 'daily') {
            $query->whereDate('updated_at', $this->date);
        } elseif ($this->type === 'monthly') {
            $query->whereYear('updated_at', Carbon::parse($this->month)->year)
                ->whereMonth('updated_at', Carbon::parse($this->month)->month);
        } elseif ($this->type === 'yearly') {
            $query->whereYear('updated_at', $this->year);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Pesan',
            'Nama Pemesan',
            'Nomor Meja',
            'Ruangan',
            'Pesanan',
            'Jumlah Item',
            'Total Harga',
            'Metode Pembayaran',
            'Status',
        ];
    }

    public function map($order): array
    {
        static $no = 0;
        $no++;

        $items = $order->orderItems->pluck('menu_name')->implode(', ');
        $totalItems = $order->orderItems->sum('quantity');

        // Format payment method
        $paymentMethods = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];
        $paymentMethod = $paymentMethods[strtolower($order->payment_method)] ?? strtoupper($order->payment_method);

        return [
            $no,
            Carbon::parse($order->created_at)->format('d/m/Y H:i'),
            $order->customer_name,
            $order->table_number,
            $order->room,
            $items,
            $totalItems,
            'Rp'.number_format($order->total_price, 0, ',', '.'),
            $paymentMethod,
            'Selesai',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header (row 1)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FA9A08'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Style untuk data rows
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        // Apply header style
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Apply data style to all data rows
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:J'.$highestRow)->applyFromArray($dataStyle);
        }

        // Set alignment for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Waktu Pesan
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Nomor Meja
        $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Ruangan
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah Item
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Total Harga
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Metode Pembayaran
        $sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 18,  // Waktu Pesan
            'C' => 20,  // Nama Pemesan
            'D' => 12,  // Nomor Meja
            'E' => 12,  // Ruangan
            'F' => 30,  // Pesanan
            'G' => 12,  // Jumlah Item
            'H' => 15,  // Total Harga
            'I' => 18,  // Metode Pembayaran
            'J' => 12,  // Status
        ];
    }

    public function title(): string
    {
        if ($this->type === 'daily') {
            return 'Laporan Harian '.Carbon::parse($this->date)->format('d-m-Y');
        } elseif ($this->type === 'monthly') {
            return 'Laporan Bulanan '.Carbon::parse($this->month)->format('F Y');
        } else {
            return 'Laporan Tahunan '.$this->year;
        }
    }
}
