<?php

namespace App\Http\Controllers;

use App\Models\OrderItems;
use Illuminate\Http\Request;
use Validator;
use App\Models\Order;

class OrderAdminController extends Controller
{
    public function search(Request $request) {
        $orders = Order::where('user_id', $request->input('query'))->paginate(12);

        return $orders;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id|required',
            'variant_id.*' => 'exists:product_variants,id|required',
            'quantity.*' => 'numeric|required',
        ]);

        if (
            $validator->fails() &&
            count($request->input('variant_id')) !=
            count($request->input('quantity'))
        ) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $order = Order::create([
            'user_id' => $request->input('user_id')
        ]);


        foreach ($request->input('variant_id') as $key => $value) {
            OrderItems::create([
                'order_id' => $order->id,
                'product_variant_id' => $value,
                'quantity' => $request->input('quantity')[$key],
            ]);
        }

        return response()->json(['message' => 'success'], 200);
    }

    public function destroy(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('id'))->get();

        foreach ($orders as $order) {
            OrderItems::where('order_id', $order->id)->delete();
        }

        return response()->json(['message' => 'deleted'], 200);
    }
}
