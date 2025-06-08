<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeMerk extends Model
{
    protected $guarded = ['id'];

    public function bikes()
    {
        return $this->hasMany(Bike::class);
    }
}
