<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $enrollments = Student::query()
            ->with('program:id,course_name,department')
            ->latest('created_at')
            ->paginate($validated['per_page'] ?? 50);

        return response()->json([
            'data' => $enrollments->getCollection()->map(fn (Student $student) => [
                'enrollment_id' => $student->id,
                'program_id' => $student->program_id ?? $student->course_id,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                ],
                'program' => $student->program ? [
                    'id' => $student->program->id,
                    'name' => $student->program->course_name,
                    'department' => $student->program->department,
                ] : null,
                'status' => 'active',
                'enrolled_at' => Carbon::parse($student->created_at)->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }
}
