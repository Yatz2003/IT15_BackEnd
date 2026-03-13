<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $students = Student::query()
            ->with(['program', 'course'])
            ->latest('created_at')
            ->paginate($validated['per_page'] ?? 50);

        return StudentResource::collection($students)->response();
    }

    public function enrollmentTrends(): JsonResponse
    {
        $driver = DB::connection()->getDriverName();
        $periodExpression = $driver === 'pgsql'
            ? "to_char(created_at, 'YYYY-MM')"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        // Aggregate student signups per month for dashboard line charts.
        $rows = Student::query()
            ->selectRaw("{$periodExpression} as period")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json([
            'data' => $rows->map(fn ($row) => [
                'period' => $row->period,
                'total' => (int) $row->total,
            ]),
        ]);
    }
}
