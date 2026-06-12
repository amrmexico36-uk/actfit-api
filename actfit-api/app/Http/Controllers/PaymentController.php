<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // POST /api/payments  — دفع طلب
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'payment_method' => 'required|in:Visa,Cash,Wallet',
        ]);

        $userId = $request->user()->id;

        // تأكيد إن الطلب بتاع المستخدم ده
        $order = Order::where('id', $data['order_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        // لو الطلب اتدفع قبل كده
        $existing = Payment::where('order_id', $order->id)->first();
        if ($existing && $existing->status === 'paid') {
            return response()->json(['message' => 'Order already paid'], 422);
        }

        $payment = Payment::create([
            'order_id'       => $order->id,
            'payment_method' => $data['payment_method'],
            'amount'         => $order->final_price,
            'status'         => 'paid',
        ]);

        // غيّر حالة الطلب لـ completed
        $order->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Payment successful',
            'payment' => $payment,
        ], 201);
    }
}
