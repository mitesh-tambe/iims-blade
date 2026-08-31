<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovements;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $sales = Sale::where('invoice_no', 'like', '%' . request('search') . '%')
    //         ->latest()
    //         ->paginate(10)
    //         ->withQueryString();
    //     return view('sales.index', compact('sales'));
    // }

    public function index()
    {
        // Paginated list for table view
        $sales = Sale::where('invoice_no', 'like', '%' . request('search') . '%')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 1. Weekly Data (Last 7 Days)
        $weeklyRaw = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('total', 'date');

        $weeklyLabels = [];
        $weeklyValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $weeklyLabels[] = Carbon::now()->subDays($i)->format('D, M j');
            $weeklyValues[] = (float) ($weeklyRaw[$date] ?? 0);
        }

        // dd($weeklyLabels, $weeklyValues);

        // 2. Monthly Data (Current Year by Month)
        $monthlyRaw = Sale::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyValues[] = (float) ($monthlyRaw[$m] ?? 0);
        }

        // 3. Yearly Data (Last 5 Years)
        $yearlyRaw = Sale::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subYears(4)->startOfYear())
            ->groupBy('year')
            ->pluck('total', 'year');

        $yearlyLabels = [];
        $yearlyValues = [];
        $currentYear = (int) Carbon::now()->year;
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearlyLabels[] = (string) $y;
            $yearlyValues[] = (float) ($yearlyRaw[$y] ?? 0);
        }

        // Combine chart datasets
        $chartData = [
            'weekly' => [
                'labels' => $weeklyLabels,
                'datasets' => [[
                    'label' => 'Weekly Sales (₹)',
                    'data' => $weeklyValues,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ]],
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'datasets' => [[
                    'label' => 'Monthly Sales (₹)',
                    'data' => $monthlyValues,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 1,
                ]],
            ],
            'yearly' => [
                'labels' => $yearlyLabels,
                'datasets' => [[
                    'label' => 'Yearly Sales (₹)',
                    'data' => $yearlyValues,
                    'backgroundColor' => 'rgba(139, 92, 246, 0.5)',
                    'borderColor' => 'rgb(139, 92, 246)',
                    'borderWidth' => 1,
                ]],
            ],
        ];

        // dd($chartData);

        return view('sales.index', compact('sales', 'chartData'));
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
    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $validated = $request->validate([
    //         'name' => 'nullable|string|max:255',
    //         'phone' => 'nullable|digits_between:1,12',

    //         // 'invoice_no' => 'required|string|unique:sales,invoice_no',
    //         'sale_date' => 'nullable|date',
    //         'total_amount' => 'required|numeric|min:0',
    //         'payment_mode' => 'nullable|string|max:50',

    //         'products' => 'required|array|min:1',
    //         'products.*.product_id' => 'required|exists:products,id',
    //         'products.*.quantity' => 'required|integer|min:1',
    //         'products.*.purchase_price' => 'required|numeric|min:0',
    //         'products.*.discount' => 'nullable|decimal:0,2',
    //         'products.*.net_amount' => 'required|numeric|min:0',
    //     ]);

    //     $sale = DB::transaction(function () use ($validated) {

    //         $customerId = null;

    //         // create customer if name or phone entered
    //         if (
    //             !empty($validated['name']) ||
    //             !empty($validated['phone'])
    //         ) {

    //             $customer = Customer::create([
    //                 'name' => $validated['name'] ?? '',
    //                 'phone' => $validated['phone'] ?? '',
    //             ]);

    //             $customerId = $customer->id;
    //         }
    //         $sale = Sale::create([
    //             'customer_id' => $customerId,
    //             // 'invoice_no' => $validated['invoice_no'],
    //             'total_amount' => $validated['total_amount'],
    //             'payment_mode' => $validated['payment_mode'],
    //             'sale_date' => $validated['sale_date'] ?? now(),
    //             'created_by' => Auth::id(),
    //         ]);

    //         $products = Product::whereIn('id', collect($validated['products'])
    //             ->pluck('product_id'))
    //             ->get()
    //             ->keyBy('id');

    //         foreach ($validated['products'] as $product) {
    //             $selectedProduct = $products[$product['product_id']];
    //             $sale->saleItems()->create([
    //                 'product_id' => $product['product_id'],
    //                 'quantity' => $product['quantity'],
    //                 'selling_price' => $product['purchase_price'],
    //                 'mrp' => $selectedProduct->mrp,
    //                 'discount' => $product['discount'] ?? 0,
    //                 'net_amount' => $product['net_amount'],
    //             ]);
    //         }

    //         return $sale;
    //     });

    //     return redirect()->route('bill.test', $sale->id);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits_between:1,12',
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|decimal:0,2',
            'products.*.net_amount' => 'required|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use ($validated) {

            $customerId = null;

            // Create customer if name or phone entered
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

            // Create Sale
            $sale = Sale::create([
                'customer_id' => $customerId,
                'total_amount' => $validated['total_amount'],
                'payment_mode' => $validated['payment_mode'],
                'sale_date' => $validated['sale_date'] ?? now(),
                'created_by' => Auth::id(),
            ]);

            // Fetch products
            $products = Product::whereIn(
                'id',
                collect($validated['products'])->pluck('product_id')
            )->get()->keyBy('id');

            foreach ($validated['products'] as $product) {

                $selectedProduct = $products[$product['product_id']];

                // Create Sale Item
                $sale->saleItems()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'selling_price' => $product['purchase_price'],
                    'mrp' => $selectedProduct->mrp,
                    'discount' => $product['discount'] ?? 0,
                    'net_amount' => $product['net_amount'],
                ]);

                // Create Stock Movement
                // Sale reduces stock, therefore quantity is NEGATIVE.
                StockMovements::create([
                    'product_id' => $product['product_id'],
                    'type' => 'sale',
                    'quantity' => - ((int) $product['quantity']),
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'remarks' => 'Sale',
                    'created_by' => Auth::id(),
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
    // public function update(Request $request, Sale $sale)
    // {
    //     $validated = $request->validate([
    //         'name' => 'nullable|string|max:255',
    //         'phone' => 'nullable|digits_between:1,12',

    //         'sale_date' => 'nullable|date',
    //         'total_amount' => 'required|numeric|min:0',
    //         'payment_mode' => 'nullable|string|max:50',

    //         'products' => 'required|array|min:1',
    //         'products.*.product_id' => 'required|exists:products,id',
    //         'products.*.quantity' => 'required|integer|min:1',
    //         'products.*.purchase_price' => 'required|numeric|min:0',
    //         'products.*.discount' => 'nullable|decimal:0,2',
    //         'products.*.net_amount' => 'required|numeric|min:0',
    //     ]);

    //     DB::transaction(function () use ($validated, $sale) {

    //         $customerId = null;

    //         // Create customer if name or phone entered
    //         if (
    //             !empty($validated['name']) ||
    //             !empty($validated['phone'])
    //         ) {

    //             $customer = Customer::create([
    //                 'name' => $validated['name'] ?? '',
    //                 'phone' => $validated['phone'] ?? '',
    //             ]);

    //             $customerId = $customer->id;
    //         }

    //         // Update sale
    //         $sale->update([
    //             'customer_id' => $customerId,
    //             'total_amount' => $validated['total_amount'],
    //             'payment_mode' => $validated['payment_mode'] ?? null,
    //             'sale_date' => $validated['sale_date'] ?? $sale->sale_date,
    //         ]);

    //         // Delete old items
    //         $sale->saleItems()->delete();

    //         // Fetch selected products
    //         $products = Product::whereIn(
    //             'id',
    //             collect($validated['products'])->pluck('product_id')
    //         )->get()->keyBy('id');

    //         // Insert updated items
    //         foreach ($validated['products'] as $product) {

    //             $selectedProduct = $products[$product['product_id']];

    //             $sale->saleItems()->create([
    //                 'product_id'    => $product['product_id'],
    //                 'quantity'      => $product['quantity'],
    //                 'selling_price' => $product['purchase_price'],
    //                 'mrp'           => $selectedProduct->mrp,
    //                 'discount'      => $product['discount'] ?? 0,
    //                 'net_amount'    => $product['net_amount'],
    //             ]);
    //         }
    //     });

    //     $filters = [];

    //     $map = [
    //         'search' => 'search',
    //         'page'   => 'page',
    //     ];

    //     foreach ($map as $from => $to) {
    //         if ($request->filled($from)) {
    //             $filters[$to] = $request->input($from);
    //         }
    //     }

    //     return redirect()
    //         ->route('sales.index', $filters)
    //         ->with('success', 'Bill updated successfully')
    //         ->with('print_bill', route('bill.test', $sale->id));
    // }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|digits_between:1,12',
            'sale_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|decimal:0,2',
            'products.*.net_amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $sale) {

            $customerId = null;

            // Create customer if name or phone entered
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

            // Update Sale
            $sale->update([
                'customer_id' => $customerId,
                'total_amount' => $validated['total_amount'],
                'payment_mode' => $validated['payment_mode'] ?? null,
                'sale_date' => $validated['sale_date'] ?? $sale->sale_date,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Remove old stock movements for this sale
        |--------------------------------------------------------------------------
        */

            StockMovements::where('reference_type', Sale::class)
                ->where('reference_id', $sale->id)
                ->where('type', 'sale')
                ->delete();

            /*
        |--------------------------------------------------------------------------
        | Remove old sale items
        |--------------------------------------------------------------------------
        */

            $sale->saleItems()->delete();

            /*
        |--------------------------------------------------------------------------
        | Fetch products
        |--------------------------------------------------------------------------
        */

            $products = Product::whereIn(
                'id',
                collect($validated['products'])->pluck('product_id')
            )->get()->keyBy('id');

            /*
        |--------------------------------------------------------------------------
        | Create new sale items + stock movements
        |--------------------------------------------------------------------------
        */

            foreach ($validated['products'] as $product) {

                $selectedProduct = $products[$product['product_id']];

                // Create Sale Item
                $sale->saleItems()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'selling_price' => $product['purchase_price'],
                    'mrp' => $selectedProduct->mrp,
                    'discount' => $product['discount'] ?? 0,
                    'net_amount' => $product['net_amount'],
                ]);

                // Create Stock Movement
                StockMovements::create([
                    'product_id' => $product['product_id'],
                    'type' => 'sale',
                    'quantity' => - ((int) $product['quantity']),
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'remarks' => 'Sale',
                    'created_by' => Auth::id(),
                ]);
            }
        });

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
            ->route('sales.index', $filters)
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
