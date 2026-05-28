<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Group::with(['users', 'courses']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $groups = $query->paginate(15);
        return response()->json($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups',
            'description' => 'nullable|string',
            'user_ids' => 'array',
            'course_ids' => 'array',
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['user_ids'])) {
            $group->users()->sync($validated['user_ids']);
        }

        if (isset($validated['course_ids'])) {
            $group->courses()->sync($validated['course_ids']);
        }

        return response()->json($group->load(['users', 'courses']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return response()->json($group->load(['users', 'courses']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:groups,name,' . $group->id,
            'description' => 'nullable|string',
            'user_ids' => 'array',
            'course_ids' => 'array',
        ]);

        if (isset($validated['name'])) {
            $group->name = $validated['name'];
        }

        if (isset($validated['description'])) {
            $group->description = $validated['description'];
        } elseif ($request->has('description')) {
            $group->description = null;
        }

        $group->save();

        if (isset($validated['user_ids'])) {
            $group->users()->sync($validated['user_ids']);
        }

        if (isset($validated['course_ids'])) {
            $group->courses()->sync($validated['course_ids']);
        }

        return response()->json($group->load(['users', 'courses']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(null, 204);
    }

    /**
     * Add users to group.
     */
    public function addUsers(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->users()->syncWithoutDetaching($validated['user_ids']);

        return response()->json($group->load(['users']));
    }

    /**
     * Remove users from group.
     */
    public function removeUsers(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->users()->detach($validated['user_ids']);

        return response()->json($group->load(['users']));
    }

    /**
     * Add courses to group.
     */
    public function addCourses(Request $request, Group $group)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $group->courses()->syncWithoutDetaching($validated['course_ids']);

        return response()->json($group->load(['courses']));
    }

    /**
     * Remove courses from group.
     */
    public function removeCourses(Request $request, Group $group)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $group->courses()->detach($validated['course_ids']);

        return response()->json($group->load(['courses']));
    }
}
