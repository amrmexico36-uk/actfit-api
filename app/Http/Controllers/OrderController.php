<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('orderItems.meal')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->get();

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with('orderItems.meal')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $cart   = Cart::where('user_id', $userId)->firstOrFail();

        $cartItems = CartItem::with('meal')->where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $totalPrice = $cartItems->sum(fn($i) => $i->quantity * $i->meal->price);

        $discountAmount = 0;
        $activeSub = Subscription::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if ($activeSub) {
            $discountPct    = $activeSub->plan->discount_percentage ?? 0;
            $discountAmount = round($totalPrice * $discountPct / 100, 2);
        }

        $finalPrice = $totalPrice - $discountAmount;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'         => $userId,
                'total_price'     => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price'     => $finalPrice,
                'status'          => 'pending',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id'  => $item->meal_id,
                    'quantity' => $item->quantity,
                    'price'    => $item->meal->price,
                ]);
            }

            CartItem::where('cart_id', $cart->id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message'         => 'Order placed successfully',
            'order_id'        => $order->id,
            'total_price'     => $totalPrice,
            'discount_amount' => $discountAmount,
            'final_price'     => $finalPrice,
        ], 201);
    }
}