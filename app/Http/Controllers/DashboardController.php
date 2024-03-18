<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;
        try {
            $carts = Cart::when($request->selectedYear, function ($query, $selectedYear) {
                return $query->whereYear('created_at', 'like', '%' . $selectedYear . '%');
            }, function ($query) use ($currentYear) {
                return $query->whereYear('created_at', $currentYear);
            })->get();

            $products = Product::all();
            $users = User::all();

            $cartCount = $carts->count();
            $productsCount = $products->count();
            $usersCount = $users->count();

            $years = Cart::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->pluck('year');

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
                'years' => $years,
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error getting data: ' . $e->getMessage())->withInput();
        }

    }
}
