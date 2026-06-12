<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['user_id', 'total_price', 'discount_amount', 'final_price', 'status'];
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}