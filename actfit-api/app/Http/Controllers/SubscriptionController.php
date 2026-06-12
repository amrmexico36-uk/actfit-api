<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // GET /api/subscriptions  — اشتراكات المستخدم
    public function index(Request $request)
    {
        $subscriptions = Subscription::with('plan')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($subscriptions);
    }

    // POST /api/subscriptions  — اشتراك في باقة
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $userId = $request->user()->id;

        // لو عنده اشتراك فعّال → منعه من الاشتراك مرة تانية
        $activeSub = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($activeSub) {
            return response()->json([
                'message' => 'You already have an active subscription',
            ], 422);
        }

        $plan      = Plan::findOrFail($data['plan_id']);
        $startDate = now()->toDateString();
        $endDate   = now()->addMonth()->toDateString(); // باقة شهرية افتراضياً

        $subscription = Subscription::create([
            'user_id'    => $userId,
            'plan_id'    => $plan->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => 'active',
        ]);

        return response()->json([
            'message'      => 'Subscribed successfully',
            'subscription' => $subscription,
            'plan'         => $plan,
        ], 201);
    }
}
