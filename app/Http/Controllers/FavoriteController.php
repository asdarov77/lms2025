<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())->get();
        return response()->json($favorites);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
        ]);

        $favorite = Favorite::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $request->course_id,
            ],
            [
                'title' => $request->title,
            ]
        );

        return response()->json($favorite, 200);
    }

    public function remove($courseId)
    {
        Favorite::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->delete();

        return response()->json(['message' => 'Removed from favorites'], 200);
    }
}
