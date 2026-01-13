<?php

namespace App\Models;

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
}
