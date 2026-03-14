<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubjectsIndexRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(SubjectsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $subjectsQuery = Subject::query()
            ->with([
                'program:id,program_name,department',
                'prerequisite:id,subject_name',
            ])
            ->orderBy('subject_name');

        if (isset($validated['program_id'])) {
            $subjectsQuery->where('program_id', $validated['program_id']);
        }

        if (! empty($validated['q'])) {
            $subjectsQuery->where('subject_name', 'like', '%'.$validated['q'].'%');
        }

        $subjects = $subjectsQuery->paginate($validated['per_page'] ?? 100);

        return response()->json([
            'data' => $subjects->getCollection()->map(fn (Subject $subject) => [
                'id' => $subject->id,
                'subject_name' => $subject->subject_name,
                'program_id' => $subject->program_id,
                'program_name' => $subject->program?->program_name,
                'department' => $subject->program?->department,
                'prerequisite_id' => $subject->prerequisite_id,
                'prerequisite_name' => $subject->prerequisite?->subject_name,
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
