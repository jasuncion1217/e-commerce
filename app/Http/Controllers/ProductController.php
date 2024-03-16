<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::select('product_id', 'product_name', 'product_img', 'product_price', 'product_img', 'product_quantity')
            ->when($request->searchTerm, function ($query, $searchTerm) {
                return $query->where('product_name', 'like', '%' . $searchTerm . '%');
            })->paginate(5);
        return Inertia::render('Products/Products', [
            'products' => $products,
        ]);
    }
    
    public function store(Request $request)
    {
        $product = new Product();
        $link = Storage::disk('public')->putFile('photos/Products', $request->file('product_img'));
        $product->product_name = $request->product_name;
        $product->product_price = $request->product_price;
        $product->product_quantity = $request->product_quantity;
        $product->product_img = $link;
        $product->save();

        return Redirect::route('products.index')
            ->with('successMessage', 'Product Added Successfully');
    }

    public function destroy($product_id)
    {
        $product = Product::find($product_id);
        $product_name = $product->product_name;
        $product->delete();

        return Redirect::route('products.index')
            ->with('successMessage', 'Product: ' . $product_name . ' Deleted Successfully');
    }

    public function getProduct($product_id)
    {
        $product = Product::find($product_id);

        return Inertia::render('Products/ViewProduct', [
            'product' => $product,
        ]);
    }

    public function updateProduct(Request $request, $product_id)
    {
        $product = Product::find($product_id);
        $product_name = $product->product_name;

        if (!$product) {
            return Redirect::route('product.show', ['product_id' => $product_id], 404)
                ->with('errorMessage', 'Product Updated Successfully');
        } else {
            $product->product_name = $request->input('product_name');
            $product->product_price = $request->input('product_price');
            $product->product_quantity = $request->input('product_quantity');
            $product->updated_at = now();

            $product->save();

            return Redirect::route('products.show', ['product_id' => $product_id])
                ->with('successMessage', 'Product: ' . $product_name . ' Updated Successfully');
        }
    }
}
