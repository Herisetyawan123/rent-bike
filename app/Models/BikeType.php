<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeType extends Model
{
    protected $guarded = ['id'];

    public function merk()
    {
        return $this->belongsTo(BikeMerk::class, 'bike_merk_id', 'id');
    }

    public function bikes()
    {
        return $this->hasMany(Bike::class);
    }
}
