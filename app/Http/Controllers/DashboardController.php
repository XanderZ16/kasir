<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Transaksi_Barang;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the date range from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // If no dates are provided, set the default range to all-time
        if (!$startDate || !$endDate) {
            $startDate = Transaksi::min('created_at');
            $endDate = now();
        }

        // Convert dates to Carbon instances for comparisons
        $startDate = \Carbon\Carbon::parse($startDate);
        $endDate = \Carbon\Carbon::parse($endDate);

        // Total sales, total bayar, and total kembalian for the specified date range
        $totalSales = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        $totalBayar = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_bayar');

        $totalKembalian = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->sum('kembalian');

        // Top 5 products sold in the specified date range
        $topProducts = Transaksi_Barang::select('barang_id', DB::raw('SUM(jumlah) as total_quantity'))
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_barang.transaksi_id')
            ->whereBetween('transaksi.created_at', [$startDate, $endDate])
            ->groupBy('barang_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)  // Limit to top 5 products
            ->get()
            ->map(function($item) {
                return [
                    'name' => Barang::find($item->barang_id)->nama_barang,
                    'quantity' => $item->total_quantity,
                ];
            });

        return view('dashboard.index', compact('totalSales', 'totalBayar', 'totalKembalian', 'topProducts'));
    }



}

