<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bike extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function addOns()
    {
        return $this->belongsToMany(AddOn::class, 'addon_bikes')
        ->withPivot('price')
                ->withTimestamps();
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
        // static::creating(function ($rentBike) {
        //     $id = auth()->id();
        //     $rentBike->user_id = $id;
        // });
    }
}
