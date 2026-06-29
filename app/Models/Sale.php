<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'invoice_no',
        'total_amount',
        'sale_date',
        'created_by',
        'payment_mode',
        'deleted_at'
    ];

    public static function generateInvoiceNo()
    {
        $prefix = strtoupper(now()->format('My'));

        $lastSale = self::where(
            'invoice_no',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('invoice_no')
            ->first();

        $next = $lastSale
            ? ((int) substr($lastSale->invoice_no, -4)) + 1
            : 1;

        return $prefix . str_pad(
            $next,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    protected static function booted()
    {
        static::creating(function ($sale) {
            $sale->invoice_no = self::generateInvoiceNo();
        });
    }

    protected $with = [
        'creator'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
