<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $guarded = ['id'];

    public function bikes()
    {
        return $this->belongsToMany(Bike::class, 'addon_bikes');
    }
}
