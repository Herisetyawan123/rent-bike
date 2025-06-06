<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    protected $fillable = [
        'user_id',
        'national_id',
        'driver_license_number',
        'gender',
        'ethnicity',
        'nationality',
        'birth_date',
        'address',
        'current_address',
        'marital_status',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
