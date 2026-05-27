<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Aukstructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\GiftParser\GiftParser;

class GiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['message' => 'GIFT upload endpoint']);
    }

    /**
     * Store a newly created resource in storage.
     * Parse GIFT file and create questions/answers
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt,gift',
        ]);

        $file = $request->file('file');
        $giftContent = file_get_contents($file->getRealPath());

        $parser = new GiftParser();
        $result = $parser->parse($giftContent);

        return response()->json([
            'message' => 'Вопросы успешно импортированы',
            'imported' => $result
        ], 200);
    }

    /**
     * Import from file path (internal use)
     */
    public function importFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $giftContent = file_get_contents($filePath);
        $parser = new GiftParser();
        
        return $parser->parse($giftContent);
    }

    /**
     * Truncate questions and answers tables
     */
    public function truncate()
    {
        DB::table('questions')->truncate();
        DB::table('answers')->truncate();

        return response()->json([
            'message' => 'Таблицы questions & answers успешно очищены'
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        // Not used for GIFT import
    }
}
