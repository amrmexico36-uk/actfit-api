<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
    public $timestamps = false;
    protected $fillable = ['cart_id', 'meal_id', 'quantity'];
    public function meal() { return $this->belongsTo(Meal::class); }
}