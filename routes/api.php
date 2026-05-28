<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Group2learningController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrivateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GradeBoundaryController;
use App\Http\Controllers\GiftController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Users
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/roles', [UserController::class, 'assignRoles']);
    Route::post('/users/{id}/groups', [UserController::class, 'assignGroups']);
    
    // Legacy user routes (redirect to UserController)
    Route::put('/users/{id}/password', [AuthController::class, 'chpass']);

    // Groups
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::put('/groups/{id}', [GroupController::class, 'update']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
    Route::post('/groups/{id}/users', [GroupController::class, 'addUsers']);
    Route::delete('/groups/{id}/users', [GroupController::class, 'removeUsers']);
    Route::post('/groups/{id}/courses', [GroupController::class, 'addCourses']);
    Route::delete('/groups/{id}/courses', [GroupController::class, 'removeCourses']);

    // Group2learnings
    Route::get('/group2learnings', [Group2learningController::class, 'index']);
    Route::get('/group2learnings/{id}', [Group2learningController::class, 'show']);
    Route::post('/group2learnings', [Group2learningController::class, 'store']);
    Route::put('/group2learnings/{id}', [Group2learningController::class, 'update']);
    Route::delete('/group2learnings/{id}', [Group2learningController::class, 'destroy']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/courses/{id}/link', [CourseController::class, 'getLink']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Aircraft
    Route::get('/aircrafts', [AircraftController::class, 'index']);
    Route::get('/aircrafts/{id}', [AircraftController::class, 'show']);
    Route::post('/aircrafts', [AircraftController::class, 'store']);
    Route::put('/aircrafts/{id}', [AircraftController::class, 'update']);
    Route::delete('/aircrafts/{id}', [AircraftController::class, 'destroy']);

    // Questions
    Route::get('/questions', [QuestionsController::class, 'index']);
    Route::get('/questions/{id}', [QuestionsController::class, 'show']);
    Route::post('/questions', [QuestionsController::class, 'store']);
    Route::put('/questions/{id}', [QuestionsController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionsController::class, 'destroy']);

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'show']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    // Grade boundaries
    Route::get('/grade-boundaries', [GradeBoundaryController::class, 'index']);
    Route::get('/grade-boundaries/{id}', [GradeBoundaryController::class, 'show']);
    Route::post('/grade-boundaries', [GradeBoundaryController::class, 'store']);
    Route::put('/grade-boundaries/{id}', [GradeBoundaryController::class, 'update']);
    Route::delete('/grade-boundaries/{id}', [GradeBoundaryController::class, 'destroy']);

    // GIFT Import
    Route::post('/gift/import', [GiftController::class, 'import']);

    // Private files streaming
    Route::get('/private/{aircraft}/{auk}/{path?}', [PrivateController::class, 'stream'])
        ->where('path', '.*');

    // Search
    Route::post('/search', [SearchController::class, 'search']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'add']);
    Route::delete('/favorites/{course_id}', [FavoriteController::class, 'remove']);
});
