<?php
namespace App\Http\Controllers;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request) {
        return response()->json(Subscription::with('plan')->where('user_id', $request->user()->id)->latest('id')->get());
    }
    public function store(Request $request) {
        $data = $request->validate(['plan_id' => 'required|exists:plans,id']);
        $userId = $request->user()->id;
        $active = Subscription::where('user_id', $userId)->where('status', 'active')->first();
        if ($active) return response()->json(['message' => 'Already subscribed'], 422);
        $sub = Subscription::create([
            'user_id'    => $userId,
            'plan_id'    => $data['plan_id'],
            'start_date' => now()->toDateString(),
            'end_date'   => now()->addMonth()->toDateString(),
            'status'     => 'active',
        ]);
        return response()->json(['message' => 'Subscribed successfully', 'subscription' => $sub], 201);
    }
}