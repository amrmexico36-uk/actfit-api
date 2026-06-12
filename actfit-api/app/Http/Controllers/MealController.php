<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    // GET /api/meals
    public function index()
    {
        return response()->json(Meal::all());
    }

    // GET /api/meals/{id}
    public function show($id)
    {
        $meal = Meal::find($id);

        if (! $meal) {
            return response()->json(['message' => 'Meal not found'], 404);
        }

        return response()->json($meal);
    }
}
