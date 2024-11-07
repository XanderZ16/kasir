<?php

namespace App\Http\Controllers;

use App\Exports\laporanExport;
use App\Models\Barang;
use App\Models\Product;
use App\Models\Transaksi;
use App\Models\Transaksi_Barang;
use Dotenv\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransaksiController extends Controller
{
    //


    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'barcode' => 'required|exists:barang,barcode',
        ]);

        // Find the product by barcode
        $barang = Barang::where('barcode', $request->barcode)->first();

        // Check if the product is found
        if (!$barang) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        // Check if the product is out of stock
        if ($barang->stok < 1) {
            return response()->json([
                'message' => 'Product is out of stock',
            ], 400);
        }

        // Return JSON response with product details
        return response()->json([
            'product' => [
                'image' => $barang->gambar,
                'name' => $barang->nama_barang,
                'price' => $barang->harga_jual,
            ],
            'quantity' => 1,  // Default quantity to 1
        ]);
    }



    public function getPrice(Request $request)
    {
        $barcode = $request->input('barcode');
        $barang = Barang::where('barcode', $barcode)->first();

        if ($barang) {
            return response()->json([
                'success' => true,
                'price' => $barang->harga_jual // Mengembalikan harga dalam format integer
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function submitOrder(Request $request)
    {
        // Validasi input
        $validator = validator($request->all(), [
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|integer|min:0',
            'total_bayar' => 'required|integer|min:0', // Validasi untuk total_bayar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Menghitung total harga
        $totalHarga = 0;
        foreach ($request->input('products') as $product) {
            $totalHarga += $product['price'] * $product['quantity'];
        }

        // Validasi apakah total_bayar mencukupi totalHarga
        if ($request->input('total_bayar') < $totalHarga) {
            return response()->json([
                'success' => false,
                'message' => 'Total bayar tidak mencukupi.',
            ], 400);
        }

        // Hitung kembalian
        $kembalian = $request->input('total_bayar') - $totalHarga;

        // Membuat entri transaksi baru
        $transaksi = new Transaksi();
        $transaksi->total_harga = $totalHarga;
        $transaksi->total_bayar = $request->input('total_bayar');
        $transaksi->kembalian = $kembalian;
        $transaksi->save();

        // Menyimpan detail produk yang dibeli ke tabel transaksi_barang
        foreach ($request->input('products') as $product) {
            $barang = Barang::where('nama_barang', $product['name'])->first(); // Asumsi kolom 'name' ada di tabel 'barang'

            if ($barang) {
                // Mengurangi stok barang
                if ($barang->stok >= $product['quantity']) {
                    $barang->stok -= $product['quantity'];

                    // Update status jika stok menjadi 0
                    if ($barang->stok == 0) {
                        $barang->status = 'inactive';
                    }

                    $barang->save();

                    // Menyimpan transaksi barang
                    $transaksiBarang = new Transaksi_Barang();
                    $transaksiBarang->transaksi_id = $transaksi->id;
                    $transaksiBarang->barang_id = $barang->id;
                    $transaksiBarang->jumlah = $product['quantity'];
                    $transaksiBarang->harga_beli = $barang->harga_beli;
                    $transaksiBarang->harga_jual = $product['price'];
                    $transaksiBarang->save();
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok untuk barang ' . $barang->nama_barang . ' tidak mencukupi.',
                    ], 400);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order submitted successfully!',
            'transaksi_id' => $transaksi->id, // Pastikan transaksi_id disertakan dalam respons
            'total_harga' => $totalHarga,
            'total_bayar' => $transaksi->total_bayar,
            'kembalian' => $transaksi->kembalian,
        ], 200);

    }


        public function printStruk($id) {
            // Dapatkan data pesanan berdasarkan orderId
            $transaksi = Transaksi::with(['transaksiBarang.barang'])->find($id);

            if (!$transaksi) {
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            try {
                // Periksa apakah printer sudah dikenali dengan benar
                $printerName = "Blueprint_M58"; // Gunakan nama printer yang sesuai

                // Verifikasi apakah printer ada di sistem
                if (!$this->isPrinterAvailable($printerName)) {
                    return response()->json(['error' => 'Printer tidak ditemukan atau tidak tersedia'], 500);
                }

                // Konektor printer
                $connector = new WindowsPrintConnector($printerName); // Untuk Windows

                // Inisialisasi printer
                $printer = new Printer($connector);

                // Cetak Header
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("SKAJU MART\n");
                $printer->text("Koperasi Pemko, Batam centre,\n Belian\n");
                $printer->text("Telp: 0812-3456-7890\n");
                $printer->feed();

                // Informasi Transaksi
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("No. Pesanan: " . $transaksi->id . "\n");
                $printer->text("Tanggal: " . $transaksi->created_at->format('d-m-Y H:i:s') . "\n");
                $printer->text("--------------------------------\n");

                // Detail Produk
                foreach ($transaksi->transaksiBarang as $product) {
                    $barang = $product->barang; // Mengakses detail barang
                    $printer->text($barang->nama_barang . "\n"); // Nama barang
                    $printer->text($product->jumlah . " x Rp " . number_format($product->harga_jual) . " = Rp " . number_format($product->jumlah * $product->harga_jual) . "\n");
                }
                $printer->text("--------------------------------\n");

                // Total Harga dan Pembayaran
                $printer->text("Total: Rp " . number_format($transaksi->total_harga) . "\n");
                $printer->text("Bayar: Rp " . number_format($transaksi->total_bayar) . "\n");
                $printer->text("Kembalian: Rp " . number_format($transaksi->kembalian) . "\n");

                // Cetak Footer
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Terima kasih!\n");
                $printer->text("Please Come Again\n");

                // Potong kertas
                $printer->cut();

                // Tutup koneksi printer
                $printer->close();

                return response()->json(['success' => 'Struk berhasil dicetak']);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        // Fungsi untuk memeriksa ketersediaan printer
        private function isPrinterAvailable($printerName) {
            // Anda bisa menggunakan exec atau shell command untuk memeriksa printer di sistem
            $availablePrinters = shell_exec('wmic printer get name');
            return strpos($availablePrinters, $printerName) !== false;
        }



    public function transaksi(Request $request)
    {
        // Set default date to today if not provided
        $date = $request->input('date', date('Y-m-d'));

        // Base query for Transaksi_Barang
        $query = Transaksi_Barang::select(
            'barang_id',
            DB::raw('SUM(jumlah) as total_quantity'),
            DB::raw('MAX(harga_beli) as harga_beli'),
            DB::raw('MAX(harga_jual) as harga_jual'),
            // Calculate keuntungan_bersih per item
            DB::raw('SUM((harga_jual - harga_beli) * jumlah) as keuntungan_bersih')
        )
            ->groupBy('barang_id')
            ->whereDate('created_at', $date); // Filter by date

        $transaksi_barang = $query->get();

        // Query to calculate total_keuntungan_bersih across all items
        $total_keuntungan_query = Transaksi_Barang::select(
            DB::raw('SUM((harga_jual - harga_beli) * jumlah) as total_keuntungan_bersih')
        )
            ->whereDate('created_at', $date); // Filter by date

        $total_keuntungan_bersih = $total_keuntungan_query->value('total_keuntungan_bersih');

        return view('transaksi', compact('transaksi_barang', 'total_keuntungan_bersih'));
    }





    public function laporan(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        // Base query for Transaksi_Barang
        $subQuery = DB::table('transaksi_barang')
            ->select(
                'transaksi_id',
                DB::raw('SUM(jumlah * harga_jual) as total_pendapatan'),
                DB::raw('SUM(jumlah * harga_beli) as total_cost'),
                DB::raw('SUM((harga_jual - harga_beli) * jumlah) as keuntungan_bersih')
            )
            ->groupBy('transaksi_id');

        // Main query
        $laporans = DB::table('transaksi')
            ->leftJoinSub($subQuery, 'keuntungan', function ($join) {
                $join->on('transaksi.id', '=', 'keuntungan.transaksi_id');
            })
            ->select(
                DB::raw('DATE(transaksi.created_at) as date'),
                DB::raw('SUM(transaksi.total_harga) as total_harga'),
                DB::raw('SUM(transaksi.total_bayar) as total_bayar'),
                DB::raw('SUM(transaksi.kembalian) as kembalian'),
                DB::raw('SUM(IFNULL(keuntungan.keuntungan_bersih, 0)) as pendapatan_bersih')
            )
            ->whereBetween('transaksi.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(transaksi.created_at)'))
            ->orderBy('date')
            ->get();

        // Hitung total keseluruhan
        $totalHarga = $laporans->sum('total_harga');
        $totalBayar = $laporans->sum('total_bayar');
        $totalKembalian = $laporans->sum('kembalian');
        $totalPendapatanBersih = $laporans->sum('pendapatan_bersih');

        return view('laporankeuangan', compact('laporans', 'startDate', 'endDate', 'totalHarga', 'totalPendapatanBersih'));
    }



    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        return Excel::download(new LaporanExport($startDate, $endDate), 'Data Laporan Keuangan ' . $startDate . ' - ' . $endDate . '.xlsx');
    }
}
