<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'multiplier'];

    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function parentUnit()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }
}
