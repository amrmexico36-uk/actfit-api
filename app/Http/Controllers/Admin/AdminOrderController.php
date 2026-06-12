<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.meal')->latest()->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $data['status']]);

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}