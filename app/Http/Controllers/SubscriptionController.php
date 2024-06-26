<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Validator;
use Carbon;

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

        $plans = SubscriptionPlan::all();
        $isSubscribed = false;
        foreach ($plans as $plan) {
            if (auth()->user()->subscribed($plan)) {
                $isSubscribed = true;
            }
        }

        if ($isSubscribed) {
            return response()->json(['message' => 'Already Subscribe to a plan.'], 200);
        }

        auth()->user()
            ->newSubscription($plan, $plan->stripe_price_id)
            ->trialDays(1)
            ->create($request->input('payment_method_id'));

        return response()->json(['message' => 'success'], 200);
    }

    public function show(Request $request)
    {
        $subscription = null;

        $plans = SubscriptionPlan::all();
        foreach ($plans as $plan) {
            if (auth()->user()->subscribed($plan)) {
                $subscription = auth()->user()->subscription($plan);
            }
        }

        if ($subscription) {
            $this->modifiedData($subscription);
            return response()->json($subscription, 200);
        }
    }

    public function cancel(Request $request)
    {
        $subscription = auth()->user()->subscriptions->where('id', $request->input('id'))->firstOrFail();
        $request->user()->subscription($subscription->type)->cancel();

        $this->modifiedData($subscription);
        return response()->json($subscription, 200);
    }

    public function resume(Request $request)
    {
        $subscription = $request->input('subscription');
        $request->user()->subscription(json_encode($subscription['type']))->resume();
        $new_subscription = $request->user()->subscription(json_encode($subscription['type']));
        $new_subscription = $this->modifiedData($new_subscription);

        return response()->json($new_subscription, 200);
    }

    public function modifiedData($subscription)
    {
        $subscription->creation_time = Carbon::create($subscription->created_at)->toDayDateTimeString();
        if ($subscription->trial_ends_at) {
            $subscription->trial_ends = Carbon::create($subscription->trial_ends_at)->toDayDateTimeString();
        }
        if ($subscription->ends_at) {
            $subscription->end_time = Carbon::create($subscription->ends_at)->toDayDateTimeString();
        }
        return $subscription;
    }

}
