<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $id = 'id';

    protected $fillable = [
        'total_harga',
        'total_bayar',
        'kembalian',
    ];

    // Define the relationship with Barang through Transaksi_Barang
    public function transaksiBarang()
    {
        return $this->hasMany(Transaksi_Barang::class, 'transaksi_id');
    }

}
