<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $studentsPerProgram = Course::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get(['id', 'course_name']);

        return response()->json([
            'data' => [
                'overview' => [
                    'students_total' => Student::query()->count(),
                    'programs_total' => Course::query()->count(),
                    'subjects_total' => Subject::query()->count(),
                    'enrollments_total' => Student::query()->count(),
                ],
                'attendance' => [
                    'average_rate' => round((float) SchoolDay::query()
                        ->where('is_holiday', false)
                        ->avg('attendance_rate'), 2),
                    'days_counted' => SchoolDay::query()->where('is_holiday', false)->count(),
                ],
                'students_per_program' => $studentsPerProgram->map(fn (Course $course) => [
                    'program_id' => $course->id,
                    'program_name' => $course->course_name,
                    'student_count' => (int) $course->students_count,
                ]),
            ],
        ]);
    }
}
