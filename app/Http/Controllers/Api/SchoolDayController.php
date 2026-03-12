<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolDayResource;
use App\Models\SchoolDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class SchoolDayController extends Controller
{
    public function index(): JsonResponse
    {
        $days = SchoolDay::query()
            ->orderByDesc('date')
            ->paginate(60);

        return SchoolDayResource::collection($days)->response();
    }

    public function attendance(): JsonResponse
    {
        $attendanceSeries = SchoolDay::query()
            ->where('is_holiday', false)
            ->orderBy('date')
            ->get(['date', 'attendance_rate']);

        return response()->json([
            'data' => $attendanceSeries->map(fn (SchoolDay $day) => [
                'date' => Carbon::parse($day->date)->toDateString(),
                'attendance_rate' => (float) $day->attendance_rate,
            ]),
            'summary' => [
                'average_attendance_rate' => round((float) $attendanceSeries->avg('attendance_rate'), 2),
                'days_counted' => $attendanceSeries->count(),
            ],
        ]);
    }
}
