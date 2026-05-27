<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Group2learningController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrivateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GradeBoundaryController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'getUserList']);
    Route::get('/users/{id}', [AuthController::class, 'getUser']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::put('/users/{id}/password', [AuthController::class, 'chpass']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
    Route::post('/group2learning', [AuthController::class, 'group2learning']);

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
    Route::post('/aircrafts', [AircraftController::class, 'store']);

    // Questions
    Route::get('/questions', [QuestionsController::class, 'index']);
    Route::get('/questions/{id}', [QuestionsController::class, 'show']);
    Route::post('/questions', [QuestionsController::class, 'store']);
    Route::put('/questions/{id}', [QuestionsController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionsController::class, 'destroy']);

    // Groups
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::put('/groups/{id}', [GroupController::class, 'update']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);

    // Group2learnings
    Route::get('/group2learnings', [Group2learningController::class, 'index']);
    Route::get('/group2learnings/{id}', [Group2learningController::class, 'show']);
    Route::put('/group2learnings/{id}', [Group2learningController::class, 'update']);
    Route::delete('/group2learnings/{id}', [Group2learningController::class, 'destroy']);

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);

    // Private files streaming
    Route::get('/private/{aircraft}/{auk}/{path?}', [PrivateController::class, 'stream'])
        ->where('path', '.*');

    // Search
    Route::post('/search', [SearchController::class, 'search']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'add']);
    Route::delete('/favorites/{course_id}', [FavoriteController::class, 'remove']);

    // Grade boundaries
    Route::get('/grade-boundaries', [GradeBoundaryController::class, 'index']);
    Route::post('/grade-boundaries', [GradeBoundaryController::class, 'store']);
});
