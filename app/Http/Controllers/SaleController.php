<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::where('invoice_no', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:sales,invoice_no',
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use ($validated) {

            $sale = Sale::create([
                'invoice_no' => $validated['invoice_no'],
                'total_amount' => $validated['total_amount'],
                'sale_date' => $validated['sale_date'] ?? now(),
                'created_by' => Auth::id(),
            ]);

            $products = Product::whereIn('id', collect($validated['products'])
                ->pluck('product_id'))
                ->get()
                ->keyBy('id');

            foreach ($validated['products'] as $product) {
                $selectedProduct = $products[$product['product_id']];
                $sale->saleItems()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'selling_price' => $product['purchase_price'],
                    'mrp' => $selectedProduct->mrp,
                ]);
            }

            return $sale;
        });

        return redirect()->route('bill.test', $sale->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return redirect()->route('bill.test', $sale->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $sale->load('saleItems.product');

        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:sales,invoice_no,' . $sale->id,
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $sale) {

            $sale->update([
                'invoice_no' => $validated['invoice_no'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $validated['total_amount'],
            ]);

            $sale->saleItems()->delete();

            $products = Product::whereIn(
                'id',
                collect($validated['products'])->pluck('product_id')
            )->get()->keyBy('id');

            foreach ($validated['products'] as $product) {

                $selected = $products[$product['product_id']];

                $sale->saleItems()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'selling_price' => $product['purchase_price'],
                    'mrp' => $selected->mrp,
                ]);
            }
        });

        return redirect()->route('bill.test', $sale->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
