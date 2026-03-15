<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EnrollmentsIndexRequest;
use App\Http\Requests\Api\RemoveEnrollmentRequest;
use App\Http\Requests\Api\StoreEnrollmentRequest;
use App\Http\Requests\Api\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Subject;
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
                'program:id,program_name,department',
            ])
            ->latest('enrolled_at');

        if (isset($validated['student_id'])) {
            $enrollmentsQuery->where('student_id', $validated['student_id']);
        }

        if (isset($validated['subject_id'])) {
            $enrollmentsQuery->where('subject_id', $validated['subject_id']);
        }

        if (isset($validated['program_id'])) {
            $enrollmentsQuery->where('program_id', $validated['program_id']);
        }

        $enrollments = $enrollmentsQuery->paginate($validated['per_page'] ?? 50);

        return response()->json([
            'data' => $enrollments->getCollection()->map(fn (Enrollment $enrollment) => [
                ...$this->transformEnrollment($enrollment),
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $enrollment = Enrollment::query()
            ->with(['student:id,name,email,program_id,year_level', 'subject:id,subject_name,program_id', 'program:id,program_name,department'])
            ->findOrFail($id);

        return response()->json($this->transformEnrollment($enrollment));
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! $this->programMatches($validated['subject_id'], $validated['program_id'])) {
            return response()->json([
                'message' => 'Selected subject does not belong to the specified program.',
                'error_code' => 'enrollment_program_subject_mismatch',
            ], 422);
        }

        $enrollment = Enrollment::query()->create([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'program_id' => $validated['program_id'],
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'status' => $validated['status'],
            'enrolled_at' => $validated['enrolled_at'] ?? now(),
        ]);

        $enrollment->load(['student:id,name,email,program_id,year_level', 'subject:id,subject_name,program_id', 'program:id,program_name,department']);

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'data' => $this->transformEnrollment($enrollment),
        ], 201);
    }

    public function enroll(StoreEnrollmentRequest $request): JsonResponse
    {
        return $this->store($request);
    }

    public function update(UpdateEnrollmentRequest $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::query()->findOrFail($id);
        $validated = $request->validated();

        $subjectId = $validated['subject_id'] ?? $enrollment->subject_id;
        $programId = $validated['program_id'] ?? $enrollment->program_id;

        if (! $this->programMatches((int) $subjectId, (int) $programId)) {
            return response()->json([
                'message' => 'Selected subject does not belong to the specified program.',
                'error_code' => 'enrollment_program_subject_mismatch',
            ], 422);
        }

        $enrollment->fill([
            'student_id' => $validated['student_id'] ?? $enrollment->student_id,
            'subject_id' => $subjectId,
            'program_id' => $programId,
            'academic_year' => $validated['academic_year'] ?? $enrollment->academic_year,
            'semester' => $validated['semester'] ?? $enrollment->semester,
            'status' => $validated['status'] ?? $enrollment->status,
            'enrolled_at' => $validated['enrolled_at'] ?? $enrollment->enrolled_at,
        ]);

        $enrollment->save();
        $enrollment->load(['student:id,name,email,program_id,year_level', 'subject:id,subject_name,program_id', 'program:id,program_name,department']);

        return response()->json([
            'message' => 'Enrollment updated successfully.',
            'data' => $this->transformEnrollment($enrollment),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $enrollment = Enrollment::query()->findOrFail($id);
        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully.',
        ]);
    }

    public function remove(RemoveEnrollmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! empty($validated['enrollment_id'])) {
            $enrollment = Enrollment::query()->findOrFail((int) $validated['enrollment_id']);
        } else {
            $enrollment = Enrollment::query()
                ->where('student_id', $validated['student_id'])
                ->where('subject_id', $validated['subject_id'])
                ->first();

            if (! $enrollment) {
                return response()->json([
                    'message' => 'Enrollment not found for the selected student and subject.',
                    'error_code' => 'enrollment_not_found',
                ], 404);
            }
        }

        $removed = $this->transformEnrollment($enrollment);
        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment removed successfully.',
            'data' => $removed,
        ]);
    }

    private function programMatches(int $subjectId, int $programId): bool
    {
        $subjectProgramId = Subject::query()->whereKey($subjectId)->value('program_id');

        return (int) $subjectProgramId === $programId;
    }

    private function transformEnrollment(Enrollment $enrollment): array
    {
        $academicYear = (string) ($enrollment->getAttribute('academic_year') ?? '');
        $semester = (string) ($enrollment->getAttribute('semester') ?? '');
        $status = (string) ($enrollment->getAttribute('status') ?? '');

        return [
            'id' => (int) $enrollment->id,
            'student_name' => $enrollment->student?->name,
            'subject_name' => $enrollment->subject?->subject_name,
            'program' => $enrollment->program?->program_name,
            'academic_year' => $academicYear,
            'semester' => $semester,
            'status' => $status,
            'student' => $enrollment->student?->name,
            'subject' => $enrollment->subject?->subject_name,
            'year' => $academicYear,
        ];
    }
}
