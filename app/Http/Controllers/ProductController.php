<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\UserProducts;
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
        } catch (Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error adding product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($product_id)
    {
        try {
            $user = auth()->user();
            $product = Product::find($product_id);
            $user_product = UserProducts::where('product_id', $product_id)->first();
            $product_name = $product->product_name;

            if ($user->hasRole('admin')) {
                $product->delete();

                if ($product->product_img !== null && storage::exists('public/' . $product->product_img)) {
                    Storage::delete('public/' . $product->product_img);
                }
                return Redirect::route('products.index')
                    ->with('successMessage', 'Product: ' . $product_name . ' Deleted Successfully');
            } elseif ($user->hasRole('editor')) {
                if ($user->id === $user_product->user_id) {
                    $product->delete();

                    if ($product->product_img !== null && storage::exists('public/' . $product->product_img)) {
                        Storage::delete('public/' . $product->product_img);
                    }
                    return Redirect::route('products.index')
                        ->with('successMessage', 'Product: ' . $product_name . ' Deleted Successfully');
                } else {
                    throw new Exception("You don't have access to this product");
                }
            }
        } catch (Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error deleting product: ' . $e->getMessage())->withInput();
        }

    }

    public function getProduct($product_id)
    {
        try {
            $user = auth()->user();
            $product = Product::find($product_id);
            $user_product = UserProducts::where('product_id', $product_id)->first();

            if ($user->hasRole('admin')) {
                return Inertia::render('Products/ViewProduct', [
                    'product' => $product,
                ]);
            } elseif ($user->hasRole('editor')) {
                if ($user->id === $user_product->user_id) {
                    return Inertia::render('Products/ViewProduct', [
                        'product' => $product,
                    ]);
                } else {
                    throw new Exception("You don't have access to this product");
                }
            }
        } catch (Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error getting product: ' . $e->getMessage())->withInput();
        }
    }

    public function updateProduct(Request $request, $product_id)
    {
        try {
            $user = auth()->user();
            $product = Product::find($product_id);
            $user_product = UserProducts::where('product_id', $product_id)->first();
            $product_name = $product->product_name;

            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:1',
                'product_quantity' => 'required|integer|min:1',
            ]);

            if ($user->hasRole('admin')) {
                if ($validator->fails()) {
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
            } elseif ($user->hasRole('editor')) {
                if ($user->id === $user_product->user_id) {
                    if ($validator->fails()) {
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
                } else {
                    throw new Exception("You don't have access to this product");
                }
            }
        } catch (Exception $e) {
            return Redirect::back()->with('errorMessage', 'Error updating product: ' . $e->getMessage())->withInput();
        }
    }
}
