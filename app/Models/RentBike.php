<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentBike extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'brand',
        'model',
        'year',
        'license_plate',
        'color',
        'rental_price_per_day',
        'availability_status',
        'photo',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($rentBike) {
            $rentBike->user_id = auth()->id();
        });
    }
}
