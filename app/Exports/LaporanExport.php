<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;

    // Constructor to receive start and end dates
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Method to fetch data for export
     */
    public function collection()
    {
        // Subquery for transaksi_barang
        $subQuery = DB::table('transaksi_barang')
            ->select(
                'transaksi_id',
                DB::raw('SUM(jumlah * harga_jual) as total_pendapatan'),
                DB::raw('SUM(jumlah * harga_beli) as total_cost'),
                DB::raw('SUM((harga_jual - harga_beli) * jumlah) as keuntungan_bersih')
            )
            ->groupBy('transaksi_id');

        // Main query with date range and calculation of pendapatan_bersih
        $laporans = DB::table('transaksi')
            ->leftJoinSub($subQuery, 'keuntungan', function ($join) {
                $join->on('transaksi.id', '=', 'keuntungan.transaksi_id');
            })
            ->select(
                DB::raw('DATE(transaksi.created_at) as date'),
                DB::raw('SUM(transaksi.total_harga) as total_harga'),
                DB::raw('SUM(IFNULL(keuntungan.keuntungan_bersih, 0)) as pendapatan_bersih')
            )
            ->whereBetween('transaksi.created_at', [$this->startDate, $this->endDate])
            ->groupBy(DB::raw('DATE(transaksi.created_at)'))
            ->orderBy('date')
            ->get();

        return $laporans;
    }

    /**
     * Method to define Excel column headings
     */
    public function headings(): array
    {
        return [
            'Tanggal Transaksi',          // Column names
            'Total Belanja',
            'Pendapatan Bersih'
        ];
    }

    /**
     * Map each row of data into a more readable format
     */
    public function map($row): array
    {
        return [
            $row->date,
            'Rp ' . number_format($row->total_harga, 0, ',', '.'),  // Format to Rupiah
            'Rp ' . number_format($row->pendapatan_bersih, 0, ',', '.'),
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Apply border to the range
        $sheet->getStyle('A1:C' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Apply center alignment for headings
        $sheet->getStyle('A1:C1')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
        ]);

        // Apply padding/spacing for cells in columns A, B, and C
        $sheet->getStyle('A2:C' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        return $sheet;
    }

    /**
     * Format columns, especially for currency
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,       // Format for Date
            'B' => '[$Rp-421] #,##0.00',                     // Custom format for Rupiah with Rp symbol
            'C' => '[$Rp-421] #,##0.00',                     // Custom format for Rupiah with Rp symbol
        ];
    }
}
