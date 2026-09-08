<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\Publication;
use App\Models\Rack;
use Illuminate\Http\Request;

class FindIdController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = $request->input('search_by', 'product');

        $results = collect();

        if ($search) {

            switch ($searchBy) {

                case 'product':

                    $results = Product::where('book_name', 'like', "%{$search}%")
                        ->get();

                    break;


                case 'author':

                    $results = Author::where('name', 'like', "%{$search}%")
                        ->get();

                    break;


                case 'category':

                    $results = Category::where('name', 'like', "%{$search}%")
                        ->get();

                    break;


                case 'publication':

                    $results = Publication::where('name', 'like', "%{$search}%")
                        ->get();

                    break;


                case 'rack':

                    $results = Rack::where('name', 'like', "%{$search}%")
                        ->get();

                    break;
                
                case 'language':

                    $results = Language::where('name', 'like', "%{$search}%")
                        ->get();

                    break;
            }
        }

        return view('find-id', compact(
            'results',
            'search',
            'searchBy'
        ));
    }
}
