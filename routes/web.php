<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
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

//for admin only
Route::middleware(['auth', 'role:admin'])->prefix('users')->group(function () {
    Route::resource('/roles', RoleController::class);
    Route::resource('/permissions', PermissionController::class);
    Route::get('/users', [UserController::class,'index'])->name('users.index');
    Route::post('/user', [UserController::class,'store'])->name('users.store');
    Route::get('/user/{id}', [UserController::class, 'getUserData'])->name('user.get');
    Route::put('/user/{id}',[UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}',[UserController::class, 'destroy'])->name('user.destroy');
});

//cart
Route::get('Cart/Cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/Cart/{product_id}', [CartController::class, 'store'])->name('cart.store');
Route::delete('/Cart/{cart_id}', [CartController::class, 'destroy'])->name('cart.destroy');


//welcome page
Route::get('/', [WelcomeController::class, 'welcomeProducts'])->name('welcome.index');


require __DIR__.'/auth.php';
