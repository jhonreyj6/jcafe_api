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
        $orders = Order::orderBy('created_at', 'desc')->paginate(12);

        return $orders;
    }

    public function search(Request $request) {
        $orders = Order::where('id', "LIKE", "%" . $request->input('query') ."%")->paginate(12);

        return $orders;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders.*' => 'exists:carts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $cart_items = Cart::whereIn('id', $request->input('orders'))->where('user_id', Auth::id())->get();


        if (count($cart_items)) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            // $amount

            if (auth()->user()->stripe_id == null) {
                $stripe_user = $stripe->customers->create([
                    'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'email' => auth()->user()->email,
                ]);

                auth()->user()->update([
                    'stripe_id' => $stripe_user->id,
                ]);
            }

            $intent = \Stripe\PaymentIntent::create([
                'amount' => 1000,
                'currency' => 'sgd',
                'payment_method_types' => ['card'],
                'payment_method' => $request->input('payment_method_id'),
                'confirm' => true,
            ]);

            if ($intent->status == 'succeeded') {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => 1,
                    'stripe_transaction_id' => $intent->id
                ]);

                foreach ($cart_items as $item) {
                    OrderItems::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity
                    ]);
                    $item->delete();
                }

                return response()->json(['message' => 'success'], 200);
            } else {
                return response()->json(['message' => 'payment error'], 200);
            }
        }

    }
}
