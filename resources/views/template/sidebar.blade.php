<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
        href="https://static.vecteezy.com/system/resources/previews/020/269/527/original/cashier-icon-for-your-website-design-logo-app-ui-free-vector.jpg">
    <title>Point Of Sales</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins';
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="fixed z-10 left-0 w-64 bg-[#353a40] h-screen flex flex-col">
        <!-- Logo -->
        <div class="flex items-center justify-center border-b border-white mx-4 border-opacity-30 h-16 bg-[#353a40]">
            <a href="/" class="text-white text-lg font-semibold">POS Kasir</a>
        </div>

        <!-- User Profile -->
        <a href="/profile"
            class="flex items-center bg-[#353a40] hover:rounded-lg hover:bg-gray-600 py-2 border-b border-white mx-4 border-opacity-30">
            @if (Auth::user()->gambar)
                <img src="{{ '/storage/avatars/' . Auth::user()->gambar }}" alt="User Avatar"
                    class="w-10 h-10 rounded-full object-cover">
            @else
                <img class="w-10 h-10 rounded-full" src="https://tse2.mm.bing.net/th?id=OIP.EhKlVZLzgrF0kHjzodRdIAHaHa&pid=Api&P=0&h=220" alt="User Avatar">
            @endif

            <div class="ml-3">
                <p class="text-white font-bold text-lg uppercase">{{ Auth::user()->name }}</p>
            </div>

        </a>

        <!-- Navigation -->
        <nav class="flex-1 px-2 space-y-2 mt-5">
            @if (Auth::check() && Auth::user()->role == 'admin')
                <a href="/dashboard"
                    class="flex items-center px-2 py-2 text-sm font-medium text-gray-300 @if (Route::currentRouteName() == 'dashboard.index') bg-gray-700 @else hover:bg-gray-600 @endif @if (Route::currentRouteName() == 'dashboard.index') text-white @else text-gray-300 @endif hover:text-white rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3" width="16" height="16"
                        fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z" />
                        <path fill-rule="evenodd"
                            d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3" />
                    </svg>
                    Dashboard
                </a>
            @endif

            @if (Auth::check() && Auth::user()->role == 'admin')
                <a href="/data-barang"
                    class="flex items-center px-2 py-2 text-sm font-medium @if (Route::currentRouteName() == 'barang.index' ||
                            Route::currentRouteName() == 'barang.create' ||
                            Route::currentRouteName() == 'barang.edit') text-white @else text-gray-300 @endif @if (Route::currentRouteName() == 'barang.index' ||
                            Route::currentRouteName() == 'barang.search' ||
                            Route::currentRouteName() == 'barang.create' ||
                            Route::currentRouteName() == 'barang.edit') bg-gray-700 @else hover:bg-gray-600 @endif hover:text-white rounded-md">
                    <svg class="w-6 h-6 mr-3" width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1 2.5C1 1.67157 1.67157 1 2.5 1L5.5 1C6.32843 1 7 1.67157 7 2.5V5.5C7 6.32843 6.32843 7 5.5 7H2.5C1.67157 7 1 6.32843 1 5.5L1 2.5ZM9 2.5C9 1.67157 9.67157 1 10.5 1L13.5 1C14.3284 1 15 1.67157 15 2.5V5.5C15 6.32843 14.3284 7 13.5 7H10.5C9.67157 7 9 6.32843 9 5.5V2.5ZM1 10.5C1 9.67157 1.67157 9 2.5 9H5.5C6.32843 9 7 9.67157 7 10.5V13.5C7 14.3284 6.32843 15 5.5 15H2.5C1.67157 15 1 14.3284 1 13.5L1 10.5ZM9 10.5C9 9.67157 9.67157 9 10.5 9H13.5C14.3284 9 15 9.67157 15 10.5V13.5C15 14.3284 14.3284 15 13.5 15H10.5C9.67157 15 9 14.3284 9 13.5V10.5Z"
                            fill="#D9D9D9" />
                    </svg>

                    Produk
                </a>
            @endif

            @if (Auth::check() && Auth::user()->role == 'kasir')
                <a href="/order-barang"
                    class="flex items-center px-2 py-2 text-sm font-medium @if (Route::currentRouteName() == 'order.index') text-white @else text-gray-300 @endif @if (Route::currentRouteName() == 'order.index') bg-gray-700 @else hover:bg-gray-600 @endif hover:text-white rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3" width="16" height="16"
                        fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                        <path
                            d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                        <path
                            d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                    </svg>
                    Kasir
                </a>
            @endif

            @if (Auth::check() && Auth::user()->role == 'kasir')
                <a href="/transaksi"
                    class="flex items-center px-2 py-2 text-sm font-medium @if (Route::currentRouteName() == 'transaksi') text-white @else text-gray-300 @endif @if (Route::currentRouteName() == 'transaksi') bg-gray-700 @else hover:bg-gray-600 @endif text-gray-300 hover:text-white rounded-md">
                    <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h18M3 8h18M3 13h18M3 18h18" />
                    </svg>
                    Transaksi
                </a>
            @endif

            @if ((Auth::check() && Auth::user()->role == 'admin') || Auth::user()->role == 'kepsek')
                <a href="/laporan-keuangan"
                    class="flex items-center px-2 py-2 text-sm font-medium @if (Route::currentRouteName() == 'laporan') text-white @else text-gray-300 @endif @if (Route::currentRouteName() == 'laporan') bg-gray-700 @else hover:bg-gray-600 @endif text-gray-300 hover:text-white rounded-md">
                    <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                        <path
                            d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                        <path
                            d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                    </svg>
                    Laporan Keuangan
                </a>
            @endif

            <a href="/profile"
                    class="flex items-center px-2 py-2 text-sm font-medium @if (Route::currentRouteName() == 'profile') text-white @else text-gray-300 @endif @if (Route::currentRouteName() == 'profile') bg-gray-700 @else hover:bg-gray-600 @endif text-gray-300 hover:text-white rounded-md">
                    <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                        <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                      </svg>
                    Pengaturan
                </a>

            <div class="px-2 mb-4 bottom-5 left-3 absolute">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-red-700 hover:bg-red-600 hover:text-white rounded-md">
                        <svg class="w-6 h-6 mr-1" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13H12.59L10.29 15.29C10.1963 15.383 10.1219 15.4936 10.0711 15.6154C10.0203 15.7373 9.9942 15.868 9.9942 16C9.9942 16.132 10.0203 16.2627 10.0711 16.3846C10.1219 16.5064 10.1963 16.617 10.29 16.71C10.383 16.8037 10.4936 16.8781 10.6154 16.9289C10.7373 16.9797 10.868 17.0058 11 17.0058C11.132 17.0058 11.2627 16.9797 11.3846 16.9289C11.5064 16.8781 11.617 16.8037 11.71 16.71L15.71 12.71C15.801 12.6149 15.8724 12.5028 15.92 12.38C16.02 12.1365 16.02 11.8635 15.92 11.62C15.8724 11.4972 15.801 11.3851 15.71 11.29L11.71 7.29C11.6168 7.19676 11.5061 7.1228 11.3842 7.07234C11.2624 7.02188 11.1319 6.99591 11 6.99591C10.8681 6.99591 10.7376 7.02188 10.6158 7.07234C10.4939 7.1228 10.3832 7.19676 10.29 7.29C10.1968 7.38324 10.1228 7.49393 10.0723 7.61575C10.0219 7.73757 9.99591 7.86814 9.99591 8C9.99591 8.13186 10.0219 8.26243 10.0723 8.38425C10.1228 8.50607 10.1968 8.61676 10.29 8.71L12.59 11H5C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12V12ZM17 2H7C6.20435 2 5.44129 2.31607 4.87868 2.87868C4.31607 3.44129 4 4.20435 4 5V8C4 8.26522 4.10536 8.51957 4.29289 8.70711C4.48043 8.89464 4.73478 9 5 9C5.26522 9 5.51957 8.89464 5.70711 8.70711C5.89464 8.51957 6 8.26522 6 8V5C6 4.73478 6.10536 4.48043 6.29289 4.29289C6.48043 4.10536 6.73478 4 7 4H17C17.2652 4 17.5196 4.10536 17.7071 4.29289C17.8946 4.48043 18 4.73478 18 5V19C18 19.2652 17.8946 19.5196 17.7071 19.7071C17.5196 19.8946 17.2652 20 17 20H7C6.73478 20 6.48043 19.8946 6.29289 19.7071C6.10536 19.5196 6 19.2652 6 19V16C6 15.7348 5.89464 15.4804 5.70711 15.2929C5.51957 15.1054 5.26522 15 5 15C4.73478 15 4.48043 15.1054 4.29289 15.2929C4.10536 15.4804 4 15.7348 4 16V19C4 19.7956 4.31607 20.5587 4.87868 21.1213C5.44129 21.6839 6.20435 22 7 22H17C17.7956 22 18.5587 21.6839 19.1213 21.1213C19.6839 20.5587 20 19.7956 20 19V5C20 4.20435 19.6839 3.44129 19.1213 2.87868C18.5587 2.31607 17.7956 2 17 2Z"
                                        fill="white" />
                                </svg>
                        Logout
                    </button>
                </form>
            </div>


        </nav>
    </div>

    <!-- Main Content -->
    <main class="pl-64">
        @yield('content')
    </main>
</body>

</html>
