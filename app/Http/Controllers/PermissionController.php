<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'guard_name' => 'sometimes|string|max:255',
        ]);

        $permission = Permission::create($validated);

        return response()->json($permission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'sometimes|string|max:255',
        ]);

        $permission->update($validated);

        return response()->json($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }

    /**
     * Assign permission to user.
     */
    public function assignToUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($validated['permission_id']);
        $user->givePermissionTo($permission);

        return response()->json(['message' => 'Permission assigned successfully']);
    }

    /**
     * Revoke permission from user.
     */
    public function revokeFromUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($validated['permission_id']);
        $user->revokePermissionTo($permission);

        return response()->json(['message' => 'Permission revoked successfully']);
    }
}
