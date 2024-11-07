<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //
    public function registerForm(){
        return view('auth.register');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'level' => ['required', 'string', 'in:admin,kasir,kepsek'], // adjust roles as needed
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ],
        'password_confirmation' => ['required', 'same:password'],
    ], [
        'name.required' => 'Nama harus diisi.',
        'level.required' => 'Level pengguna harus dipilih.',
        'level.in' => 'Level pengguna tidak valid.',
        'email.required' => 'Alamat email harus diisi.',
        'email.email' => 'Format alamat email tidak valid.',
        'email.unique' => 'Alamat email sudah terdaftar.',
        'password.required' => 'Password harus diisi.',
        'password.min' => 'Password harus minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password.regex' => 'Password harus memiliki huruf besar, huruf kecil, angka, dan karakter khusus.',
        'password_confirmation.required' => 'Konfirmasi password harus diisi.',
        'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
    ]);

    try {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->level
        ]);

        // Optional: You can automatically log in the user after registration
        // Auth::login($user);

        return redirect('/login')->with('success', 'Registrasi berhasil. Silakan login dengan akun baru Anda.');
    } catch (\Exception $e) {
        Log::error('Registration error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
    }
}

    public function loginForm(){
        return view('auth.login');
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if the "Remember Me" checkbox is checked
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) { // Pass the $remember variable
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You are logged in!');
        }

        return back()->with('error', 'The provided credentials were incorrect.');
    }


    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->gambar) {
                Storage::delete('public/avatars/' . $user->gambar);
            }

            // Simpan foto baru
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('public/avatars', $fileName);

            // Update user
            $user->gambar = $fileName;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'string'],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'different:current_password',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ],
    ], [
        'new_password.regex' => 'Password harus memiliki minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan karakter khusus.',
        'new_password.different' => 'Password baru harus berbeda dengan password saat ini.',
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Password saat ini tidak cocok'])->withInput();
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    Auth::logoutOtherDevices($request->new_password);

    return redirect()->back()->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.');
}
}
