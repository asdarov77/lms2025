<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Aukstructure;
use App\Http\Filters\QuestionFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $filter = new QuestionFilter($request->query());
        
        $questions = Question::filter($filter)
            ->with(['answers', 'category', 'aukstructure'])
            ->paginate(20);

        return response()->json($questions);
    }

    public function show($id)
    {
        $question = Question::with(['answers', 'category', 'aukstructure'])->findOrFail($id);
        return response()->json($question);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'aukstructure_id' => 'nullable|exists:aukstructures,id',
            'answers' => 'nullable|array',
            'answers.*.answer' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $question = Question::create([
                'question_text' => $validated['question_text'],
                'category_id' => $validated['category_id'] ?? null,
                'aukstructure_id' => $validated['aukstructure_id'] ?? null,
            ]);

            if (isset($validated['answers'])) {
                foreach ($validated['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer' => $answerData['answer'],
                        'is_correct' => $answerData['is_correct'] ?? false,
                    ]);
                }
            }

            DB::commit();
            return response()->json($question->load('answers'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating question', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        $validated = $request->validate([
            'question_text' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'aukstructure_id' => 'nullable|exists:aukstructures,id',
            'answers' => 'nullable|array',
            'answers.*.id' => 'nullable|exists:answers,id',
            'answers.*.answer' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $question->update($validated);

            if (isset($validated['answers'])) {
                foreach ($validated['answers'] as $answerData) {
                    if (isset($answerData['id'])) {
                        $answer = Answer::find($answerData['id']);
                        $answer->update($answerData);
                    } else {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer' => $answerData['answer'],
                            'is_correct' => $answerData['is_correct'] ?? false,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json($question->load('answers'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating question', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        
        return response()->json(null, 204);
    }
}
