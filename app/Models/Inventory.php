<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'current_stock',
        'critical_level',
        'rack_no',
        'status',
    ];
}
