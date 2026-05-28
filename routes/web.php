<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseImportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrivateController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\AircraftController;

// Главная
Route::get('/', function () {
    return redirect('/login');
});

// Аутентификация
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Защищенные маршруты
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Импорт курсов и назначение групп
    Route::get('/courses/import', [CourseImportController::class, 'create'])->name('courses.import.create');
    Route::post('/courses/import', [CourseImportController::class, 'store'])->name('courses.import.store');
    Route::post('/courses/assign-groups', [CourseImportController::class, 'assignGroups'])->name('courses.assign.groups');
    
    // Управление ролями и разрешениями (RBAC Admin)
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Приватный доступ к файлам (защита от хотлинка)
    Route::get('/private/stream/{path}', [PrivateController::class, 'stream'])->where('path', '.*')->name('private.stream');
    
    // CRUD сущности
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('questions', QuestionsController::class);
    Route::apiResource('aircrafts', AircraftController::class);
    
    // GIFT импорт
    Route::post('/gift/import', [GiftController::class, 'import'])->name('gift.import');
});
