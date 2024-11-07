@extends('template.sidebar')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white shadow-md rounded p-4">
            <div class="mb-4 flex space-x-4">
                <input type="text" placeholder="Scan Barcode..." id="barcode"
                    class="border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    autofocus readonly>
                <div id="barcode-error" class="text-red-500 mt-2"></div>
            </div>

            <div class="flex flex-col mb-4">
                <table class="min-w-full bg-white border">
                    <thead>
                        <tr>
                            <th class="py-2">Gambar</th>
                            <th class="py-2">Nama Produk</th>
                            <th class="py-2">Jumlah Beli</th>
                            <th class="py-2">Harga Satuan</th>
                            <th class="py-2">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="product-list">
                        <!-- Dynamic Rows Will Be Added Here -->
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mb-4">
                <span id="total-price">Total Harga: Rp 0.00</span>
            </div>

            <!-- Total Bayar and Kembalian -->
            <div class="mb-4">
                <label for="total_bayar" class="block text-gray-700">Bayar:</label>
                <input type="text" id="total_bayar"
                    class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                <div id="total-bayar-error" class="text-red-500 mt-2"></div>
            </div>
            <div class="mb-4">
                <label for="kembalian" class="block text-gray-700">Kembalian:</label>
                <input type="text" id="kembalian"
                    class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline" readonly>
            </div>
            <div class="mb-4">
                <div>
                    <button class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded mr-2">Batal</button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let barcode = '';
            let lastKeyTime = Date.now();
            let scannedBarcodes = [];

            $(document).on('keypress', function(e) {
                let currentTime = Date.now();
                if (currentTime - lastKeyTime > 100) {
                    barcode = '';
                }
                lastKeyTime = currentTime;

                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    if (barcode.length > 0) {
                        $('#barcode').val(barcode);
                        $('#barcode-error').hide();

                        let existingRow = $('#product-list tr[data-barcode="' + barcode + '"]');
                        if (existingRow.length > 0) {
                            let quantityInput = existingRow.find('.quantity-input');
                            let currentQuantity = parseInt(quantityInput.val().replace(/\./g, ''), 10) || 1;
                            quantityInput.val(formatNumber(currentQuantity + 1));
                            updateTotalPrice();
                        } else {
                            $.ajax({
                                url: '/scan-barang',
                                method: 'POST',
                                data: {
                                    barcode: barcode,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.product) {
                                        let price = response.product.price || 0;
                                        let quantity = response.quantity || 1;
                                        let formattedPrice = `Rp ${formatNumber(price)}`;

                                        let productRow = `<tr data-barcode="${barcode}">
                                        <td class="py-2 px-4 text-center flex items-center justify-center">
                                            <img src="{{ asset('storage') }}/${response.product.image}" class="border border-black border-opacity-30 rounded-lg w-16 h-16 object-cover">
                                        </td>
                                        <td class="py-2 px-4 text-center">${response.product.name}</td>
                                        <td class="py-2 px-4 text-center">
                                            <input type="number" value="${formatNumber(quantity)}" class="quantity-input border rounded py-1 px-2 text-center w-12" min="1">
                                            <button class="text-red-500 px-2 delete-row">🗑️</button>
                                        </td>
                                        <td class="py-2 px-4 text-center">${formattedPrice}</td>
                                        <td class="py-2 px-4 text-center">Rp ${formatNumber(price * quantity)}</td>
                                    </tr>`;

                                        $('#product-list').append(productRow);
                                        scannedBarcodes.push(barcode);
                                        updateTotalPrice();
                                    } else {
                                        $('#barcode-error').text('Product not found.').show();
                                    }

                                    $('#barcode').val('');
                                    barcode = '';
                                },
                                error: function(xhr) {
                                    let errorMessage =
                                        'Produk Tidak Terdaftar Atau Tidak Di Temukan.';
                                    if (xhr.status === 404) {
                                        errorMessage =
                                            'Produk Tidak Terdaftar Atau Tidak Di Temukan.';
                                    } else if (xhr.status === 400) {
                                        errorMessage = 'Stok Dari Produk Ini Sudah Habis.';
                                    }
                                    $('#barcode-error').text(errorMessage).show();
                                }
                            });
                        }
                    } else {
                        $('#barcode-error').text('Invalid barcode. Please scan again.').show();
                    }
                } else {
                    barcode += String.fromCharCode(e.which);
                }
            });

            $(document).on('click', '.delete-row', function() {
                let row = $(this).closest('tr');
                let barcodeToRemove = String(row.data('barcode')).trim();

                scannedBarcodes = scannedBarcodes.filter(barcode => String(barcode).trim() !==
                    barcodeToRemove);

                row.remove();
                updateTotalPrice();
            });

            $(document).on('input', '.quantity-input', function() {
                let quantity = parseInt($(this).val().replace(/\./g, ''), 10);
                if (isNaN(quantity) || quantity < 1) {
                    $(this).val(formatNumber(1));
                } else {
                    $(this).val(formatNumber(quantity));
                }
                updateTotalPrice();
            });

            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function updateTotalPrice() {
                let newTotal = 0;
                $('#product-list tr').each(function() {
                    let priceText = $(this).find('td:nth-child(4)').text().replace('Rp ', '').replace(/\./g,
                        '');
                    let price = parseInt(priceText, 10) || 0;
                    let quantity = parseInt($(this).find('.quantity-input').val().replace(/\./g, ''), 10) ||
                        1;
                    let totalPrice = price * quantity;
                    $(this).find('td:nth-child(5)').text(`Rp ${formatNumber(totalPrice)}`);
                    newTotal += totalPrice;
                });
                $('#total-price').text(`Total: Rp ${formatNumber(newTotal)}`);
                calculateKembalian();
            }

            function calculateKembalian() {
                let totalBayarText = $('#total_bayar').val().replace(/\./g, '');
                let totalBayar = parseInt(totalBayarText, 10) || 0;
                let totalPriceText = $('#total-price').text().replace('Total: Rp ', '').replace(/\./g, '');
                let totalPrice = parseInt(totalPriceText, 10) || 0;

                if (totalBayar >= totalPrice) {
                    let kembalian = totalBayar - totalPrice;
                    $('#kembalian').val(`Rp ${formatNumber(kembalian)}`);
                    $('#total-bayar-error').text('');
                } else {
                    $('#kembalian').val('Rp 0');
                    $('#total-bayar-error').text('Total Bayar tidak boleh kurang dari Total Harga.');
                }
            }

            $('#total_bayar').on('input', function() {
                let value = $(this).val().replace(/\./g, '');
                if (!isNaN(value) && value.length > 0) {
                    $(this).val(formatNumber(parseInt(value, 10)));
                }
                calculateKembalian();
            });

            $(document).on('click', '.bg-red-500', function() {
                $('#barcode').val('');
                $('#total_bayar').val('');
                $('#total-price').text('Total Harga: Rp 0.00');
                $('#kembalian').val('Rp 0');
                $('#product-list').empty();
                scannedBarcodes = [];
                $('#total-bayar-error').hide();
            });

            $('button.bg-blue-500').click(function() {
                if (scannedBarcodes.length === 0) {
                    $('#barcode-error').text(
                        'Silahkan scan setidaknya satu produk sebelum melakukan submit.').show();
                    return;
                } else {
                    $('#barcode-error').hide();
                }

                let totalBayar = parseInt($('#total_bayar').val().replace(/\./g, ''), 10) || 0;
                if (totalBayar === 0) {
                    $('#total-bayar-error').text('Silahkan masukkan jumlah pembayaran sebelum menyimpan transaksi.').show();
                    return;
                } else {
                    $('#total-bayar-error').hide();
                }

                let products = [];
                $('#product-list tr').each(function() {
                    let product = {
                        name: $(this).find('td:nth-child(2)').text(),
                        quantity: parseInt($(this).find('input').val(), 10) || 1,
                        price: parseInt($(this).find('td:nth-child(4)').text().replace('Rp ',
                            '').replace(/\./g, ''), 10) || 0
                    };
                    products.push(product);
                });

                let kembalian = parseInt($('#kembalian').val().replace('Rp ', '').replace(/\./g, ''), 10) ||
                    0;

                $.ajax({
                    url: '/submit-order',
                    method: 'POST',
                    data: {
                        products: products,
                        total_bayar: totalBayar,
                        kembalian: kembalian,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Panggil fungsi cetak struk setelah transaksi berhasil
                            console.log(response.transaksi_id);
                            printReceipt(response.transaksi_id);
                            // showReceiptModal(response.transaksi_id);

                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Berhasil',
                                text: 'Barang telah berhasil dibeli.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                $('#barcode').val('');
                                $('#product-list').empty();
                                $('#total-price').text('Total: Rp 0');
                                scannedBarcodes = [];
                                $('#total_bayar').val('');
                                $('#kembalian').val('');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Transaksi Gagal',
                                text: 'Gagal memproses transaksi, coba lagi.',
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memproses transaksi.',
                        });
                    }
                });
            });

            function printReceipt(orderId) {
                $.ajax({
                    url: '/print-struk/' + orderId, // Panggil endpoint cetak struk
                    method: 'GET',
                    success: function(response) {
                        console.log('Struk berhasil dicetak');
                    },
                    error: function(xhr) {
                        console.error('Gagal mencetak struk: ' + xhr.responseJSON.error);
                    }
                });
            }

       
    });
    </script>
@endsection
