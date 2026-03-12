<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        $courses = Course::query()
            ->withCount('students')
            ->orderBy('course_name')
            ->get();

        return CourseResource::collection($courses)->response();
    }

    public function distribution(): JsonResponse
    {
        $distribution = Course::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get(['id', 'course_name']);

        return response()->json([
            'data' => $distribution->map(fn (Course $course) => [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'student_count' => $course->students_count,
            ]),
        ]);
    }
}
