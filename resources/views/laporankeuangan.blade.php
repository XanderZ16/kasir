@extends('template.sidebar')


@section('content')
    <div class="container mx-auto p-4">

        <button id="export-excel-btn" class="bg-blue-500 text-white p-2 rounded-lg absolute bottom-5 right-5">Export to Excel</button>

        <h1 class="text-2xl font-bold mb-4">Laporan Keuangan</h1>

        {{-- {{json_encode($laporans)}} --}}
        <!-- Date Filter Form -->
        <form method="GET" action="{{ route('laporan') }}" class="mb-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="start_date" class="block text-gray-700">Tanggal Mulai:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                        class="mt-1 block w-full border p-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-gray-700">Tanggal Selesai:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                        class="mt-1 block w-full border p-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md">Filter</button>
                </div>
            </div>
        </form>

        <!-- Table to Display Data -->
        <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b">Date</th>
                    <th class="py-2 px-4 border-b">Total Belanja</th>
                    {{-- <th class="py-2 px-4 border-b">Bayar</th> --}}
                    {{-- <th class="py-2 px-4 border-b">Kembalian</th> --}}
                    <th class="py-2 px-4 border-b">Pendapatan Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $laporan)
                    <tr>
                        <td class="py-2 px-4 border-b text-center">
                            {{ \Carbon\Carbon::parse($laporan->date)->format('j F Y') }}
                        </td>
                        <td class="py-2 px-4 border-b text-center">Rp
                            {{ number_format($laporan->total_harga, 0, ',', '.') }}</td>
                        {{-- <td class="py-2 px-4 border-b text-center">Rp
                            {{ number_format($laporan->total_bayar, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 border-b text-center">Rp {{ number_format($laporan->kembalian, 0, ',', '.') }}
                        </td> --}}
                        <td class="py-2 px-4 border-b text-center">Rp
                            {{ number_format($laporan->pendapatan_bersih, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-2 px-4 border-b text-center">No data available for the selected date
                            range.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-t">TOTAL</th>
                    <th class="py-2 px-4 border-t text-center">Rp {{ number_format($totalHarga, 0, ',', '.') }}</th>
                    {{-- <th class="py-2 px-4 border-t text-center">Rp {{ number_format($totalBayar, 0, ',', '.') }}</th> --}}
                    {{-- <th class="py-2 px-4 border-t text-center">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</th> --}}
                    <th class="py-2 px-4 border-t text-center">Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#export-excel-btn').on('click', function() {
    window.location.href = "{{ route('export.excel') }}";
});

</script>

@endsection
