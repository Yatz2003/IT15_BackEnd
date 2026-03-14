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

        $days = $query->paginate($validated['per_page'] ?? 100);

        return response()->json([
            'data' => $days->getCollection()->map(function (SchoolDay $day): array {
                $date = $day->date instanceof Carbon ? $day->date : Carbon::parse((string) $day->date);

                return [
                    'date' => $date->toDateString(),
                    'is_holiday' => (bool) $day->is_holiday,
                    'attendance_rate' => (float) $day->attendance_rate,
                    'event' => $this->eventLabel($date, (bool) $day->is_holiday),
                ];
            }),
            'meta' => [
                'current_page' => $days->currentPage(),
                'last_page' => $days->lastPage(),
                'per_page' => $days->perPage(),
                'total' => $days->total(),
            ],
        ]);
    }

    private function eventLabel(Carbon $date, bool $isHoliday): string
    {
        if ($isHoliday) {
            return 'Holiday';
        }

        if ((int) $date->format('m') === 10 && (int) $date->format('d') <= 15) {
            return 'Midterm Week';
        }

        if ((int) $date->format('m') === 3 && (int) $date->format('d') >= 20) {
            return 'Finals Period';
        }

        return 'Class Day';
    }
}
