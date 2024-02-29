<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // old
        // $cart = Cart::where('user_id', Auth::user()->id)->whereIn('id', $request->input('id'))->get();

        // $cart->each(function($data, $key) {
        //     Order::create([
        //         'user_id' => Auth::user()->id,
        //         'quantity' => $data->quantity,
        //         'product_variant_id' => $data->product_variant_id,
        //     ]);
        //     Cart::find($data->id)->delete();
        // });

        // return response()->json(['message' => 'success'], 200);

        $user = User::whereId(Auth::id())->firstOrFail();


            $payment = $user->charge(
                1,
                $request->input('payment_method_id')
            );
            return $request->all();
            $payment = $payment->asStripePaymentIntent();

        $carts->each(function($data, $key) {
            Order::create([
                'user_id' => Auth::user()->id,
                'quantity' => $data->quantity,
                'product_variant_id' => $data->product_variant_id,
            ]);
            Cart::find($data->id)->delete();
        });

        $data = [];
        foreach ($variants as $variant) {
            $data = array_merge($data, array($variant->stripe_price_id => 1));
        }

        // foreach ($carts as $cart) {
        //     OrderItems::create([
        //         'user_id' => Auth::id(),
        //         'book_id' => $cart->book_id,
        //         'order_id' => $order->id,
        //     ]);
        //     $cart->delete();
        // }

        // return $request->user()->checkout($data, [
        //     'success_url' => route('payment.success'),
        //     'cancel_url' => route('payment.cancel'),
        // ]);

    }
}
