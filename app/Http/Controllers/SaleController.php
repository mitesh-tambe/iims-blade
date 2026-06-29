<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits_between:1,12',

            // 'invoice_no' => 'required|string|unique:sales,invoice_no',
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.net_amount' => 'required|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use ($validated) {

            $customerId = null;

            // create customer if name or phone entered
            if (
                !empty($validated['name']) ||
                !empty($validated['phone'])
            ) {

                $customer = Customer::create([
                    'name' => $validated['name'] ?? '',
                    'phone' => $validated['phone'] ?? '',
                ]);

                $customerId = $customer->id;
            }
            $sale = Sale::create([
                'customer_id' => $customerId,
                // 'invoice_no' => $validated['invoice_no'],
                'total_amount' => $validated['total_amount'],
                'payment_mode' => $validated['payment_mode'],
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
                    'discount' => $product['discount'] ?? 0,
                    'net_amount' => $product['net_amount'],
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
        $sale->load([
            'customer',
            'saleItems.product'
        ]);

        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits_between:1,12',
            // 'invoice_no' => 'required|string|unique:sales,invoice_no,' . $sale->id,
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.net_amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $sale) {

            $customerId = null;

            // create new customer if entered
            if (
                !empty($validated['name']) ||
                !empty($validated['phone'])
            ) {

                $customer = Customer::create([
                    'name' => $validated['name'] ?? '',
                    'phone' => $validated['phone'] ?? '',
                ]);

                $customerId = $customer->id;
            }
            $sale->update([
                'customer_id' => $customerId,
                // 'invoice_no' => $validated['invoice_no'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $validated['total_amount'],
                'payment_mode' => $validated['payment_mode'],
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
                    'discount' => $product['discount'] ?? 0,
                    'net_amount' => $product['net_amount'],
                ]);
            }
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Bill updated successfully')
            ->with('print_bill', route('bill.test', $sale->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }
}
