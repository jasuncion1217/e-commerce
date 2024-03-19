<?php

namespace App\Http\Controllers;

use App\Models\UserProducts;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;

            if ($user->hasRole('admin')) {
                $products = UserProducts::select('products.product_id', 'products.product_name', 'products.product_img', 'products.product_price', 'products.product_img', 'products.product_quantity', 'users.name', 'users.id')
                ->join('products', 'user_products.product_id', '=', 'products.product_id')
                ->join('users', 'user_products.user_id', '=', 'users.id')
                ->orderByDesc('product_id')
                ->when($request->searchTerm, function ($query, $searchTerm) {
                    return $query->where('product_name', 'like', '%' . $searchTerm . '%');
                })->paginate(5);
            } elseif ($user->hasRole('editor')) {
                $products = UserProducts::select('products.product_id', 'products.product_name', 'products.product_img', 'products.product_price', 'products.product_img', 'products.product_quantity', 'users.name', 'users.id')
                ->join('products', 'user_products.product_id', '=', 'products.product_id')
                ->join('users', 'user_products.user_id', '=', 'users.id')
                ->orderByDesc('product_id')
                ->when($request->searchTerm, function ($query, $searchTerm) {
                    return $query->where('product_name', 'like', '%' . $searchTerm . '%');
                })->where('users.id', $user_id)
                ->paginate(5);
            }
            

            return Inertia::render('Products/Products', [
                'products' => $products,
            ]);
        } catch (QueryException $e) {
            return response()->json(['errorMessage' => 'Database query error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $user_id = $user->id;

            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:1',
                'product_quantity' => 'required|integer|min:1',
                'product_img' => 'required|mimes:jpeg,png,jpg',
            ]);

            if ($validator->fails()) {
                return Redirect::route('products.index')
                    ->withErrors($validator);
            } else {
                $product = new Product();
                $link = Storage::disk('public')->putFile('photos/Products', $request->file('product_img'));
                $product->product_name = $request->product_name;
                $product->product_price = $request->product_price;
                $product->product_quantity = $request->product_quantity;
                $product->product_img = $link;
                $product->save();

                $user_product = new UserProducts;
                $user_product->user_id = $user_id;
                $user_product->product_id = $product->product_id;
                $user_product->save();

                return Redirect::route('products.index')
                    ->with('successMessage', 'Product Added Successfully');
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error adding product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($product_id)
    {
        try {
            $product = Product::find($product_id);
            $product_name = $product->product_name;
            $product->delete();

            return Redirect::route('products.index')
                ->with('successMessage', 'Product: ' . $product_name . ' Deleted Successfully');
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error deleting product: ' . $e->getMessage())->withInput();
        }

    }

    public function getProduct($product_id)
    {
        try {
            $product = Product::find($product_id);

            return Inertia::render('Products/ViewProduct', [
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error getting product: ' . $e->getMessage())->withInput();
        }
    }

    public function updateProduct(Request $request, $product_id)
    {
        try {
            $product = Product::find($product_id);
            $product_name = $product->product_name;

            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:1',
                'product_quantity' => 'required|integer|min:1',
            ]);


            if (!$product) {
                return Redirect::route('products.show', ['product_id' => $product_id])
                    ->with('errorMessage', 'Product no found');
            } elseif ($validator->fails()) {
                return Redirect::route('products.show', ['product_id' => $product_id])
                    ->withErrors($validator);
            } else {
                $product->product_name = $request->input('product_name');
                $product->product_price = $request->input('product_price');
                $product->product_quantity = $request->input('product_quantity');
                $product->updated_at = now();

                $product->save();

                return Redirect::route('products.show', ['product_id' => $product_id])
                    ->with('successMessage', 'Product: ' . $product_name . ' Updated Successfully');
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error updating product: ' . $e->getMessage())->withInput();
        }
    }
}
