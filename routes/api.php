<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/enrollment-trends', [StudentController::class, 'enrollmentTrends']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/distribution', [CourseController::class, 'distribution']);

    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/school-days', [SchoolDayController::class, 'index']);
    Route::get('/attendance', [SchoolDayController::class, 'attendance']);

    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

    // Backward-compatible alias.
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
});