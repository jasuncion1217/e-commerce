<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
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
            $products = Product::select('product_id', 'product_name', 'product_img', 'product_price', 'product_img', 'product_quantity')
                ->orderByDesc('product_id')
                ->when($request->searchTerm, function ($query, $searchTerm) {
                    return $query->where('product_name', 'like', '%' . $searchTerm . '%');
                })->paginate(5);
            
            return Inertia::render('Products/Products', [
                'products' => $products,
            ]);
        } catch (QueryException $e) {
            return response()->json(['errorMessage' => 'Database query error: ' . $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:0',
                'product_quantity' => 'required|integer|min:0',
                'product_img' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);
    
            if($validator->fails()){
                return Redirect::route('products.index')
                    ->with('errorMessage',  $validator->messages());
            } else {
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
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return Redirect::back()->withErrors($e->errors())->withInput();
            }

            return Redirect::back()->with('errorMessage', 'Error adding product: ' . $e->getMessage())->withInput();
        }
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

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_quantity' => 'required|integer|min:0',
        ]);


        if (!$product) {
            return Redirect::route('products.show', ['product_id' => $product_id])
                ->with('errorMessage', 'Product no found');
        } if($validator->fails()){
            return Redirect::route('products.show', ['product_id' => $product_id])
                ->with('errorMessage', $validator->messages());
        }else {
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
