<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Author;
use App\Models\Publication;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'productsCount'     => Product::count(),
            'authorsCount'      => Author::count(),
            'publicationsCount' => Publication::count(),
            'categoriesCount'   => Category::count(),
        ]);
    }
}

