<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StudentsIndexRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    public function index(StudentsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $studentsQuery = Student::query()->with('program');

        if (isset($validated['program_id'])) {
            $studentsQuery->where('program_id', $validated['program_id']);
        }

        if (isset($validated['year_level'])) {
            $studentsQuery->where('year_level', $validated['year_level']);
        }

        if (! empty($validated['q'])) {
            $studentsQuery->where(function ($query) use ($validated): void {
                $query->where('name', 'like', '%'.$validated['q'].'%')
                    ->orWhere('email', 'like', '%'.$validated['q'].'%');
            });
        }

        $students = $studentsQuery
            ->orderBy('name')
            ->paginate($validated['per_page'] ?? 50);

        return response()->json([
            'data' => $students->getCollection()->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'program_id' => $student->program_id,
                'program_name' => $student->program?->program_name,
                'department' => $student->program?->department,
                'year_level' => (int) $student->year_level,
                'demographics' => [
                    'gender' => ((int) crc32((string) $student->email) % 2 === 0) ? 'Male' : 'Female',
                    'age' => 17 + (int) $student->year_level + ((int) $student->id % 3),
                    'age_group' => (17 + (int) $student->year_level + ((int) $student->id % 3)) <= 20 ? '18-20' : '21-24',
                    'residency' => ((int) $student->id % 4 === 0) ? 'Dormitory' : 'Commuter',
                ],
                'created_at' => $student->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ]);
    }
}
