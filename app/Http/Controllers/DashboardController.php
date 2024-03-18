<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $carts = Cart::whereYear('created_at', '=', '2024')->get();
            $products = Product::all();
            $users = User::all();

            $cartCount = $carts->count();
            $productsCount = $products->count();
            $usersCount = $users->count();

            $monthlySales = [];

            foreach ($carts as $cartItem) {
                $month = $cartItem->created_at->format('F');
                $revenue = $cartItem->sum('total_price');
                $totalPrice = $cartItem->total_price;

                if (isset ($monthlySales[$month])) {
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
                'revenue' => $revenue ?? '0',
                'lineGraphData' => $lineGraphData,
                'totalRevenue' => array_sum($data),
            ]);
        }  catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error getting data: ' . $e->getMessage())->withInput();
        }

    }
}
