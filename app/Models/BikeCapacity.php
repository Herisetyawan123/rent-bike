<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeCapacity extends Model
{
    protected $guarded = ['id'];
    public function bikes()
    {
        return $this->hasMany(Bike::class, 'bike_capacity_id', 'id');
    }
}
