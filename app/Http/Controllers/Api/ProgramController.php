<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProgramsIndexRequest;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(ProgramsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $programsQuery = Program::query()
            ->withCount('students')
            ->orderBy('program_name');

        if (! empty($validated['department'])) {
            $programsQuery->where('department', $validated['department']);
        }

        if (array_key_exists('is_active', $validated)) {
            $programsQuery->where('is_active', $validated['is_active']);
        }

        $programs = $programsQuery->paginate($validated['per_page'] ?? 100);

        $rows = $programs->getCollection()->map(fn (Program $program) => [
            'id' => $program->id,
            'program_name' => $program->program_name,
            'course_name' => $program->program_name,
            'course' => $program->program_name,
            'department' => $program->department,
            'is_active' => (bool) $program->is_active,
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
        ]);
    }
}
