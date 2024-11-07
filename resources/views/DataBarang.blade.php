@extends('template.sidebar')

@section('content')
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">List Produk</h1>
            <a href="{{ route('barang.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tambah Produk
            </a>
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form id="search-form" action="{{ route('barang.search') }}" method="GET">
                <input type="text" name="barcode" id="search-barcode" autocomplete="off" placeholder="Search by Barcode" class="border rounded py-2 px-4 w-full" autofocus>
                <button type="submit" class="hidden">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">NO</th>
                        <th class="px-4 py-2 border-b">Nama Barang</th>
                        <th class="px-4 py-2 border-b">Gambar</th>
                        <th class="px-4 py-2 border-b">Barcode</th>
                        <th class="px-4 py-2 border-b">Harga Beli</th>
                        <th class="px-4 py-2 border-b">Harga Jual</th>
                        <th class="px-4 py-2 border-b">Stok</th>
                        <th class="px-4 py-2 border-b">Status</th>
                        <th class="px-4 py-2 border-b">Tanggal Buat</th>
                        <th class="px-4 py-2 border-b">Terakhir Update</th>
                        <th class="px-4 py-2 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataBarang as $barang)
                        <tr class="{{ $barang->stok < 10 ? 'bg-yellow-100' : '' }}">
                            <td class="px-4 py-2 border-b text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border-b text-center">{{ $barang->nama_barang }}</td>
                            <td class="px-4 py-2 border-b text-center">
                                <img src="{{ asset('storage/' . $barang->gambar) }}" alt="{{ $barang->nama_barang }}" class="w-16">
                            </td>
                            <td class="px-4 py-2 border-b text-center">{{ $barang->barcode }}</td>
                            <td class="px-4 py-2 border-b text-center">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }},00</td>
                            <td class="px-4 py-2 border-b text-center">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }},00</td>
                            <td class="px-4 py-2 border-b text-center">{{ $barang->stok }}</td>
                            <td class="px-4 py-2 border-b text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $barang->status == 'active' ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                                    {{ $barang->status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b text-center">{{ \Carbon\Carbon::parse($barang->created_at)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 border-b text-center">{{ \Carbon\Carbon::parse($barang->updated_at)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 border-b text-center">
                                <div class="flex items-center justify-center">
                                    <a href="/edit-barang-{{ $barang->id }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                        Edit
                                    </a>
                                    <a href="{{ route('barang.manage-stock', $barang->id) }}" class="text-green-500 hover:text-green-700 mr-2">
                                        Kelola Stok
                                    </a>
                                    <button onclick="confirmDelete({{ $barang->id }})" class="text-red-500 hover:text-red-700">
                                        Delete
                                    </button>
                                    <form id="delete-form-{{ $barang->id }}" action="{{ url('/delete-barang', $barang->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-2 border-b text-center">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include SweetAlert via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Auto-submit form when user types in the barcode field
            $('#search-barcode').on('input', function() {
                var barcode = $(this).val().trim();
                if (barcode !== '') {
                    $('#search-form').submit();
                }
            });

            // Confirm Delete Functionality
            window.confirmDelete = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form-' + id).submit();
                    }
                });
            };

            // Toast Notification
            @if (session('success'))
                showToast("{{ session('success') }}");
            @endif

            function showToast(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    </script>
@endsection
