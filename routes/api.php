<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/students', [StudentController::class, 'index']);

    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);



    Route::get('/students/enrollment-trends', [DashboardController::class, 'enrollmentTrends']);
    Route::get('/courses/distribution', [DashboardController::class, 'courseDistribution']);
    Route::get('/attendance', [DashboardController::class, 'attendancePatterns']);
});