<?php

namespace App\Http\Controllers;

use App\Services\ScormParserService;
use App\Models\Course;
use App\Models\Group;
use App\Models\Aircraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CourseImportController extends Controller
{
    protected $scormService;

    public function __construct(ScormParserService $scormService)
    {
        $this->scormService = $scormService;
    }

    public function create()
    {
        $courses = Course::all();
        $aircrafts = Aircraft::all();
        return inertia('Courses/Import', compact('courses', 'aircrafts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:zip',
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:scorm,gift',
        ]);

        $path = $request->file('file')->store('imports');
        $fullPath = storage_path('app/' . $path);

        try {
            DB::beginTransaction();

            if ($request->type === 'scorm') {
                $this->scormService->parseScorm($fullPath, $request->course_id);
            } elseif ($request->type === 'gift') {
                // Логика парсинга GIFT (упрощенно)
                $content = file_get_contents($fullPath);
                // Здесь должен быть парсер GIFT, создающий Questions и Answers
                // ...
            }

            DB::commit();
            return redirect()->back()->with('success', 'Курс успешно импортирован');
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::delete($path);
            return redirect()->back()->withErrors(['error' => 'Ошибка импорта: ' . $e->getMessage()]);
        }
    }

    public function assignGroups(Request $request)
    {
        $request->validate([
            'group_ids' => 'required|array',
            'course_id' => 'required|exists:courses,id',
            'category_ids' => 'nullable|array', // Для частичной записи
        ]);

        $groups = Group::whereIn('id', $request->group_ids)->get();
        
        foreach ($groups as $group) {
            // Проверка специальности (Aircraft)
            if ($group->aircraft_id) {
                // Можно добавить фильтрацию курсов по специальности
            }

            // Запись группы на курс (или части курса)
            $group->courses()->syncWithoutDetaching([
                $request->course_id => [
                    'categories' => $request->category_ids ? json_encode($request->category_ids) : null
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Группы успешно записаны на курс');
    }
}
