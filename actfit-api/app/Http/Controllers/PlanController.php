<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class PlanController extends Controller
{
    // GET /api/plans
    public function index()
    {
        return response()->json(Plan::all());
    }

    // GET /api/plans/{id}
    public function show($id)
    {
        $plan = Plan::find($id);

        if (! $plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        return response()->json($plan);
    }
}
