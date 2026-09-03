<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\StockMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter;

class StockMovementsController extends Controller
{

    private function getReportQuery(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'level' => 'nullable|in:normal,critical',
        ]);

        $search = trim($validated['search'] ?? '');

        $stockQuery = StockMovements::query()
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as available_stock'),
                DB::raw('MAX(created_at) as latest_movement_date')
            )
            ->groupBy('product_id');

        return PurchaseItem::query()
            ->join(
                'products',
                'products.id',
                '=',
                'purchase_items.product_id'
            )
            ->join(
                'purchases',
                'purchases.id',
                '=',
                'purchase_items.purchase_id'
            )
            ->leftJoinSub(
                $stockQuery,
                'stock',
                function ($join) {
                    $join->on(
                        'stock.product_id',
                        '=',
                        'purchase_items.product_id'
                    );
                }
            )
            ->select(
                'purchase_items.product_id',
                'products.book_name as product_name',
                DB::raw('SUM(purchase_items.quantity) as purchased_quantity'),
                DB::raw('COALESCE(stock.available_stock, 0) as quantity'),
                'stock.latest_movement_date'
            )
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'products.book_name',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->groupBy(
                'purchase_items.product_id',
                'products.book_name',
                'stock.available_stock',
                'stock.latest_movement_date'
            )
            ->when(
                ($validated['level'] ?? null) === 'normal',
                function ($query) {
                    $query->havingRaw(
                        'COALESCE(stock.available_stock, 0) > 5'
                    );
                }
            )
            ->when(
                ($validated['level'] ?? null) === 'critical',
                function ($query) {
                    $query->havingRaw(
                        'COALESCE(stock.available_stock, 0) <= 5'
                    );
                }
            )
            ->orderByDesc('stock.latest_movement_date')
            ->orderBy('products.book_name');
    }

    public function index(Request $request)
    {
        $movements = $this->getReportQuery($request)
            ->paginate(10)
            ->withQueryString();

        $hasFilters =
            $request->filled('search') ||
            $request->filled('level');

        return view(
            'reports.index',
            compact('movements', 'hasFilters')
        );
    }

    public function export(Request $request)
    {
        $fileName = 'stock-report-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        $writer = SimpleExcelWriter::streamDownload($fileName);

        $writer->addHeader([
            'Product',
            'Quantity',
            'Date',
            'Level',
        ]);

        $movements = $this->getReportQuery($request)->get();

        foreach ($movements as $movement) {

            $quantity = max(0, (int) $movement->quantity);

            $writer->addRow([
                'Product' => $movement->product_name ?? '-',

                'Quantity' => $quantity,

                'Date' => $movement->created_at
                    ? \Carbon\Carbon::parse(
                        $movement->created_at
                    )->format('d/m/Y h:i A')
                    : '-',

                'Level' => $quantity > 5
                    ? 'Normal'
                    : 'Critical',
            ]);
        }

        $writer->close();
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovements $stockMovements)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovements $stockMovements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovements $stockMovements)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovements $stockMovements)
    {
        //
    }
}
