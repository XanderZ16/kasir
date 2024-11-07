@extends('template.sidebar')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
            <!-- Foto Pengguna Section -->
            <div class="bg-white shadow-md rounded-lg p-6 md:w-1/4">
                <h2 class="text-xl font-semibold mb-4">Foto Pengguna</h2>
                <div class="mb-4">
                    @if (Auth::user()->gambar)
                        <img id="imgPreview" src="{{ '/storage/avatars/' . Auth::user()->gambar }}" alt="User Avatar"
                            class="w-full aspect-square object-cover bg-gray-200 rounded-lg">
                    @else
                        <img id="imgPreview" src="https://i.pinimg.com/736x/cb/45/72/cb4572f19ab7505d552206ed5dfb3739.jpg"
                            class="w-full aspect-square bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 object-cover">
                        </img>
                    @endif
                </div>
                <form id="photoForm" action="{{ route('profile.update.photo') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-700">Choose File</label>
                        <input onchange="previewImage(event)" type="file" id="photo" name="photo"
                            class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-100 file:text-blue-700
                        hover:file:bg-blue-200">
                        <p class="mt-1 text-sm text-gray-500">No file chosen</p>
                    </div>
                    <button type="button" onclick="confirmPhotoUpdate()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200 ease-in-out">
                        Ganti Foto
                    </button>
                </form>
            </div>

            <!-- Kelola Pengguna Section -->
            <div class="bg-white shadow-md rounded-lg p-6 md:w-2/5">
                <h2 class="text-xl font-semibold mb-4">Kelola Pengguna</h2>
                <form id="userInfoForm" action="{{ route('profile.update.info') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="nama" name="name" value="{{ Auth::user()->name }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <input type="text" id="role" name="role" value="{{ Auth::user()->role }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700"
                            readonly>
                    </div>
                    <button type="button" onclick="confirmUserUpdate()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200 ease-in-out">
                        Ubah Profil
                    </button>
                </form>
            </div>

            <!-- Ganti Password Section -->
            <div class="bg-white shadow-md rounded-lg p-6 md:w-1/3">
                <h2 class="text-xl font-semibold mb-4">Ganti Password</h2>
                <form id="passwordForm" action="{{ route('profile.update.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" value="{{ Auth::user()->name }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700"
                            readonly>
                    </div>
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat
                            Ini</label>
                        <div class="relative">
                            <input type="password" name="current_password" placeholder="Enter Your Password"
                                id="current_password"
                                class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700 @error('current_password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('#current_password')"
                                class="absolute right-2 inset-y-0 flex items-center cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-eye-slash" viewBox="0 0 16 16">
                                    <path
                                        d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                                    <path
                                        d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                                    <path
                                        d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password"
                                placeholder="Enter Your New Password"
                                class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700 @error('new_password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('#new_password')"
                                class="absolute right-2 inset-y-0 flex items-center cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                    <path
                                        d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                                    <path
                                        d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                                    <path
                                        d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                                </svg>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                            Password Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                placeholder="Confirm Your New Password"
                                class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-opacity-50 py-2 px-4 bg-gray-50 text-gray-700">
                            <button type="button" onclick="togglePassword('#new_password_confirmation')"
                                class="absolute right-2 inset-y-0 flex items-center cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                    <path
                                        d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                                    <path
                                        d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                                    <path
                                        d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="confirmPasswordUpdate()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200ease-in-out">
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imgPreview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function togglePassword(inputId) {
            const passwordInput = document.querySelector(inputId);
            const toggleButton = passwordInput.nextElementSibling;
            const eyeIcon = toggleButton.querySelector('svg');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>';
            }
        }

        function confirmPhotoUpdate() {
            Swal.fire({
                title: 'Konfirmasi Update Foto',
                text: "Apakah Anda yakin ingin memperbarui foto profil?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, perbarui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('photoForm').submit();
                }
            });
        }

        function confirmUserUpdate() {
            Swal.fire({
                title: 'Konfirmasi Update Profil',
                text: "Apakah Anda yakin ingin memperbarui informasi profil?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, perbarui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('userInfoForm').submit();
                }
            });
        }

        function confirmPasswordUpdate() {
            Swal.fire({
                title: 'Konfirmasi Update Password',
                text: "Apakah Anda yakin ingin mengubah password?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('passwordForm').submit();
                }
            });
        }
    </script>
@endsection
