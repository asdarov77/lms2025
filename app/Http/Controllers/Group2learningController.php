<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class Group2learningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with('users')->get();
        return response()->json($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'array',
        ]);

        $group = Group::create([
            'name' => $validated['name'],
        ]);

        if (isset($validated['user_ids'])) {
            $group->users()->sync($validated['user_ids']);
        }

        return response()->json($group->load('users'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return response()->json($group->load('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'user_ids' => 'array',
        ]);

        if (isset($validated['name'])) {
            $group->name = $validated['name'];
            $group->save();
        }

        if (isset($validated['user_ids'])) {
            $group->users()->sync($validated['user_ids']);
        }

        return response()->json($group->load('users'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(['message' => 'Group deleted successfully']);
    }

    /**
     * Add users to group.
     */
    public function addUsers(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
        ]);

        $group->users()->syncWithoutDetaching($validated['user_ids']);

        return response()->json($group->load('users'));
    }

    /**
     * Remove users from group.
     */
    public function removeUsers(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
        ]);

        $group->users()->detach($validated['user_ids']);

        return response()->json($group->load('users'));
    }
}
