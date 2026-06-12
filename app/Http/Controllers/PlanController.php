<?php
namespace App\Http\Controllers;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index() {
        return response()->json(Plan::all());
    }
    public function show($id) {
        $plan = Plan::find($id);
        if (!$plan) return response()->json(['message' => 'Plan not found'], 404);
        return response()->json($plan);
    }
}