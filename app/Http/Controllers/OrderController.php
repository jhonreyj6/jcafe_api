<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use Auth;
use Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return $orders;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders.*' => 'exists:carts,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $cart_items = Cart::whereIn('id', $request->input('orders'))->get();

        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 0,
        ]);
        foreach ($cart_items as $item) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity
            ]);
            // $item->delete();
        }

        // $order_items = OrderItems::where('order_id', $order->id)->get();
        // $product_variant_order_details = ProductVariant::whereIn('product_id', $order_items->pluck('product_variant_id'))->get();

        // $data = [];
        // foreach ($product_variant_order_details as $variant) {
        //     $data = array_merge($data, array($variant->stripe_price_id => 1));
        // }
        // return $request->user()->checkout($data, [
        //     'success_url' => route('payment.success'),
        //     'cancel_url' => route('payment.cancel'),
        // ]);
    }

    public function successPayment() {
        return view('pages.payment_success');
    }

    public function cancelPayment() {
        return view('pages.payment_cancel');
    }
}
