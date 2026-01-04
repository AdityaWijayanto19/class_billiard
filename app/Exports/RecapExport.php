<?php

namespace App\Exports;

use App\Models\Report;
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

class RecapExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function collection()
    {
        // Return order summary as collection
        $orders = collect($this->report->order_summary ?? []);

        return $orders;
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
        ];
    }

    public function map($order): array
    {
        static $no = 0;
        $no++;

        $items = collect($order['items'] ?? [])->pluck('menu_name')->implode(', ');
        $totalItems = collect($order['items'] ?? [])->sum('quantity');

        // Format payment method
        $paymentMethods = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
        ];
        $paymentMethod = $paymentMethods[strtolower($order['payment_method'] ?? 'cash')] ?? 'Tunai';

        return [
            $no,
            $order['created_at'] ?? '',
            $order['customer_name'] ?? '',
            $order['table_number'] ?? '',
            $order['room'] ?? '',
            $items,
            $totalItems,
            'Rp'.number_format($order['total_price'] ?? 0, 0, ',', '.'),
            $paymentMethod,
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
                'startColor' => ['rgb' => '8B5CF6'],
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
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Apply data style to all data rows
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:I'.$highestRow)->applyFromArray($dataStyle);
        }

        // Set alignment for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Waktu Pesan
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Nomor Meja
        $sheet->getStyle('E:E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Ruangan
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah Item
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Total Harga
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Metode Pembayaran

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
        ];
    }

    public function title(): string
    {
        return 'Tutup Hari '.Carbon::parse($this->report->start_date)->format('d-m-Y').' s/d '.Carbon::parse($this->report->end_date)->format('d-m-Y');
    }
}
