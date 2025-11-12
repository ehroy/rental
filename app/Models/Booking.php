<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'nama_pemesan',
        'nomor_wa',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'total_harga',
        'catatan',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
