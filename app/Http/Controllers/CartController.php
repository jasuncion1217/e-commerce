<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::select('products.product_name', 'products.product_img', 'products.product_quantity' , 'cart.quantity', 'cart.cart_id', 'cart.total_price')
            ->join('products', 'cart.product_id', '=', 'products.product_id')
            ->orderByDesc('cart.cart_id')
            ->get();
        return Inertia::render('Cart/Cart',[
            'cartItems' => $cart,
        ]);
    }
    public function store(Request $request, $product_id)
    {
        $product = Product::find($product_id);
        $product_name = $product->product_name;

        if (!$product) {
            return Inertia::render('welcome.index', [
                'errorMessage' => 'Product not found',
            ])->toResponse($request)->setStatusCode(403);
        }elseif($product->product_quantity < $request->product_quantity){
            return Inertia::render('welcome.index', [
                'errorMessage' => 'You have exceeded the number of stocks',
            ])->toResponse($request)->setStatusCode(400);
        }elseif( $request->product_quantity <= 0){
            return Inertia::render('welcome.index', [
                'errorMessage' => 'Invalid input',
            ])->toResponse($request)->setStatusCode(400);
        }elseif ($product->product_quantity <= 0) {
            return Inertia::render('welcome.index', [
                'errorMessage' => 'Invalid input',
            ])->toResponse($request)->setStatusCode(400);
        }

        $updated_product_quantity = $product->product_quantity - $request->product_quantity;
    
        $cart = new Cart();
        $cart->product_id = $product_id;
        $cart->quantity = $request->product_quantity;
        $add_to_cart_quantity = $cart->quantity = $request->product_quantity;
        $total_price = $add_to_cart_quantity * $product->product_price;
        $cart->total_price = $total_price;
        $product->product_quantity = $updated_product_quantity;
        $product->save();
        $cart->save();

        return Redirect::route('welcome.index')
            ->with('successMessage', 'Product: ' . $product_name . ' Added to cart successfully');
    }

    public function destroy($cart_id)
    {
        $cart = Cart::find($cart_id);
        $product_id = $cart->product_id;
        $product = Product::find($product_id);
        $product_name = $product->product_name;

        $product_quantity = $product->product_quantity + $cart->quantity;

        $product->product_quantity = $product_quantity;
        $product->save();
        $cart->delete();

        return Redirect::route('cart.index')
                ->with('successMessage', 'Product: ' . $product_name  . ' removed successfully from your cart.');
    }
}
