<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Middleware;

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
        // $products = Product::select('product_id', 'product_name', 'product_img', 'product_price', 'product_img', 'product_quantity')
        // ->when($request->searchTerm, function ($query, $searchTerm) {
        //     return $query->where('product_name', 'like', '%' . $searchTerm . '%');
        // })->paginate(6);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'successMessage' => fn() => $request->session()->get('successMessage'),
                'errorMessage' => fn() => $request->session()->get('errorMessage'),
            ],
            // 'products' => $products
        ];
    }
}
