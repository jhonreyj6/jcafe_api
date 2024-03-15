<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon;
use Validator;
use App\Models\Product;
use Storage;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::user()->id)->get();
        $cart->each(function ($value) {
            $value->product_details = Product::find($value->product_id);
            $value->product_variant_details = ProductVariant::find($value->product_variant_id);
            $value->image_url = Storage::disk('s3')->url('products/images/' . $value->product_details->image);
            $value->getProductDetails;
            return $value;
        });

        return response()->json([
            'cart_items' => $cart,
            'cart_count' => $cart->pluck('quantity')->sum(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'integer|required',
            'product_variant_id' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $cart = Cart::where('product_id', $request->input('product_id'))->where('product_variant_id', $request->input('product_variant_id'))->where('user_id', Auth::id())->first();

        $product_details = ProductVariant::where('id', $request->input('product_variant_id'))->first();
        $quantity = $request->input('quantity');

        if ($cart) {
            if ($request->input('quantity') + $cart->quantity > $product_details->stock) {
                $quantity = $product_details->stock;
            } else {
                $quantity = $request->input('quantity') + $cart->quantity;
            }
            $cart->update([
                'quantity' => $quantity
            ]);

            $cart->save();

            $cart_count = Cart::where('user_id', Auth::id())->get();
            return response()->json(['quantity' => $cart_count->pluck('quantity')->sum()], 200);
        }

        if ($request->input('quantity') > $product_details->stock) {
            $quantity = $product_details->stock;
        }


        DB::table('carts')->insert([
            'user_id' => Auth::user()->id,
            'product_id' => $request->input('product_id'),
            'quantity' => $quantity,
            'product_variant_id' => $request->input('product_variant_id'),
        ]);

        // return data query
        $cart_count = Cart::where('user_id', Auth::id())->get();
        return response()->json(['quantity' => $cart_count->pluck('quantity')->sum()], 200);
    }


    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCartRequest  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $cart = Cart::whereId($id)->where('user_id', Auth::id())->firstOrFail();

        if ($cart) {
            if ($request->input('quantity') >= 1 && $request->input('quantity') <= $cart->getVariants->stock) {
                $cart->quantity = $request->input('quantity');
                $cart->save();

                return response()->json(['updated' => ''], 200);
            }
        }

        return response()->json(['message' => 'fail'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $carts = Cart::whereIn('id', $request->input('orders'))->where('user_id', Auth::id())->get();

        if ($carts->count()) {
            foreach ($carts as $cart) {
                $cart->delete();
            }
        }

        return response()->json(['message' => 'success'], 200);
    }
}
