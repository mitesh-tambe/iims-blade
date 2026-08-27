<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adjustments = StockAdjustment::with(['product', 'creator'])
            ->latest()
            ->paginate(10);

        return view('stock-adjustments.index', compact('adjustments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::whereIn('id', function ($query) {
            $query->select('product_id')
                ->from('purchase_items');
        })
            ->withSum('stockMovements', 'quantity')
            ->orderBy('book_name')
            ->get();

        $products->each(function ($product) {
            $product->available_stock = (int) ($product->stock_movements_sum_quantity ?? 0);
        });

        $adjustmentNo = 'ADJ-' . str_pad(
            (StockAdjustment::max('id') ?? 0) + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        return view('stock-adjustments.create', [
            'products' => $products,
            'adjustment_no' => $adjustmentNo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'product_id' => [
                'required',
                'exists:purchase_items,product_id',
            ],
            'adjustment_type' => 'required|in:add,remove',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {

            $currentStock = StockMovements::where(
                'product_id',
                $validated['product_id']
            )->sum('quantity');

            $currentStock = (int) $currentStock;

            $quantity = (int) $validated['quantity'];

            if ($validated['adjustment_type'] === 'remove') {

                // Cannot remove more stock than currently available
                if ($quantity > $currentStock) {
                    return back()
                        ->withInput()
                        ->with('error', "Cannot remove {$quantity} units. Available stock is {$currentStock}.");
                }

                $quantity = -$quantity;
            }

            $adjustment = StockAdjustment::create([
                'adjustment_no' => 'TEMP-' . uniqid(),
                'product_id' => $validated['product_id'],
                'type' => $validated['adjustment_type'],
                'adjustment_date' => $validated['adjustment_date'],
                'quantity' => $quantity,
                'reason' => $validated['reason'],
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $adjustment->update([
                'adjustment_no' => 'ADJ-' . str_pad(
                    $adjustment->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            StockMovements::create([
                'product_id' => $validated['product_id'],
                'type' => 'adjustment',
                'quantity' => $quantity,
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adjustment->id,
                'remarks' => 'Stock Adjustment: ' . $adjustment->adjustment_no,
                'created_by' => auth()->id(),
            ]);
            DB::commit();
            return redirect()
                ->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment created successfully.');
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
    public function show(string $id)
    {
        $stockAdjustment = StockAdjustment::with(['product', 'creator'])
            ->findOrFail($id);
        return view('stock-adjustments.view', compact('stockAdjustment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $adjustment = StockAdjustment::findOrFail($id);

            // Find the stock movement created by this adjustment
            $stockMovement = StockMovements::where('reference_type', StockAdjustment::class)
                ->where('reference_id', $adjustment->id)
                ->first();

            if (!$stockMovement) {
                throw new \Exception('Stock movement for this adjustment was not found.');
            }

            /*
         * Reverse the original stock movement.
         *
         * Example:
         * Add +5  -> delete adjustment => -5
         * Remove -5 -> delete adjustment => +5
         */
            StockMovements::create([
                'product_id' => $adjustment->product_id,
                'type' => 'adjustment_reversal',
                'quantity' => -$stockMovement->quantity,
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adjustment->id,
                'remarks' => 'Reversal of Stock Adjustment: ' . $adjustment->adjustment_no,
                'created_by' => auth()->id(),
            ]);

            // Delete the original adjustment record
            $adjustment->delete();

            DB::commit();

            return redirect()
                ->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment deleted successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function productSearch(Request $request)
    {
        $search = trim($request->q);

        if (!$search) {
            return response()->json([]);
        }

        $products = Product::whereIn('id', function ($query) {
            $query->select('product_id')
                ->from('purchase_items');
        })
            ->where(function ($query) use ($search) {
                $query->where('book_name', 'LIKE', "%{$search}%")
                    ->orWhere('isbn', 'LIKE', "%{$search}%")
                    ->orWhere('barcode_no', 'LIKE', "%{$search}%");
            })
            ->select(
                'id',
                'book_name',
                'isbn',
                'barcode_no'
            )
            ->limit(20)
            ->get();

        $products->each(function ($product) {

            $product->available_stock = StockMovements::where(
                'product_id',
                $product->id
            )->sum('quantity');
        });

        return response()->json($products);
    }
}
