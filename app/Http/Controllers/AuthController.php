<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group2learning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login', 'register']);
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'fio' => 'required|string',
            'password' => 'required|string|confirmed',
            'group_id' => 'nullable|exists:groups,id',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'fio' => $fields['fio'],
            'password' => Hash::make($fields['password']),
            'group_id' => $request->group_id,
            'role' => $request->role ?? 'Пользователь',
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'fio' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('fio', $fields['fio'])->first();
        
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'неверный логин или пароль'], 401);
        }

        $token = $user->createToken($request->fio)->plainTextToken;
        $permissions = $user->permissions;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'permissions' => $permissions
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'logout'], 200);
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return response()->json(['message' => 'невозможно удалить супер пользователя'], 500);
        }
        
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function getUserList()
    {
        if (Auth::user()->role === 'Администратор') {
            $users = User::with(['group', 'permissions'])->orderBy('id')->get();
        } else {
            $users = User::with(['group', 'permissions'])
                ->where('group_id', Auth::user()->group_id)
                ->orderBy('id')
                ->get();
        }
        
        return response()->json($users);
    }

    public function getUser($id)
    {
        $user = User::with(['group', 'permissions'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $user->update($request->only([
            'fio', 'role', 'phonenumber', 'city', 'country', 
            'organization', 'position', 'rank', 'spfere', 'specialization', 'group_id'
        ]));

        if ($request->has('permission_id')) {
            $user->permissions()->sync($request->permission_id);
        }

        return response()->json($user, 200);
    }

    public function chpass(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();
        
        return response()->json($user, 200);
    }

    public function group2learning(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'category_id' => 'required|exists:categories,id',
            'course_id' => 'required|array',
            'course_id.*' => 'exists:courses,id',
            'teacher' => 'nullable|string',
            'typeOfLesson' => 'nullable|string',
            'study_from' => 'required|date',
            'study_to' => 'required|date|after_or_equal:study_from',
        ]);

        foreach ($request->course_id as $courseId) {
            Group2learning::create([
                'group_id' => $request->group_id,
                'category_id' => $request->category_id,
                'course_id' => $courseId,
                'teacher' => $request->teacher,
                'typeOfLesson' => $request->typeOfLesson,
                'study_from' => $request->study_from,
                'study_to' => $request->study_to,
            ]);
        }

        return response()->json(['message' => 'Группа успешно записана на курсы'], 200);
    }
}
