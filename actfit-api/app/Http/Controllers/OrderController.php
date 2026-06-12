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
    // GET /api/orders  — كل طلبات المستخدم
    public function index(Request $request)
    {
        $orders = Order::with('orderItems.meal')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // GET /api/orders/{id}  — تفاصيل طلب معين
    public function show(Request $request, $id)
    {
        $order = Order::with('orderItems.meal')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($order);
    }

    // POST /api/orders  — إنشاء طلب من العربية
    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $cart   = Cart::where('user_id', $userId)->firstOrFail();

        $cartItems = CartItem::with('meal')->where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        // حساب السعر الإجمالي
        $totalPrice = $cartItems->sum(fn($i) => $i->quantity * $i->meal->price);

        // لو عنده اشتراك فعّال → طبّق الخصم
        $discountAmount = 0;
        $activeSub = Subscription::with('plan')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
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

            // امسح العربية بعد الطلب
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
