<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\Product;
use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cart = Cart::all();

        $cartCount = $cart->count();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
            ],
            'flash' => [
                'successMessage' => fn() => $request->session()->get('successMessage'),
                'errorMessage' => fn() => $request->session()->get('errorMessage'),
            ],
            'cartCount' => $cartCount,
            // 'products' => $products
        ];
    }
}
