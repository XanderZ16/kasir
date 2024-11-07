@extends('template.sidebar')
@section('content')
<div class="w-full mx-auto p-8">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 flex justify-center">Edit Product</h2>

        <form id="edit-form" action="{{ url('/edit-barang', $barang->id) }}" method="POST" enctype="multipart/form-data" class="w-full">
            @csrf
            @method('PUT')

            <!-- Barcode Scanner -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="barcode">Barcode</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="barcode" type="text" name="barcode" value="{{ old('barcode', $barang->barcode) }}" required autofocus readonly>
                @error('barcode')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
                <p id="barcode-error" class="text-red-500 text-xs italic mt-2" style="display: none;"></p>
            </div>

            <!-- Product Name -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama Barang</label>
                <input class="shadow appearance-none border rounded uppercase w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="name" type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                @error('nama_barang')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Beli -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="harga_beli">Harga Beli</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="harga_beli" type="text" name="harga_beli" value="{{ old('harga_beli', number_format($barang->harga_beli, 0, ',', '.')) }}" required>
                @error('harga_beli')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga Jual -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="harga_jual">Harga Jual</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="harga_jual" type="text" name="harga_jual" value="{{ old('harga_jual', number_format($barang->harga_jual, 0, ',', '.')) }}" required>
                @error('harga_jual')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="stok">Stok</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="stok" type="number" name="stok" min="0" value="{{ old('stok', $barang->stok) }}" required>
                @error('stok')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Gambar Barang</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="image" type="file" name="image" accept="image/*" onchange="previewImage(event)">
                @error('image')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror

                <div id="image-preview" class="mt-2"></div>

                @if($barang->gambar)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$barang->gambar) }}" alt="Current Image" class="w-32 h-32 object-cover">
                        <p class="text-sm text-gray-600">Current Image</p>
                    </div>
                @endif
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <label class="inline-flex items-center">
                    <input type="radio" class="form-radio" name="status" value="active" {{ $barang->status == 'active' ? 'checked' : '' }}>
                    <span class="ml-2">Active</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" class="form-radio" name="status" value="inactive" {{ $barang->status == 'inactive' ? 'checked' : '' }}>
                    <span class="ml-2">Inactive</span>
                </label>
                @error('status')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Product
                </button>
                <a href="{{ url('/data-barang') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-4">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>

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
                e.preventDefault();
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

        $('#harga_beli, #harga_jual').on('input', function() {
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

         // Validasi stok tidak boleh negatif
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

        $('form').on('submit', function(event) {
            event.preventDefault();
            let hargaBeliField = $('#harga_beli');
            let hargaJualField = $('#harga_jual');
            let valueBeli = hargaBeliField.val().replace(/\./g, '');
            let valueJual = hargaJualField.val().replace(/\./g, '');

            if (!isNaN(valueBeli) && !isNaN(valueJual)) {
                hargaBeliField.val(valueBeli);
                hargaJualField.val(valueJual);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to save the changes?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            }
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('image-preview');
                output.innerHTML = `<img src="${reader.result}" alt="Image Preview" class="w-32 h-32 object-cover mt-2">`;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection
