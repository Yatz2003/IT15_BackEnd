<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $latestSchoolDay = SchoolDay::query()->latest('date')->first();

        return response()->json([
            'data' => [
                'students_total' => Student::query()->count(),
                'courses_total' => Course::query()->count(),
                'school_days_total' => SchoolDay::query()->count(),
                'average_attendance_rate' => round((float) SchoolDay::query()
                    ->where('is_holiday', false)
                    ->avg('attendance_rate'), 2),
                'latest_school_day' => $latestSchoolDay ? [
                    'date' => Carbon::parse($latestSchoolDay->date)->toDateString(),
                    'attendance_rate' => (float) $latestSchoolDay->attendance_rate,
                    'is_holiday' => $latestSchoolDay->is_holiday,
                ] : null,
            ],
        ]);
    }
}
