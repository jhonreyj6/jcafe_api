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
        $user = User::whereId(Auth::id())->firstOrFail();


            $payment = $user->charge(
                1,
                $request->input('payment_method_id')
            );
            return $request->all();
            $payment = $payment->asStripePaymentIntent();

            $order = $user->orders()
                ->create([
                    'stripe_transaction_id' => $payment->charges->data[0]->id,
                    'order_items_id' => 1,
                    'status' => 0,
                    'user_id' => Auth::id(),
                ]);

            // foreach (json_decode($request->input('cart'), true) as $item) {
            //     $order->products()
            //         ->attach($item['id'], ['quantity' => $item['quantity']]);
            // }

            // $order->load('products');
            return $order;

    }
}
