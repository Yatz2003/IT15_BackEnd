<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReportsIndexRequest;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(ReportsIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $months = $validated['months'] ?? 12;
        $periodStart = now()->startOfMonth()->subMonths($months - 1);

        return response()->json([
            'data' => [
                'overview' => [
                    'students_total' => Student::query()->count(),
                    'programs_total' => Program::query()->count(),
                    'subjects_total' => Subject::query()->count(),
                    'enrollments_total' => Enrollment::query()->count(),
                ],
                'attendance' => [
                    'average_rate' => round((float) SchoolDay::query()
                        ->where('is_holiday', false)
                        ->avg('attendance_rate'), 2),
                    'days_counted' => SchoolDay::query()->where('is_holiday', false)->count(),
                ],
                'students_per_program' => $this->studentsPerProgramData(),
                'year_level_distribution' => $this->yearLevelDistributionData(),
                'attendance_by_month' => $this->attendanceByMonthData($periodStart),
            ],
        ]);
    }

    private function studentsPerProgramData()
    {
        return Program::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get(['id', 'program_name'])
            ->map(fn (Program $program) => [
                'program_id' => $program->id,
                'program_name' => $program->program_name,
                'student_count' => (int) $program->students_count,
            ]);
    }

    private function yearLevelDistributionData()
    {
        return Student::query()
            ->select('year_level')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('year_level')
            ->orderBy('year_level')
            ->get()
            ->map(fn ($row) => [
                'year_level' => (int) $row->year_level,
                'total' => (int) $row->total,
            ]);
    }

    private function attendanceByMonthData(\Carbon\CarbonInterface $periodStart)
    {
        $driver = DB::connection()->getDriverName();
        $periodExpression = $driver === 'pgsql'
            ? "to_char(date, 'YYYY-MM')"
            : "DATE_FORMAT(date, '%Y-%m')";

        return SchoolDay::query()
            ->selectRaw("{$periodExpression} as month")
            ->selectRaw('AVG(attendance_rate) as average_rate')
            ->where('is_holiday', false)
            ->where('date', '>=', $periodStart->toDateString())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'average_rate' => round((float) $row->average_rate, 2),
            ]);
    }
}
