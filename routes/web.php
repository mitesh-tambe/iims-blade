<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/barcode-test', function () {

    $barcode = DNS1D::getBarcodeSVG(
        'BK000001',
        'C128',
        2,
        60
    );

    return view('barcode-test', compact('barcode'));
});

Route::get('/bill-test', function () {
    return view('bill-test');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products/export', [ProductController::class, 'export'])
        ->name('products.export');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    Route::resource('products', ProductController::class);
    Route::get(
        '/products/{product}/barcode-print',
        [ProductController::class, 'barcodePrint']
    )->name('products.barcode.print');

    Route::get('/products/{product}/json', function (Product $product) {

        return response()->json([
            'id' => $product->id,
            'book_name' => $product->book_name,
            'mrp' => $product->mrp,
            'isbn' => $product->isbn,
            'barcode_no' => $product->barcode_no,
        ]);
    });

    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('publications', PublicationController::class);
    Route::resource('racks', RackController::class);
    Route::resource('languages', LanguageController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('invoices', PurchaseController::class);
    Route::resource('users', UserController::class);
});

require __DIR__ . '/auth.php';
