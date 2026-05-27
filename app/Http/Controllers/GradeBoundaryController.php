<?php

namespace App\Http\Controllers;

use App\Models\GradeBoundary;
use Illuminate\Http\Request;

class GradeBoundaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $boundaries = GradeBoundary::orderBy('boundary')->get();
        return response()->json($boundaries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'boundary' => 'required|integer|min:0|max:100',
            'grade' => 'required|string|max:10',
        ]);

        $boundary = GradeBoundary::updateOrCreate(
            ['boundary' => $request->boundary],
            ['grade' => $request->grade]
        );

        return response()->json($boundary, 200);
    }
}
