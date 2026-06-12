<?php
// ════════════════════════════════════════════════════════════════════
//  app/Models/User.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden   = ['password', 'reset_token', 'reset_token_expiry'];
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Meal.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'price', 'image_url'];
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Cart.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id'];

    public function items() {
        return $this->hasMany(CartItem::class);
    }
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/CartItem.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['cart_id', 'meal_id', 'quantity'];

    public function meal() {
        return $this->belongsTo(Meal::class);
    }
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Order.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_price', 'discount_amount', 'final_price', 'status'
    ];

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/OrderItem.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'meal_id', 'quantity', 'price'];

    public function meal() {
        return $this->belongsTo(Meal::class);
    }
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Plan.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'price', 'discount_percentage', 'description'];
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Subscription.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'plan_id', 'start_date', 'end_date', 'status'];

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
}


// ════════════════════════════════════════════════════════════════════
//  app/Models/Payment.php
// ════════════════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'payment_method', 'amount', 'status'];
}
