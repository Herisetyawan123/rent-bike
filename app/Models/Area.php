<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $guarded = ['id'];

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'area_id', 'id');
    }
}
