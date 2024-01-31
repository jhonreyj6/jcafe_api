<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);

        foreach ($products as $key => $value) {
            $value->image_url = Storage::disk('s3')->url('products/images/' . $value->image);
            $value->getVariants;
            $value->default_stocks = $value->getVariants()->first()->stock;
            $value->default_price = $value->getVariants()->first()->price;
            $value->default_product_variant_id = $value->getVariants()->first()->id;
            $value->default_variant = $value->getVariants()->first()->value;
        }
        return $products;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'string|required|min:2|max:20|unique:products,name',
            'description' => 'string|required|max:50',
            'image' => 'file|required',
            'rating' => 'numeric|required',
            'variant_value.*' => 'numeric|required',
            'variant_unit.*' => 'string|required',
            'variant_price.*' => 'numeric|required',
            'variant_stock.*' => 'numeric|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->file('image')->hashName(),
            'rating' => $request->rating,
        ]);

        for($i = 0;  $i < count($request->input('variant_value')); $i++ ) {
            ProductVariant::create([
                'product_id' => $product->id,
                'value' => $request->input('variant_value')[$i],
                'unit' => $request->input('variant_unit')[$i],
                'price' => $request->input('variant_price')[$i],
                'stock' => $request->input('variant_stock')[$i],
                'stripe_api_id' => 4,
            ]);
        }


        Storage::disk('s3')->putFileAs('/products/images', $request->file('image'), $request->file('image')->hashName(), 'public');
        $product->getVariants;
        $product->image_url = Storage::disk('s3')->url('products/images/' . $product->image);
        $product->stock = $product->getVariants()->first()->stock;
        $product->price = $product->getVariants()->first()->price;
        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        return $request->all();
        $validator = Validator::make($request->all(), [
            'id' => 'exists:product,id',
            'name' => 'string|required|min:2|max:20',
            'description' => 'string|required|max:50',
            'stock' => 'numeric|required',
            'image' => 'file',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $product = Product::findOrFail($request->input('id'));
        $product->getVariants()->delete();
        Storage::disk('s3')->delete('products/images/' . $product->image);
        $product->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
