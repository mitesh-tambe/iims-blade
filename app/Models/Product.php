<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'book_name',
        'isbn',
        'edition',
        'book_pages',
        'barcode_no',
        'author_id',
        'publication_id',
        'language_id',
        'category_id',
        'mrp',
        'disc_from_company',
        'disc_for_customer',
        'amt_company',
        'amt_customer',
        'rack_no',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // 🔍 SEARCH
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('book_name', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('barcode_no', 'like', "%{$search}%");

                $q->orWhereHas(
                    'author',
                    fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                );

                $q->orWhereHas(
                    'publication',
                    fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                );

                $q->orWhereHas(
                    'language',
                    fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                );

                $q->orWhereHas(
                    'category',
                    fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                );
            });
        });

        // 🎯 SIMPLE ID FILTERS
        foreach (['author_id', 'publication_id', 'category_id'] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        // 📦 RACK FILTER (0-safe)
        if (
            array_key_exists('rack_no', $filters) &&
            $filters['rack_no'] !== '' &&
            $filters['rack_no'] !== null
        ) {
            $query->where('rack_no', $filters['rack_no']);
        }

        return $query;
    }
}
