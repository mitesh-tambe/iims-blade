<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::with([
            'author',
            'publication',
            'language',
            'category'
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Product fields
                    $q->where('book_name', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhere('barcode_no', 'like', "%{$search}%");

                    // Relations
                    $q->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('publication', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('language', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            })

            // ✅ FILTERS (ADDED)
            ->when($request->author_id, function ($q) use ($request) {
                $q->where('author_id', $request->author_id);
            })
            ->when($request->publication_id, function ($q) use ($request) {
                $q->where('publication_id', $request->publication_id);
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->when($request->has('rack_no'), function ($q) use ($request) {
                $q->where('rack_no', $request->rack_no);
            })

            ->paginate(10)
            ->withQueryString();

        // ✅ FILTER DATA (ADDED)
        return view('products.index', [
            'products'   => $products,
            'authors'    => Author::orderBy('name')->get(),
            'publications'  => Publication::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'racks' => Product::whereNotNull('rack_no')
                ->select('rack_no')
                ->distinct()
                ->orderBy('rack_no')
                ->pluck('rack_no')
                ->map(fn($rack) => trim((string) $rack)),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'authors' => Author::orderBy('name')->get(),
            'publications' => Publication::orderBy('name')->get(),
            'languages' => Language::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $mrp = (float) $request->mrp;
    //     $amtCompany = round(($mrp * (float) $request->disc_from_company) / 100);
    //     $amtCustomer = round(($mrp * (float) $request->disc_for_customer) / 100);

    //     Product::create([
    //         'book_name'           => $request->book_name,
    //         'isbn'                => $request->isbn,
    //         'edition'             => $request->edition,
    //         'book_pages'          => $request->book_pages,
    //         'barcode_no'          => $request->barcode_no,
    //         'author_id'           => $request->author_id,
    //         'publication_id'      => $request->publication_id,
    //         'language_id'         => $request->language_id,
    //         'category_id'         => $request->category_id,
    //         'mrp'                 => $request->mrp,
    //         'disc_from_company'   => $request->disc_from_company,
    //         'amt_company'         => $amtCompany,
    //         'disc_for_customer'   => $request->disc_for_customer,
    //         'amt_customer'        => $amtCustomer,
    //     ]);

    //     return redirect()
    //         ->route('products.index')
    //         ->with('success', 'Product created successfully.');
    // }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'book_name'         => 'required|string|max:255',
                'isbn'              => 'nullable|string|max:255',
                'edition'           => 'nullable|string|max:50',
                // 'book_pages'        => 'nullable|integer',
                'book_pages' => 'required|string|max:10',
                'barcode_no'        => 'nullable|string|max:255',
                'author_id'         => 'required|exists:authors,id',
                'publication_id'    => 'required|exists:publications,id',
                'language_id'       => 'required|exists:languages,id',
                'category_id'       => 'required|exists:categories,id',
                'mrp'               => 'required|numeric|min:0',
                'disc_from_company' => 'nullable|numeric|min:0|max:100',
                'disc_for_customer' => 'nullable|numeric|min:0|max:100|lte:disc_from_company',
                'rack_no'          => 'required|string|max:100',
            ],
            [
                'disc_for_customer.lte' =>
                'Customer discount cannot be greater than company discount.',
            ]
        );

        $validated = $validator->validated();

        // ✅ Calculations (unchanged)
        $mrp = (float) $validated['mrp'];

        $amtCompany = round(($mrp * (float) $validated['disc_from_company']) / 100, 2);
        $amtCustomer = round(($mrp * (float) $validated['disc_for_customer']) / 100, 2);

        // ✅ Create product
        Product::create([
            'book_name'           => $validated['book_name'],
            'isbn'                => $validated['isbn'] ?? null,
            'edition'             => $validated['edition'] ?? null,
            'book_pages'          => $validated['book_pages'] ?? null,
            'barcode_no'          => $validated['barcode_no'] ?? null,
            'author_id'           => $validated['author_id'],
            'publication_id'      => $validated['publication_id'],
            'language_id'         => $validated['language_id'],
            'category_id'         => $validated['category_id'],
            'mrp'                 => $mrp,
            'disc_from_company'   => $validated['disc_from_company'],
            'amt_company'         => $amtCompany,
            'disc_for_customer'   => $validated['disc_for_customer'],
            'amt_customer'        => $amtCustomer,
            'rack_no'             => $validated['rack_no'],
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', [
            'product'       => $product,
            'authors'       => Author::orderBy('name')->get(),
            'publications'  => Publication::orderBy('name')->get(),
            'languages'     => Language::orderBy('name')->get(),
            'categories'    => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'book_name'            => ['required', 'string', 'max:255'],
            'isbn'                 => ['nullable', 'string', 'max:100'],
            'edition'              => ['nullable', 'integer', 'min:1'],
            'book_pages'           => ['required', 'string', 'max:10'],
            'barcode_no'           => ['nullable', 'string', 'max:100'],
            'mrp'                  => ['required', 'numeric', 'min:0'],
            'disc_from_company'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'amt_company'          => ['nullable', 'numeric', 'min:0'],
            'disc_for_customer'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'amt_customer'         => ['nullable', 'numeric', 'min:0'],
            'author_id'            => ['required', 'exists:authors,id'],
            'publication_id'       => ['required', 'exists:publications,id'],
            'language_id'          => ['required', 'exists:languages,id'],
            'category_id'          => ['required', 'exists:categories,id'],
            'rack_no'              => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $mrp = (float) $validated['mrp'];
            if (empty($validated['amt_company'])) {
                $validated['amt_company'] = round(
                    ($mrp * (float) $validated['disc_from_company']) / 100
                );
            }

            if (empty($validated['amt_customer'])) {
                $validated['amt_customer'] = round(
                    ($mrp * (float) $validated['disc_for_customer']) / 100
                );
            }

            $product->update([
                'book_name'          => $validated['book_name'],
                'isbn'               => $validated['isbn'],
                'edition'            => $validated['edition'],
                'book_pages'         => $validated['book_pages'],
                'barcode_no'         => $validated['barcode_no'],
                'mrp'                => $validated['mrp'],
                'disc_from_company'  => $validated['disc_from_company'],
                'amt_company'        => $validated['amt_company'],
                'disc_for_customer'  => $validated['disc_for_customer'],
                'amt_customer'       => $validated['amt_customer'],
                'author_id'          => $validated['author_id'],
                'publication_id'     => $validated['publication_id'],
                'language_id'        => $validated['language_id'],
                'category_id'        => $validated['category_id'],
                'rack_no'            => $validated['rack_no'],
            ]);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
