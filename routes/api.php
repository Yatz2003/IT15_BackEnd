<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard summary is protected because it exposes aggregated internal metrics.
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
});

Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/enrollment-trends', [StudentController::class, 'enrollmentTrends']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/distribution', [CourseController::class, 'distribution']);

Route::get('/school-days', [SchoolDayController::class, 'index']);
Route::get('/attendance', [SchoolDayController::class, 'attendance']);