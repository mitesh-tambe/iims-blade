<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovements extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'remarks',
        'created_by'
    ];
}
