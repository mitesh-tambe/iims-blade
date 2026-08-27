<?php

namespace App\Http\Controllers;

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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'level' => 'nullable|in:normal,critical',
        ]);

        $search = trim($validated['search'] ?? '');
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        return StockMovements::query()
            ->join(
                'products',
                'products.id',
                '=',
                'stock_movements.product_id'
            )

            ->select(
                'stock_movements.product_id',
                'products.book_name as product_name',
                DB::raw('SUM(stock_movements.quantity) as quantity'),
                DB::raw('MAX(stock_movements.created_at) as created_at')
            )

            // ONLY products which have at least one SALE
            // Adjustments are still included in SUM()
            ->whereIn('stock_movements.product_id', function ($query) {

                $query->select('product_id')
                    ->from('stock_movements')
                    ->where('type', 'sale')
                    ->distinct();
            })

            // Search
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'products.book_name',
                    'like',
                    '%' . $search . '%'
                );
            })

            // Start Date
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate(
                    'stock_movements.created_at',
                    '>=',
                    $startDate
                );
            })

            // End Date
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate(
                    'stock_movements.created_at',
                    '<=',
                    $endDate
                );
            })
            ->groupBy(
                'stock_movements.product_id',
                'products.book_name'
            )

            // Normal
            ->when(
                ($validated['level'] ?? null) === 'normal',
                function ($query) {
                    $query->havingRaw(
                        'SUM(stock_movements.quantity) > 5'
                    );
                }
            )

            // Critical
            ->when(
                ($validated['level'] ?? null) === 'critical',
                function ($query) {
                    $query->havingRaw(
                        'SUM(stock_movements.quantity) <= 5'
                    );
                }
            )
            ->orderBy('products.book_name');
    }

    public function index(Request $request)
    {
        $movements = $this->getReportQuery($request)
            ->paginate(10)
            ->withQueryString();

        $hasFilters =
            $request->filled('search') ||
            $request->filled('start_date') ||
            $request->filled('end_date') ||
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
