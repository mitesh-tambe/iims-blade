<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_no',
        'total_amount',
        'sale_date',
    ];
}
