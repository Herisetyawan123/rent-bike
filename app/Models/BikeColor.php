<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeColor extends Model
{
    

    protected $guarded = ['id'];

    public function bikeColor()
    {
        return $this->belongsTo(BikeColor::class);
    }

    public function bikes()
    {
        return $this->hasMany(Bike::class);
    }
}
