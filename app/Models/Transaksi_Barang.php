<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi_Barang extends Model
{
    use HasFactory;

    protected $table = 'transaksi_barang';
    protected $id = 'id';

    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'quantity',
        'harga_beli',
        'harga_jual',
    ];

    // Optional: You can define relationships back to Transaksi and Barang if needed
    public function barang()
{
    return $this->belongsTo(Barang::class, 'barang_id');
}

public function transaksi()
{
    return $this->belongsTo(Transaksi::class, 'transaksi_id');
}

}
