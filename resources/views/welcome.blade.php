@extends('template.sidebar')

@section('content')

    <div class="relative h-screen bg-cover bg-center" style="background-image: url('{{ asset('/storage/images/skajuang.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div> <!-- Layer gelap untuk gambar background -->

        <div class="fixed inset-0 flex flex-col items-center justify-center h-full">
            <!-- Glass Effect Container -->
            <div class="bg-white/10 backdrop-blur-sm p-10 rounded-xl shadow-lg border border-gray-400 text-center max-w-xl w-full">
                <h1 class="text-4xl font-bold text-white mb-4">Selamat Datang di SKAJU-MART</h1>
                <p class="text-lg text-gray-200">
                    SKAJU-MART menawarkan berbagai macam produk dengan harga terjangkau. Kunjungi kami untuk mendapatkan penawaran menarik.
                </p>

                @if(Auth::check() && Auth::user()->role == 'kasir')
                <button class="mt-6 px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow hover:bg-yellow-600">
                    <a href="/order-barang">
                        Tambah Transaksi
                    </a>
                </button>
                @endif
            </div>
        </div>
    </div>

@endsection
