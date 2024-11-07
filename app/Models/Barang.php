<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $id = 'id';

    protected $fillable = [
        'nama_barang',
        'gambar',
        'barcode',
        'harga_beli',
        'harga_jual',
        'stok',
        'status',
    ];

    // Define the relationship with Transaksi through Transaksi_Barang
    public function transaksiBarang()
{
    return $this->hasMany(Transaksi_Barang::class, 'barang_id');
}

}
