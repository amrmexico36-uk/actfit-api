<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;

class AdminMealController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image_url'   => 'nullable|string',
        ]);

        $meal = Meal::create($data);

        return response()->json(['message' => 'Meal created', 'meal' => $meal], 201);
    }

    public function update(Request $request, $id)
    {
        $meal = Meal::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'image_url'   => 'nullable|string',
        ]);

        $meal->update($data);

        return response()->json(['message' => 'Meal updated', 'meal' => $meal]);
    }

    public function destroy($id)
    {
        $meal = Meal::findOrFail($id);
        $meal->delete();

        return response()->json(['message' => 'Meal deleted']);
    }
}