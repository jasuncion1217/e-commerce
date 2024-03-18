<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/Dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//products
Route::middleware('auth')->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('Products/Products', [ProductController::class, 'index'])->name('products.index');
    Route::delete('/products/{product_id}', [ProductController::class, 'destroy'])->name('products.delete');
    Route::get('/products/show/{product_id}', [ProductController::class, 'getProduct'])->name('products.show');
    Route::put('/products/update/{product_id}', [ProductController::class, 'updateProduct'])->name('products.update');
});

//cart
Route::get('Cart/Cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/Cart/{product_id}', [CartController::class, 'store'])->name('cart.store');
Route::delete('/Cart/{cart_id}', [CartController::class, 'destroy'])->name('cart.destroy');


//welcome page
Route::get('/', [WelcomeController::class, 'welcomeProducts'])->name('welcome.index');


require __DIR__.'/auth.php';
