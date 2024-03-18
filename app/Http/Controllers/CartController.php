<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        try {
            $cart = Cart::select('products.product_name', 'products.product_img', 'products.product_quantity', 'cart.quantity', 'cart.cart_id', 'cart.total_price')
                ->join('products', 'cart.product_id', '=', 'products.product_id')
                ->orderByDesc('cart.cart_id')
                ->get();
            return Inertia::render('Cart/Cart', [
                'cartItems' => $cart,
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error getting cart items: ' . $e->getMessage())->withInput();
        }
    }
    public function store(Request $request, $product_id)
    {
        try {
            $product = Product::find($product_id);
            $product_name = $product->product_name;

            $validator = Validator::make($request->all(), [
                'product_quantity' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return Redirect::route('welcome.index')
                    ->withErrors($validator);
            } else {
                if (!$product) {
                    return Redirect::back()->withErrors(['error' => 'Product not found']);
                } elseif ($product->product_quantity <= 0) {
                    return Redirect::back()->withErrors(['error' => 'Out of stock']);
                } elseif ($product->product_quantity < $request->product_quantity) {
                    return Redirect::back()->withErrors(['error' => 'You have exceeded the number of stocks']);
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
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error adding cart item: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($cart_id)
    {
        try {
            $cart = Cart::find($cart_id);
            $product_id = $cart->product_id;
            $product = Product::find($product_id);
            $product_name = $product->product_name;

            $product_quantity = $product->product_quantity + $cart->quantity;

            $product->product_quantity = $product_quantity;
            $product->save();
            $cart->delete();

            return Redirect::route('cart.index')
                ->with('successMessage', 'Product: ' . $product_name . ' removed successfully from your cart.');
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error deleting cart item: ' . $e->getMessage())->withInput();
        }
    }
}
