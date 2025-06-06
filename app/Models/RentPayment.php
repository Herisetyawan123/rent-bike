<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    protected $guarded = ['id'];

    public function renter()
    {
        return $this->belongsTo(Renter::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function package()
    {
        return $this->belongsTo(BikePackage::class, 'package_id');
    }
}
