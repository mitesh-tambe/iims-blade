<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\PurchaseItem;
use App\Models\StockMovements;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function generateRefNo()
    {
        $datePrefix = now()->format('dmy');

        $latestInvoice = Purchase::where('ref_no', 'like', $datePrefix . '%')
            ->orderByDesc('ref_no')
            ->first();

        if ($latestInvoice) {

            $lastNumber =
                (int) substr($latestInvoice->ref_no, -3);

            $newNumber = $lastNumber + 1;
        } else {

            $newNumber = 1;
        }

        return $datePrefix .
            str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $purchases = Purchase::with('vendor')
            ->where(function ($query) {
                $search = request('search');

                $query->where('invoice_no', 'like', '%' . $search . '%')
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $products = Product::all();
        // generate reference number
        $ref_no = $this->generateRefNo();
        return view('invoices.create', compact('vendors', 'products', 'ref_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([

    //         'invoice_no' => 'required|string|max:255',

    //         'purchase_date' => 'required|date',

    //         'vendor_id' => 'nullable|exists:vendors,id',

    //         'total_amount' => 'required|numeric|min:0',

    //         'ref_no' => 'required|string|max:255|unique:purchases,ref_no',

    //         'products' => 'required|array|min:1',

    //         'products.*.product_id' => 'required|exists:products,id',

    //         'products.*.quantity' => 'required|integer|min:1',

    //         'products.*.purchase_price' => 'required|numeric|min:0',

    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         // Create Purchase
    //         $purchase = Purchase::create([

    //             'vendor_id' => $validated['vendor_id'] ?? null,

    //             'invoice_no' => $validated['invoice_no'],

    //             'purchase_date' => $validated['purchase_date'] ?? now(),

    //             'total_amount' => $validated['total_amount'],

    //             'ref_no' => $validated['ref_no'],
    //         ]);

    //         // Purchase Items
    //         foreach ($validated['products'] as $item) {

    //             PurchaseItem::create([

    //                 'purchase_id' => $purchase->id,

    //                 'product_id' => $item['product_id'],

    //                 'quantity' => (int) $item['quantity'],

    //                 'cost_price' => (float) $item['purchase_price'],
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()
    //             ->route('invoices.index')
    //             ->with('success', 'Invoice created successfully.');
    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()
    //             ->withInput()
    //             ->with('error', $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'vendor_id' => 'nullable|exists:vendors,id',
            'total_amount' => 'required|numeric|min:0',
            'ref_no' => 'required|string|max:255|unique:purchases,ref_no',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            // Create Purchase
            $purchase = Purchase::create([
                'vendor_id' => $validated['vendor_id'] ?? null,
                'invoice_no' => $validated['invoice_no'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $validated['total_amount'],
                'ref_no' => $validated['ref_no'],
            ]);

            // Create Purchase Items + Stock Movements
            foreach ($validated['products'] as $item) {

                $quantity = (int) $item['quantity'];

                // Create Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'cost_price' => (float) $item['purchase_price'],
                ]);

                // Create Stock Movement
                StockMovements::create([
                    'product_id' => $item['product_id'],
                    'type' => 'purchase',
                    'quantity' => $quantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'remarks' => 'Purchase Invoice: ' . $purchase->invoice_no,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchase = Purchase::with([
            'vendor',
            'items.product'
        ])->findOrFail($id);

        // dd($purchase->toArray());
        return view('invoices.view', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        $vendors = Vendor::all();
        $products = Product::all();
        return view('invoices.edit', compact('purchase', 'vendors', 'products'));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'vendor_id' => 'nullable|exists:vendors,id',
            'total_amount' => 'required|numeric|min:0',

            'ref_no' => 'required|string|max:255|unique:purchases,ref_no,' . $id,

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            $purchase = Purchase::findOrFail($id);

            // Update Purchase
            $purchase->update([
                'vendor_id' => $validated['vendor_id'] ?? null,
                'invoice_no' => $validated['invoice_no'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $validated['total_amount'],
                'ref_no' => $validated['ref_no'],
            ]);

            /*
        |--------------------------------------------------------------------------
        | Remove old stock movements for this invoice only
        |--------------------------------------------------------------------------
        */

            StockMovements::where('reference_type', Purchase::class)
                ->where('reference_id', $purchase->id)
                ->where('type', 'purchase')
                ->delete();

            /*
        |--------------------------------------------------------------------------
        | Remove old purchase items
        |--------------------------------------------------------------------------
        */

            $purchase->items()->delete();

            /*
        |--------------------------------------------------------------------------
        | Create new purchase items + stock movements
        |--------------------------------------------------------------------------
        */

            foreach ($validated['products'] as $item) {

                // Create Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'cost_price' => (float) $item['purchase_price'],
                ]);

                // Create new Stock Movement
                StockMovements::create([
                    'product_id' => $item['product_id'],
                    'type' => 'purchase',
                    'quantity' => (int) $item['quantity'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'remarks' => 'Purchase Invoice: ' . $purchase->invoice_no,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            $filters = [];

            $map = [
                'search' => 'search',
                'page' => 'page',
            ];

            foreach ($map as $from => $to) {
                if ($request->filled($from)) {
                    $filters[$to] = $request->input($from);
                }
            }

            return redirect()
                ->route('invoices.index', $filters)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
