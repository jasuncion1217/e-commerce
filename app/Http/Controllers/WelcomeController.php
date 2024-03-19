<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class WelcomeController extends Controller
{
    public function welcomeProducts(Request $request)
    {
        $products = Product::select('products.product_id', 'products.product_name', 'products.product_img', 'products.product_price', 'products.product_img', 'products.product_quantity', 'users.name')
            ->join('user_products', 'products.product_id', '=', 'user_products.product_id')
            ->join('users', 'user_products.user_id', '=', 'users.id')
            ->orderByDesc('product_id')
            ->when($request->searchTerm, function ($query, $searchTerm) {
                return $query->where('product_name', 'like', '%' . $searchTerm . '%');
            })->paginate(6);
        return Inertia::render('Welcome', [
            'products' => $products,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
