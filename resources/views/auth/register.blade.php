<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white">
    <div class="min-h-svh flex justify-center items-center">
        <form action="{{ route('register') }}" method="POST" class="w-full max-w-sm mx-auto">
            @csrf
            <div class="pb-5 relative">
                <label for="name" class="block">Name</label>
                <input type="text" name="name" placeholder="Name" id="name" value="{{ old('name') }}" class="w-full p-2 bg-slate-400 border @error('name') border-red-500 @enderror rounded-lg shadow-lg">
                @error('name')
                    <p class="absolute bottom-0 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pb-5 relative">
                <label for="email" class="block">Email</label>
                <input type="email" name="email" placeholder="Email" id="email" value="{{ old('email') }}" class="w-full p-2 bg-slate-400 border @error('email') border-red-500 @enderror rounded-lg shadow-lg">
                @error('email')
                    <p class="absolute bottom-0 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pb-5 relative">
                <label for="level" class="block">Level</label>
                <select name="level" class="w-full p-2 bg-slate-400 border @error('level') border-red-500 @enderror rounded-lg shadow-lg" id="level">
                    <option value="">Select a role</option>
                    <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ old('level') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="kepsek" {{ old('level') == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                </select>
                @error('level')
                    <p class="absolute bottom-0 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pb-5 relative">
                <label for="password" class="block">Password</label>
                <div class="relative">
                    <input type="password" name="password" placeholder="Password" id="password" class="w-full p-2 bg-slate-400 border @error('password') border-red-500 @enderror rounded-lg shadow-lg pr-10">
                    <div class="absolute right-2 inset-y-0 flex items-center">
                        <button type="button" id="togglePassword" class="focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                                <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('password')
                    <p class="absolute bottom-0 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pb-5 relative">
                <label for="password_confirmation" class="block">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" id="password_confirmation" class="w-full p-2 bg-slate-400 border rounded-lg shadow-lg">
            </div>

            <button type="submit" class="w-full p-2 bg-slate-800 rounded-lg shadow-lg">Register</button>
            <p class="mt-2 text-sm text-center">
                Already have an account? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login</a>
            </p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle the icon
                this.querySelector('svg').innerHTML = type === 'password'
                    ? '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>'
                    : '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>';
            });
        });
    </script>
</body>

</html>
