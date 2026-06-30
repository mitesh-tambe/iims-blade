<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'invoice_no',
        'total_amount',
        'purchase_date',
        'ref_no'
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
