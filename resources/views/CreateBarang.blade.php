@extends('template.sidebar')
@section('content')
<div class="w-full mx-auto p-8">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 flex justify-center">Add New Product</h2>

        <form id="createBarangForm" action="{{ url('/create-new-barang') }}" method="POST" enctype="multipart/form-data" class="w-full">
            @csrf

            <!-- Barcode Scanner -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="barcode">Barcode</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="barcode" type="text" name="barcode" value="{{ old('barcode') }}" required autofocus readonly>
                @error('barcode')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
                <!-- Error Message for Barcode Scanner -->
                <p id="barcode-error" class="text-red-500 text-xs italic mt-2" style="display: none;"></p>
            </div>

            <!-- Product Name -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama Barang</label>
                <input class="shadow uppercase appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="name" type="text" name="nama_barang" value="{{ old('name') }}" required>
                @error('nama_barang')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="harga_beli">Harga Beli</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="harga_beli" type="text" name="harga_beli" value="{{ old('harga_beli') }}" required>
                @error('harga_beli')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="harga_jual">Harga Jual</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="harga_jual" type="text" name="harga_jual" value="{{ old('harga_jual') }}" required>
                @error('harga_jual')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="stok">Stok</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="stok" type="number" name="stok" min="0" value="{{ old('stok') }}" required>
                @error('stok')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Gambar Barang</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="image" type="file" name="image" accept="image/*">
                @error('image')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <label class="inline-flex items-center">
                    <input type="radio" class="form-radio" name="status" value="active" checked>
                    <span class="ml-2">Active</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" class="form-radio" name="status" value="inactive">
                    <span class="ml-2">Inactive</span>
                </label>
                @error('status')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button id="btnSubmit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Add Product
                </button>
                <button type="button" id="btn_clear_text" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-4">
                    Clear Barcode Text
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        let barcode = '';
        let lastKeyTime = Date.now();

        $(document).on('keypress', function(e) {
            let currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) {
                barcode = '';
            }
            lastKeyTime = currentTime;

            if (e.which === 13) { // Enter key to process the barcode
                if (barcode.length > 0) {
                    $('#barcode').val(barcode);
                    $('#barcode-error').hide();
                    console.log(`Barcode scanned: ${barcode}`);
                } else {
                    $('#barcode-error').text('Invalid barcode. Please scan again.').show();
                }
                barcode = '';
            } else {
                barcode += String.fromCharCode(e.which);
            }
        });

        $('#btn_clear_text').click(function () {
            $('#barcode').val("").focus();
            $('#barcode-error').hide();
        });

        function formatCurrencyInput(input) {
            input.on('input', function() {
                let value = $(this).val().replace(/\./g, '');
                if (!isNaN(value) && value.length > 0) {
                    value = parseInt(value);
                    $(this).val(value.toLocaleString('id-ID'));
                } else {
                    $(this).val('');
                }
            }).on('blur', function() {
                let value = $(this).val().replace(/\./g, '');
                if (!isNaN(value) && value.length > 0) {
                    $(this).val(parseInt(value).toLocaleString('id-ID'));
                }
            });
        }

        // Terapkan fungsi formatCurrencyInput ke elemen dengan ID 'harga_beli' dan 'harga_jual'
        formatCurrencyInput($('#harga_beli'));
        formatCurrencyInput($('#harga_jual'));

        $('#btnSubmit').on('click', function(e) {
            e.preventDefault();

            // Menghapus format ribuan sebelum mengirimkan form
            $('#harga_beli, #harga_jual').each(function() {
                let plainValue = $(this).val().replace(/\./g, '');
                $(this).val(plainValue);
            });

            let stok = parseInt($('#stok').val().replace(/\./g, ''));
            if (stok < 0) {
                Swal.fire({
                    title: 'Invalid Input!',
                    text: "Stok tidak boleh kurang dari 0.",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to add this product.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#createBarangForm').submit();
                }
            });
        });

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @elseif(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>

@endsection
