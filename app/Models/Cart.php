<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id'];
    public function items() { return $this->hasMany(CartItem::class); }
}