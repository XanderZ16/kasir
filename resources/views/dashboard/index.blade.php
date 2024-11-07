@extends('template.sidebar')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white p-4">
        <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

        <!-- Date Range Filter -->
        <form action="{{ route('dashboard.index') }}" method="GET" class="py-4">
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start_date" class="block text-gray-700">Start Date:</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ request('start_date') }}"
                           class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-gray-700">End Date:</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ request('end_date') }}"
                           class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Filter</button>
            </div>
        </form>

        <!-- Dashboard Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-4">
            <!-- Total Sales -->
            <div class="bg-green-100 border border-green-300 rounded-lg p-4 shadow-md">
                <h3 class="text-lg font-medium text-green-700">Total Sales</h3>
                <p class="text-2xl text-green-800 mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>

            <!-- Total Bayar -->
            <div class="bg-blue-100 border border-blue-300 rounded-lg p-4 shadow-md">
                <h3 class="text-lg font-medium text-blue-700">Total Bayar</h3>
                <p class="text-2xl text-blue-800 mt-2">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
            </div>

            <!-- Total Kembalian -->
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 shadow-md">
                <h3 class="text-lg font-medium text-red-700">Total Kembalian</h3>
                <p class="text-2xl text-red-800 mt-2">Rp {{ number_format($totalKembalian, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Top Products Sold -->
        <div class="py-8">
            <h3 class="text-lg font-medium">Top Products Sold</h3>
            <canvas id="top-products-chart"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('top-products-chart').getContext('2d');
        const topProductsData = @json($topProducts);

        const labels = topProductsData.map(item => item.name);
        const data = topProductsData.map(item => item.quantity);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Top Products Sold',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
