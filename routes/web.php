<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout' , [AuthController::class , 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {

    // Hanya Admin yang bisa mengakses halaman barang
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/data-barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/create-new-barang', [BarangController::class, 'CreateBarangForm'])->name('barang.create');
        Route::post('/create-new-barang', [BarangController::class, 'CreateBarang']);
        Route::delete('/delete-barang/{id}', [BarangController::class, 'DeleteBarang']);
        Route::get('/edit-barang-{id}', [BarangController::class, 'EditBarangForm'])->name('barang.edit');
        Route::put('/edit-barang/{id}', [BarangController::class, 'EditBarang']);
        Route::get('/search-barcode', [BarangController::class, 'searchByBarcode'])->name('barang.search');
        Route::get('/barang/{id}/manage-stock', [BarangController::class, 'manageStock'])->name('barang.manage-stock');
    Route::put('/barang/{id}/update-stock', [BarangController::class, 'updateStock'])->name('barang.update-stock');
    });

    // Admin dan Kasir bisa mengakses halaman transaksi
    Route::middleware(['role:kasir'])->group(function () {
        Route::get('/get-price', [TransaksiController::class, 'getPrice']);
        Route::get('/order-barang', function () {
            return view('OrderBarang');
        })->name('order.index');
        Route::post('/scan-barang', [TransaksiController::class, 'store']);
        Route::post('/submit-order', [TransaksiController::class, 'SubmitOrder']);
        Route::get('/transaksi', [TransaksiController::class, 'transaksi'])->name('transaksi');
        Route::get('/print-struk/{id}', [TransaksiController::class, 'printStruk']);
        Route::get('/get-receipt/{id}', [TransaksiController::class, 'getReceipt']);
    });


    // Admin, Kasir, dan Bos bisa mengakses halaman laporan
    Route::middleware(['role:admin,kepsek'])->group(function () {
        Route::get('/laporan-keuangan', [TransaksiController::class, 'laporan'])->name('laporan');
        Route::get('/export-excel', [TransaksiController::class, 'exportExcel'])->name('export.excel');
    });

    // Dashboard hanya bisa diakses oleh Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('role:admin');

    // Halaman utama
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/profile' , function(){
        return view('auth.profile');
    })->name('profile');

    Route::put('/profile/update-photo', [AuthController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::put('/profile/update-info', [AuthController::class, 'updateInfo'])->name('profile.update.info');
    Route::put('/profile/update-password', [AuthController::class, 'updatePassword'])->name('profile.update.password');

});
