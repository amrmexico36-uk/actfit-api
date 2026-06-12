<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id', 'plan_id', 'start_date', 'end_date', 'status'];
    public function plan() { return $this->belongsTo(Plan::class); }
}