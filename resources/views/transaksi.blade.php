@extends('template.sidebar')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Barang Terjual</h2>

        <!-- Date Filter Form -->
        <form method="GET" action="{{ route('transaksi') }}" class="mb-4">
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="border rounded py-2 px-3">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Filter</button>
        </form>

        <div class="mb-4">
            <h1>

                Data Transaksi Tanggal:
                {{ date('Y-m-d') }}
            </h1>
            <h3 class="text-lg font-semibold">Total Keuntungan Bersih
                : Rp {{ number_format($total_keuntungan_bersih, 0, ',', '.') }}</h3>
        </div>

        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4 border-b">No</th>
                    <th class="py-2 px-4 border-b">Barang</th>
                    <th class="py-2 px-4 border-b">Jumlah</th>
                    <th class="py-2 px-4 border-b">Keuntungan Bersih</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($transaksi_barang as $transaksi)
                    <tr>
                        <td class="text-center py-2 px-4 border-b">{{ $loop->iteration }}</td>
                        <td class="py-2 px-4 border-b">
                            <!-- Adjust according to actual field names if necessary -->
                            <div class="flex items-center">
                                <img src="{{ asset('storage/' . $transaksi['barang']['gambar']) }}"
                                    alt="{{ $transaksi['barang']['nama_barang'] }}" class="w-16 h-16 object-cover mr-4">
                                <div>
                                    <div class="font-semibold">{{ $transaksi['barang']['nama_barang'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaksi['barang']['barcode'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-2 px-4 border-b">{{ $transaksi['total_quantity'] }}</td>
                        <td class="text-center py-2 px-4 border-b">
                            Rp {{ number_format($transaksi['keuntungan_bersih'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
