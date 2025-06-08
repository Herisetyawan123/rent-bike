<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractLatter extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'file_path',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class);
    }

}
