<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'contact_person_name',
        'tax_id',
        'business_address',
        'latitude',
        'longitude',
        'phone',
        'photo_attachment',
        'national_id',
        'legal_documents',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
