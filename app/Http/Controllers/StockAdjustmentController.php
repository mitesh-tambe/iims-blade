<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockAdjustment;
use App\Models\StockMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            $product->available_stock = (int) (
                $product->stock_movements_sum_quantity ?? 0
            );
        });

        $adjustmentNo = 'ADJ-' . str_pad(
            (StockAdjustment::max('id') ?? 0) + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        return view('stock-adjustments.create', [
            'products' => $products,
            // 'purchases' => $purchases,
            'adjustment_no' => $adjustmentNo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => [
                'required',
                'exists:purchases,id',
            ],
            'adjustment_date' => 'required|date',
            'product_id' => [
                'required',
                Rule::exists('purchase_items', 'product_id')
                    ->where(function ($query) use ($request) {
                        $query->where('purchase_id', $request->purchase_id);
                    }),
            ],
            'adjustment_type' => 'required|in:add,remove,price_change',
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf(
                    in_array($request->adjustment_type, ['add', 'remove'])
                ),
            ],
            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(
                    $request->adjustment_type === 'price_change'
                ),
            ],
            'reason' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // =========================================================
            // GET THE EXACT PRODUCT FROM THE SELECTED PURCHASE
            // =========================================================
            $purchaseItem = PurchaseItem::where('purchase_id', $validated['purchase_id'])
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if (!$purchaseItem) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', 'The selected product does not belong to the selected invoice.');
            }

            // =========================================================
            // PRICE CHANGE
            // =========================================================
            if ($validated['adjustment_type'] === 'price_change') {

                // ---------------------------------------------------------
                // Create stock adjustment record
                // ---------------------------------------------------------
                $adjustment = StockAdjustment::create([
                    'adjustment_no' => 'TEMP-' . uniqid(),
                    'purchase_id' => $validated['purchase_id'],
                    'product_id' => $validated['product_id'],
                    'type' => 'price_change',
                    'adjustment_date' => $validated['adjustment_date'],
                    'quantity' => null,
                    'reason' => $validated['reason'],
                    'remarks' => $validated['remarks'] ?? null,
                    'unit_cost' => $validated['unit_cost'],
                    'created_by' => auth()->id(),
                ]);

                // ---------------------------------------------------------
                // Generate adjustment number
                // ---------------------------------------------------------
                $adjustment->update([
                    'adjustment_no' => 'ADJ-' . str_pad(
                        $adjustment->id,
                        6,
                        '0',
                        STR_PAD_LEFT
                    ),
                ]);

                // ---------------------------------------------------------
                // Update product MRP
                // ---------------------------------------------------------
                $product = Product::find($validated['product_id']);

                if (!$product) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with('error', 'Selected product was not found.');
                }

                $product->update([
                    'mrp' => $validated['unit_cost'],
                ]);

                // ---------------------------------------------------------
                // Create stock movement record
                // ---------------------------------------------------------
                StockMovements::create([
                    'product_id' => $validated['product_id'],
                    'type' => 'price_change',
                    'quantity' => null,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'remarks' => 'Price Change: ' . $adjustment->adjustment_no,
                    'created_by' => auth()->id(),
                ]);

                Purchase::whereKey($validated['purchase_id'])->update([
                    'status' => 'edited',
                ]);

                DB::commit();

                return redirect()
                    ->route('stock-adjustments.index')
                    ->with('success', 'Product price changed successfully.');
            }

            $requestedQuantity = (int) $validated['quantity'];

            // =========================================================
            // CURRENT STOCK OF THIS PRODUCT
            // =========================================================
            $currentStock = (int) StockMovements::where(
                'product_id',
                $validated['product_id']
            )->sum('quantity');

            // =========================================================
            // PREVIOUS ADJUSTMENTS FOR THIS PURCHASE + PRODUCT
            // =========================================================
            $previousAdjustmentQuantity = (int) StockAdjustment::where(
                'purchase_id',
                $validated['purchase_id']
            )
                ->where('product_id', $validated['product_id'])
                ->sum('quantity');

            // =========================================================
            // CORRECTED QUANTITY FOR THIS PURCHASE
            //
            // Example:
            // Original purchase = 10
            // Previous adjustment = -2
            // Corrected quantity = 8
            //
            // Example:
            // Original purchase = 10
            // Previous adjustment = +3
            // Corrected quantity = 13
            // =========================================================
            $correctedPurchaseQuantity =
                (int) $purchaseItem->quantity + $previousAdjustmentQuantity;

            // =========================================================
            // ADD STOCK
            //
            // ADD is allowed even when current stock is 0.
            // =========================================================
            if ($validated['adjustment_type'] === 'add') {
                $quantity = $requestedQuantity;
            }

            // =========================================================
            // REMOVE STOCK
            // =========================================================
            else {
                // -----------------------------------------------------
                // Current stock cannot become negative
                // -----------------------------------------------------
                if ($currentStock <= 0) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Cannot remove stock. Current available stock is 0.'
                        );
                }

                // -----------------------------------------------------
                // Cannot remove more than current physical stock
                // -----------------------------------------------------
                if ($requestedQuantity > $currentStock) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "Cannot remove {$requestedQuantity} units. Current available stock is {$currentStock}."
                        );
                }

                // -----------------------------------------------------
                // Cannot correct this purchase below zero
                // -----------------------------------------------------
                if ($requestedQuantity > $correctedPurchaseQuantity) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "Cannot remove {$requestedQuantity} units. The corrected quantity for this invoice is {$correctedPurchaseQuantity}."
                        );
                }

                $quantity = -$requestedQuantity;
            }

            // =========================================================
            // CREATE STOCK ADJUSTMENT
            // =========================================================
            $adjustment = StockAdjustment::create([
                'adjustment_no' => 'TEMP-' . uniqid(),
                'purchase_id' => $validated['purchase_id'],
                'product_id' => $validated['product_id'],
                'type' => $validated['adjustment_type'],
                'adjustment_date' => $validated['adjustment_date'],
                'quantity' => $quantity,
                'reason' => $validated['reason'],
                'remarks' => $validated['remarks'] ?? null,
                'unit_cost' => $validated['unit_cost'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // =========================================================
            // GENERATE ADJUSTMENT NUMBER
            // =========================================================
            $adjustment->update([
                'adjustment_no' => 'ADJ-' . str_pad(
                    $adjustment->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            // =========================================================
            // CREATE STOCK MOVEMENT
            // =========================================================
            StockMovements::create([
                'product_id' => $validated['product_id'],
                'type' => 'adjustment',
                'quantity' => $quantity,
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adjustment->id,
                'remarks' => 'Stock Adjustment: ' . $adjustment->adjustment_no,
                'created_by' => auth()->id(),
            ]);

            Purchase::whereKey($validated['purchase_id'])->update([
                'status' => 'edited',
            ]);

            DB::commit();

            return redirect()
                ->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Error: ' . $e->getMessage()
                );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stockAdjustment = StockAdjustment::with(['product', 'creator'])
            ->findOrFail($id);

        // dd($stockAdjustment);
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
                'type' => 'adjustment',
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
        $request->validate([
            'q' => 'nullable|string|max:255',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $search = trim($request->input('q', ''));
        $purchaseId = $request->input('purchase_id');

        $products = Product::query()

            // Get only products belonging to the selected invoice
            ->whereIn('id', function ($query) use ($purchaseId) {
                $query->select('product_id')
                    ->from('purchase_items')
                    ->where('purchase_id', $purchaseId);
            })

            // Product search
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('book_name', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhere('barcode_no', 'like', "%{$search}%");
                });
            })

            // Current stock
            ->withSum('stockMovements', 'quantity')

            ->orderBy('book_name')
            ->limit(20)
            ->get();

        // Add available_stock
        $products->each(function ($product) {
            $product->available_stock = (int) (
                $product->stock_movements_sum_quantity ?? 0
            );
        });

        return response()->json($products);
    }

    public function purchaseSearch(Request $request)
    {
        $search = $request->get('q');
        $purchases = Purchase::query()
            ->with('vendor:id,name')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhere('ref_no', 'like', '%' . $search . '%');
                });
            })
            ->select([
                'id',
                'vendor_id',
                'invoice_no',
                'purchase_date',
                'ref_no',
            ])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json(
            $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'invoice_no' => $purchase->invoice_no,
                    'purchase_date' => $purchase->purchase_date?->format('d-m-Y'),
                    'ref_no' => $purchase->ref_no,
                    'vendor_name' => $purchase->vendor?->name,
                ];
            })
        );
    }
}
