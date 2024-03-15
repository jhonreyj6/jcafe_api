<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_plan' => 'in:visitor,regular,loyal'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->get('*')], 500);
        }

        $plan = SubscriptionPlan::where('name', $request->input('subscription_plan'))->firstOrFail();

        // stripe method
        // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // if (auth()->user()->stripe_id == null) {
        //     $stripe_user = $stripe->customers->create([
        //         'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
        //         'email' => auth()->user()->email,
        //     ]);

        //     auth()->user()->update([
        //         'stripe_id' => $stripe_user->id,
        //     ]);
        // }

        // attaching payment method without updating default payment method of stripe cx
        // stripe payment
        // $stripe->paymentMethods->attach(
        //     $request->input('payment_method_id'),
        //     ['customer' => auth()->user()->stripe_id]
        // );

        // $stripe->subscriptions->create([
        //     'customer' => auth()->user()->stripe_id,
        //     'items' => [['price' => $plan->stripe_price_id]],
        // ]);

        if(auth()->user()->subscriptions->count()) {
            return response()->json(['message' => 'Already Subscribe to a plan.'], 200);
        }

        $request->user()->newSubscription($plan, $plan->stripe_price_id)->create($request->input('payment_method_id'));

        return response()->json(['message' => 'success'], 200);
    }

    public function show(Request $request) {
        if(auth()->user()->subscriptions->count()) {
            return response()->json(['subscription' => auth()->user()->subscriptions], 200);
        }
    }

}
