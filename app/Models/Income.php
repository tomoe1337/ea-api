<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $guarded = false;
    protected $casts = [
        'data' => 'json',
    ];
}
