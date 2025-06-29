<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractClause extends Model
{
    protected $guarded = ["id"];

    public function vendor()
    {
        return $this->belongsTo(User::class);
    }
}
