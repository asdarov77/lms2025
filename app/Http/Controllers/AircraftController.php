<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\Course;
use App\Models\Category;
use App\Models\Aukstructure;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AircraftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        $aircrafts = Aircraft::with('courses.categories')->orderBy('id')->get();
        return response()->json($aircrafts);
    }

    public function show($id)
    {
        $aircraft = Aircraft::with(['courses.aukstructures.links', 'courses.categories'])->findOrFail($id);
        return response()->json($aircraft);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'path' => 'required|string|max:255|unique:aircrafts,path',
        ]);

        $aircraft = Aircraft::create($validated);
        
        // Parse courses for this aircraft
        $this->parseCoursesForAircraft($aircraft);

        return response()->json($aircraft, 201);
    }

    public function showClassesFs()
    {
        $coursesPath = Config::get('app.courses_path');
        $classes = [];
        
        if (is_dir($coursesPath)) {
            $items = array_diff(scandir($coursesPath), ['..', '.']);
            foreach ($items as $item) {
                if (is_dir($coursesPath . '/' . $item)) {
                    $classes[] = $item;
                }
            }
        }
        
        return response()->json(array_values($classes));
    }

    public function showAuks(string $air)
    {
        $coursesPath = Config::get('app.courses_path');
        $fullPath = $coursesPath . '/' . $air;
        $auks = [];
        
        if (is_dir($fullPath)) {
            $auks = array_diff(scandir($fullPath), ['..', '.']);
        }
        
        return response()->json($auks);
    }

    private function parseCoursesForAircraft(Aircraft $aircraft)
    {
        $coursesPath = Config::get('app.courses_path');
        $aircraftPath = $coursesPath . '/' . $aircraft->path;
        
        if (!is_dir($aircraftPath)) {
            return;
        }

        $courseDirs = array_diff(scandir($aircraftPath), ['..', '.']);
        
        foreach ($courseDirs as $courseDir) {
            if (!is_dir($aircraftPath . '/' . $courseDir)) {
                continue;
            }

            $manifestPath = $aircraftPath . '/' . $courseDir . '/imsmanifest.xml';
            
            if (file_exists($manifestPath)) {
                $this->parseManifest($manifestPath, $aircraft->id, $courseDir);
            }
        }
    }

    private function parseManifest(string $manifestPath, int $aircraftId, string $courseTitle)
    {
        $xmlContent = file_get_contents($manifestPath);
        if (!$xmlContent) {
            return;
        }

        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            return;
        }

        // Create or update course
        $course = Course::updateOrCreate(
            ['path' => $courseTitle, 'aircraft_id' => $aircraftId],
            ['title' => $courseTitle]
        );

        // Parse organizations and items
        $organizations = $xml->xpath('//organization');
        
        foreach ($organizations as $org) {
            $this->processOrganization($org, null, $course->id, 0);
        }

        // Parse resources for links
        $resources = $xml->xpath('//resource');
        foreach ($resources as $resource) {
            $href = (string) $resource['href'];
            if ($href && pathinfo($href, PATHINFO_EXTENSION) === 'html') {
                // Links will be created during item processing
            }
        }
    }

    private function processOrganization($org, $parentId, $courseId, $typeLevel)
    {
        $title = (string) $org['identifierref'] ?: (string) $org->title;
        if (empty($title)) {
            $title = 'Без названия';
        }

        $aukstructure = Aukstructure::updateOrCreate(
            [
                'title' => $title,
                'parent_id' => $parentId,
                'course_id' => $courseId,
            ],
            [
                'type' => min($typeLevel, 3),
                'identifier' => (string) $org['identifier'],
            ]
        );

        // Process child items
        $items = $org->xpath('item');
        foreach ($items as $item) {
            $this->processItem($item, $aukstructure->id, $courseId, $typeLevel + 1);
        }
    }

    private function processItem($item, $parentId, $courseId, $typeLevel)
    {
        $title = (string) $item->title ?: 'Без названия';
        
        $aukstructure = Aukstructure::updateOrCreate(
            [
                'title' => $title,
                'parent_id' => $parentId,
                'course_id' => $courseId,
            ],
            [
                'type' => min($typeLevel, 3),
                'identifier' => (string) $item['identifier'],
            ]
        );

        // Create links for type 3 (modules)
        if ($typeLevel >= 3) {
            $resourceId = (string) $item['identifierref'];
            if ($resourceId) {
                $this->createLinksForResource($resourceId, $aukstructure->id);
            }
        }

        // Process nested items
        $nestedItems = $item->xpath('item');
        foreach ($nestedItems as $nestedItem) {
            $this->processItem($nestedItem, $aukstructure->id, $courseId, $typeLevel + 1);
        }
    }

    private function createLinksForResource($resourceId, $aukstructureId)
    {
        // This would need the full XML to resolve resource references
        // Simplified implementation - in real scenario you'd pass the XML document
    }
}
