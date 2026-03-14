<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EnrollmentsIndexRequest;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    public function index(EnrollmentsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $enrollmentsQuery = Enrollment::query()
            ->with([
                'student:id,name,email,program_id,year_level',
                'subject:id,subject_name,program_id',
                'subject.program:id,program_name,department',
            ])
            ->latest('enrolled_at');

        if (isset($validated['student_id'])) {
            $enrollmentsQuery->where('student_id', $validated['student_id']);
        }

        if (isset($validated['subject_id'])) {
            $enrollmentsQuery->where('subject_id', $validated['subject_id']);
        }

        if (isset($validated['program_id'])) {
            $enrollmentsQuery->whereHas('subject', function ($query) use ($validated): void {
                $query->where('program_id', $validated['program_id']);
            });
        }

        $enrollments = $enrollmentsQuery->paginate($validated['per_page'] ?? 50);

        return response()->json([
            'data' => $enrollments->getCollection()->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->id,
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
                'student' => [
                    'id' => $enrollment->student?->id,
                    'name' => $enrollment->student?->name,
                    'email' => $enrollment->student?->email,
                    'year_level' => $enrollment->student?->year_level,
                ],
                'subject' => [
                    'id' => $enrollment->subject?->id,
                    'subject_name' => $enrollment->subject?->subject_name,
                ],
                'program' => $enrollment->subject?->program ? [
                    'id' => $enrollment->subject->program->id,
                    'program_name' => $enrollment->subject->program->program_name,
                    'department' => $enrollment->subject->program->department,
                ] : null,
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
