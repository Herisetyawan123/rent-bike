<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bike extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function addOns()
    {
        return $this->belongsToMany(AddOn::class, 'addon_bikes');
    }

    public function bikeMerk()
    {
        return $this->belongsTo(BikeMerk::class);
    }

    public function bikeType()
    {
        return $this->belongsTo(BikeType::class);
    }

    public function bikeColor()
    {
        return $this->belongsTo(BikeColor::class);
    }

    public function bikeCapacity()
    {
        return $this->belongsTo(BikeCapacity::class);
    }

    protected static function booted()
    {
        static::creating(function ($rentBike) {
            $id = Auth::user()->id;
            $rentBike->user_id = $id;
        });
    }
}
