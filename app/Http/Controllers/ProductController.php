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

        return response()->json($products, 200);
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
            'variant_stripe_id.*' => 'string|required',
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

        for ($i = 0; $i < count($request->input('variant_value')); $i++) {
            ProductVariant::create([
                'product_id' => $product->id,
                'value' => $request->input('variant_value')[$i],
                'unit' => $request->input('variant_unit')[$i],
                'price' => $request->input('variant_price')[$i],
                'stock' => $request->input('variant_stock')[$i],
                'stripe_price_id' => $request->input('variant_stripe_id')[$i],
            ]);
        }


        Storage::disk('s3')->putFileAs('/products/images', $request->file('image'), $request->file('image')->hashName(), 'public');
        $product->getVariants;
        $product->image_url = Storage::disk('s3')->url('products/images/' . $product->image);
        $product->stock = $product->getVariants()->first()->stock;
        $product->price = $product->getVariants()->first()->price;
        return response()->json($product, 200);
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
        $validator = Validator::make($request->all(), [
            'id' => 'exists:products,id',
            'name' => 'string|min:2|max:20',
            'description' => 'string|max:500|nullable',
            'rating' => 'numeric|required',
            'image' => 'file|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $product = Product::findOrFail($request->input('id'));
        $variant = ProductVariant::where('product_id', $product->id)->get();

        if ($request->file('image')) {
            Storage::disk('s3')->delete('products/images/' . $product->image);
            Storage::disk('s3')->putFileAs('/products/images', $request->file('image'), $request->file('image')->hashName(), 'public');
        }

        $product->update([
            'name' => $request->input('name') ? $request->input('name') : $product->name,
            'description' => $request->input('description') ? $request->input('description') : $product->description,
            'image' => $request->file('image') ? $request->file('image')->hashName() : $product->image
        ]);

        $product->image_url = Storage::disk('s3')->url('products/images/' . $product->image);
        $product->getVariants;
        $product->default_stocks = $product->getVariants()->first()->stock;
        $product->default_price = $product->getVariants()->first()->price;
        $product->default_product_variant_id = $product->getVariants()->first()->id;
        $product->default_variant = $product->getVariants()->first()->value;

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $products = Product::whereIn('id', $request->input('id'))->get();
        foreach ($products as $product) {
            $product->getVariants()->delete();
            Storage::disk('s3')->delete('products/images/' . $product->image);
            $product->delete();
        }

        return response()->json(['message' => 'deleted'], 200);
    }
}
