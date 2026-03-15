<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubjectsIndexRequest;
use App\Http\Requests\Api\StoreSubjectRequest;
use App\Http\Requests\Api\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(SubjectsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $uniqueSubjectIds = Subject::query()
            ->selectRaw('MIN(id)')
            ->whereNotNull('subject_code')
            ->where('subject_code', '!=', '')
            ->groupBy('subject_code');

        $subjectsQuery = Subject::query()
            ->whereIn('id', $uniqueSubjectIds)
            ->with([
                'program:id,program_name',
            ])
            ->orderBy('subject_name');

        if (isset($validated['program_id'])) {
            $subjectsQuery->where('program_id', $validated['program_id']);
        }

        if (! empty($validated['q'])) {
            $subjectsQuery->where(function ($query) use ($validated): void {
                $query->where('subject_name', 'like', '%'.$validated['q'].'%')
                    ->orWhere('subject_code', 'like', '%'.$validated['q'].'%');
            });
        }

        if (! empty($validated['per_page'])) {
            $subjects = $subjectsQuery->paginate((int) $validated['per_page']);

            return response()->json([
                'data' => $subjects->getCollection()->map(fn (Subject $subject): array => $this->transformSubject($subject)),
                'meta' => [
                    'current_page' => $subjects->currentPage(),
                    'last_page' => $subjects->lastPage(),
                    'per_page' => $subjects->perPage(),
                    'total' => $subjects->total(),
                ],
            ]);
        }

        return response()->json(
            $subjectsQuery->get()->map(fn (Subject $subject): array => $this->transformSubject($subject))
        );
    }

    public function show(int $id): JsonResponse
    {
        $subject = Subject::query()
            ->with('program:id,program_name')
            ->findOrFail($id);

        return response()->json($this->transformSubject($subject));
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $subject = Subject::query()->create([
            'subject_code' => $validated['subject_code'],
            'subject_name' => $validated['subject_name'],
            'program_id' => $validated['program_id'],
            'units' => (int) $validated['units'],
            'semester' => $validated['semester'],
            'prerequisite_id' => null,
        ]);

        $subject->load('program:id,program_name');

        return response()->json([
            'message' => 'Subject created successfully.',
            'data' => $this->transformSubject($subject),
        ], 201);
    }

    public function update(UpdateSubjectRequest $request, int $id): JsonResponse
    {
        $subject = Subject::query()->findOrFail($id);
        $validated = $request->validated();

        $subject->fill([
            'subject_code' => $validated['subject_code'] ?? $subject->subject_code,
            'subject_name' => $validated['subject_name'] ?? $subject->subject_name,
            'program_id' => $validated['program_id'] ?? $subject->program_id,
            'units' => array_key_exists('units', $validated) ? (int) $validated['units'] : $subject->units,
            'semester' => $validated['semester'] ?? $subject->semester,
        ]);

        $subject->save();
        $subject->load('program:id,program_name');

        return response()->json([
            'message' => 'Subject updated successfully.',
            'data' => $this->transformSubject($subject),
        ]);
    }

    private function transformSubject(Subject $subject): array
    {
        return [
            'id' => (int) $subject->id,
            'subject_code' => (string) $subject->subject_code,
            'subject_name' => (string) $subject->subject_name,
            'program' => $subject->program?->program_name,
            'program_name' => $subject->program?->program_name,
            'units' => (int) $subject->units,
            'semester' => (string) $subject->semester,
        ];
    }
}
