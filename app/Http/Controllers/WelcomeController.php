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
        $products = Product::select('product_id', 'product_name', 'product_img', 'product_price', 'product_img', 'product_quantity')
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
