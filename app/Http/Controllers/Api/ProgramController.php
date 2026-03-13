<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $programs = Course::query()
            ->withCount('students')
            ->orderBy('course_name')
            ->paginate($validated['per_page'] ?? 100);

        $rows = $programs->getCollection()->map(fn (Course $program) => [
            'id' => $program->id,
            'program_name' => $program->course_name,
            'department' => $program->department,
            'enrolled_students' => (int) $program->students_count,
        ]);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $programs->currentPage(),
                'last_page' => $programs->lastPage(),
                'per_page' => $programs->perPage(),
                'total' => $programs->total(),
            ],
            'charts' => [
                'students_by_department' => $this->studentsByDepartment($rows),
            ],
        ]);
    }

    private function studentsByDepartment(Collection $rows): Collection
    {
        return $rows
            ->groupBy('department')
            ->map(fn (Collection $group, string $department) => [
                'department' => $department,
                'students_total' => (int) $group->sum('enrolled_students'),
            ])
            ->values();
    }
}
