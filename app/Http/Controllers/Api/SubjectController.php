<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $subjects = Subject::query()
            ->with('program:id,course_name')
            ->orderBy('department')
            ->orderBy('subject_name')
            ->paginate($validated['per_page'] ?? 100);

        return response()->json([
            'data' => $subjects->getCollection()->map(fn (Subject $subject) => [
                'id' => $subject->id,
                'code' => $subject->code,
                'subject_name' => $subject->subject_name,
                'program_id' => $subject->program_id,
                'program_name' => $subject->program?->course_name,
                'department' => $subject->department,
                'units' => (int) $subject->units,
            ]),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'last_page' => $subjects->lastPage(),
                'per_page' => $subjects->perPage(),
                'total' => $subjects->total(),
            ],
        ]);
    }
}
