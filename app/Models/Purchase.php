<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'total_amount',
        'purchase_date'
    ];
}
