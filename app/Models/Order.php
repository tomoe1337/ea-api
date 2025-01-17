<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = false;
    protected $casts = [
        'data' => 'json',
    ];
}
