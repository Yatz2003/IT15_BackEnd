<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/weather', [WeatherController::class, 'show']);
Route::get('/dashboard/weather', [WeatherController::class, 'show']);
Route::get('/dashboard/mini-weather', [WeatherController::class, 'mini']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/students', [StudentController::class, 'index']);

    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/courses', [ProgramController::class, 'index']);
    Route::get('/courses-offered', [ProgramController::class, 'index']);
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/school-days', [SchoolDayController::class, 'index']);
    Route::get('/academic-calendar', [SchoolDayController::class, 'index']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/enrollment-trends', [DashboardController::class, 'enrollmentTrends']);
    Route::get('/dashboard/enrollment-analytics', [DashboardController::class, 'enrollmentAnalytics']);
    Route::get('/dashboard/enrollment-analytics-yearly', [DashboardController::class, 'enrollmentAnalyticsYearly']);
    Route::get('/dashboard/program-distribution', [DashboardController::class, 'programDistribution']);
    Route::get('/dashboard/attendance-patterns', [DashboardController::class, 'attendancePatterns']);
    Route::get('/dashboard/reliability-snapshot', [DashboardController::class, 'reliabilitySnapshot']);
    Route::get('/dashboard/room-assignments', [DashboardController::class, 'roomAssignments']);
    Route::get('/dashboard/room-availability', [DashboardController::class, 'roomAvailability']);
    Route::get('/rooms', [DashboardController::class, 'rooms']);

    // Backward-compatible alias for existing frontend calls.
    Route::get('/attendance', [DashboardController::class, 'attendancePatterns']);
});