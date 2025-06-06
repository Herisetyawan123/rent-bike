<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_bike_id',
        'unit_id',
        'duration',
        'price',
        'description',
    ];

    public function bike()
    {
        return $this->belongsTo(RentBike::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function rentBike()
    {
        return $this->belongsTo(RentBike::class);
    }
}
