<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'price'               => 'required|numeric|min:0',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'description'         => 'nullable|string',
        ]);

        $plan = Plan::create($data);

        return response()->json(['message' => 'Plan created', 'plan' => $plan], 201);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $data = $request->validate([
            'name'                => 'sometimes|string|max:100',
            'price'               => 'sometimes|numeric|min:0',
            'discount_percentage' => 'sometimes|integer|min:0|max:100',
            'description'         => 'nullable|string',
        ]);

        $plan->update($data);

        return response()->json(['message' => 'Plan updated', 'plan' => $plan]);
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Plan deleted']);
    }
}