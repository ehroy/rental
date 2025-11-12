<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'harga_sewa_perhari',
        'is_available',
        'category_id'
    ];

    protected $casts = [
        'harga_sewa_perhari' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Check apakah produk tersedia di tanggal tertentu
    public function isAvailableOnDate($startDate, $endDate, $excludeBookingId = null)
    {
        $query = $this->bookings()
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('tanggal_mulai', '<=', $startDate)
                         ->where('tanggal_selesai', '>=', $endDate);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    // Get booked dates
    public function getBookedDates()
    {
        return $this->bookings()
            ->where('status', '!=', 'rejected')
            ->get()
            ->map(function ($booking) {
                return [
                    'start' => $booking->tanggal_mulai,
                    'end' => $booking->tanggal_selesai,
                    'status' => $booking->status,
                ];
            });
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
   
    
}