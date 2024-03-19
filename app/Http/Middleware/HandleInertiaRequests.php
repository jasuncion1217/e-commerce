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
                'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'email_verified_at' => $request->user()->email_verified_at,
                        'created_at' => $request->user()->created_at,
                        'updated_at' => $request->user()->updated_at,
                        'role' => $request->user()->roles()->pluck('name')->first(),
                    ] : null,
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
