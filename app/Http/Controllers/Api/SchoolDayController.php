<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SchoolDaysIndexRequest;
use App\Models\SchoolDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class SchoolDayController extends Controller
{
    public function index(SchoolDaysIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = SchoolDay::query()->orderBy('date');

        if (! empty($validated['start_date'])) {
            $query->whereDate('date', '>=', $validated['start_date']);
        }

        if (! empty($validated['end_date'])) {
            $query->whereDate('date', '<=', $validated['end_date']);
        }

        if (array_key_exists('is_holiday', $validated)) {
            $query->where('is_holiday', (bool) $validated['is_holiday']);
        }

        if (! empty($validated['per_page'])) {
            $days = $query->paginate((int) $validated['per_page']);

            return response()->json([
                'data' => $days->getCollection()->map(fn (SchoolDay $day): array => $this->transformSchoolDay($day)),
                'meta' => [
                    'current_page' => $days->currentPage(),
                    'last_page' => $days->lastPage(),
                    'per_page' => $days->perPage(),
                    'total' => $days->total(),
                ],
            ]);
        }

        return response()->json(
            $query->get(['date', 'day_name', 'is_holiday', 'event_name', 'attendance_rate'])
                ->map(fn (SchoolDay $day): array => $this->transformSchoolDay($day))
        );
    }

    private function transformSchoolDay(SchoolDay $day): array
    {
        return [
            'date' => Carbon::parse((string) $day->date)->toDateString(),
            'day_name' => (string) $day->day_name,
            'is_holiday' => (bool) $day->is_holiday,
            'event_name' => $day->event_name,
            'attendance_rate' => (int) $day->attendance_rate,
        ];
    }
}
