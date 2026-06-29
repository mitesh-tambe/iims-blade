<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Author;
use App\Models\Publication;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = Sale::select('id', 'invoice_no', 'total_amount', 'sale_date')->latest()->get();
        // dd($sales);
        return view('dashboard', [
            'productsCount'     => Product::count(),
            'authorsCount'      => Author::count(),
            'publicationsCount' => Publication::count(),
            'categoriesCount'   => Category::count(),
            'racksCount'         => Rack::count(),
            'sales' => $sales,
            'invoiceNo'         => Sale::generateInvoiceNo(),
        ]);
    }
}
