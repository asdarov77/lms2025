<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $categories = Category::with('courses')->paginate(15);
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::with(['courses.aukstructures', 'aircraft'])->findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:50',
            'aircraft_id' => 'nullable|exists:aircrafts,id',
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:50',
            'aircraft_id' => 'nullable|exists:aircrafts,id',
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        return response()->json(null, 204);
    }
}
