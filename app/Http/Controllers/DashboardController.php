<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $cart = Cart::all();
        $carts = Cart::whereYear('created_at', '=', '2024')->get();
        $products = Product::all();
        $users = User::all();

        $cartCount = $cart->count();
        $productsCount = $products->count();
        $usersCount = $users->count();

        $monthlySales = [];

        foreach ($cart as $cartItem) {
            $month = $cartItem->created_at->format('F');
            $revenue = $cartItem->sum('total_price');
            $totalPrice = $cartItem->total_price;

            if (isset($monthlySales[$month])) {
                $monthlySales[$month] += $totalPrice;
            } else {
                $monthlySales[$month] = $totalPrice;
            }
        }
        
        $labels = array_keys($monthlySales);
        $data = array_values($monthlySales);
        $lineGraphData = [
            'labels' => $labels,
            'data' => $data,
        ];

        return Inertia::render('Dashboard', [
            'cartItems' => $cartCount,
            'products' => $productsCount,
            'users' => $usersCount,
            'revenue' => $revenue,
            'lineGraphData' => $lineGraphData,
            'totalRevenue' => array_sum($data),
        ]);
    }
}
