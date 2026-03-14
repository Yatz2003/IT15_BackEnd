<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DashboardOverviewRequest;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview(DashboardOverviewRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $months = $validated['months'] ?? 12;
        $periodStart = now()->startOfMonth()->subMonths($months - 1);

        return response()->json([
            'students_count' => Student::query()->count(),
            'programs_count' => Program::query()->count(),
            'active_programs_count' => Program::query()->where('is_active', true)->count(),
            'subjects_count' => Subject::query()->count(),
            'monthly_enrollment' => $this->monthlyEnrollmentData($periodStart),
            'course_distribution' => $this->courseDistributionData(),
            'attendance_trends' => $this->attendanceTrendData($periodStart),
        ]);
    }

    public function enrollmentTrends(): JsonResponse
    {
        $driver = DB::connection()->getDriverName();

        $monthKeyExpression = $driver === 'pgsql'
            ? "to_char(created_at, 'YYYY-MM')"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthLabelExpression = $driver === 'pgsql'
            ? "to_char(created_at, 'Mon')"
            : "DATE_FORMAT(created_at, '%b')";

        $rows = Student::query()
            ->selectRaw("{$monthKeyExpression} as month_key")
            ->selectRaw("{$monthLabelExpression} as month")
            ->selectRaw('COUNT(*) as students')
            ->groupBy('month_key', 'month')
            ->orderBy('month_key')
            ->get();

        return response()->json($rows->map(fn ($row) => [
            'month' => $row->month,
            'students' => (int) $row->students,
        ]));
    }

    public function courseDistribution(): JsonResponse
    {
        $rows = Program::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get(['id', 'program_name']);

        return response()->json($rows->map(fn (Program $program) => [
            'course' => $program->program_name,
            'students' => (int) $program->students_count,
        ]));
    }

    public function attendancePatterns(): JsonResponse
    {
        $rows = SchoolDay::query()
            ->selectRaw('date')
            ->selectRaw('AVG(attendance_rate) as attendance_rate')
            ->where('is_holiday', false)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($rows->map(fn ($row) => [
            'day' => (string) $row->date,
            'attendance_rate' => round((float) $row->attendance_rate, 2),
        ]));
    }

    private function monthlyEnrollmentData(\Carbon\CarbonInterface $periodStart)
    {
        $driver = DB::connection()->getDriverName();
        $periodExpression = $driver === 'pgsql'
            ? "to_char(enrolled_at, 'YYYY-MM')"
            : "DATE_FORMAT(enrolled_at, '%Y-%m')";

        return Enrollment::query()
            ->selectRaw("{$periodExpression} as month")
            ->selectRaw('COUNT(*) as total')
            ->where('enrolled_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'total' => (int) $row->total,
            ]);
    }

    private function courseDistributionData()
    {
        return Program::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get()
            ->map(fn (Program $program) => [
                'program_id' => $program->id,
                'program_name' => $program->program_name,
                'students_count' => (int) $program->students_count,
            ]);
    }

    private function attendanceTrendData(\Carbon\CarbonInterface $periodStart)
    {
        return SchoolDay::query()
            ->where('date', '>=', $periodStart->toDateString())
            ->orderBy('date')
            ->get(['date', 'attendance_rate', 'is_holiday'])
            ->map(fn (SchoolDay $day) => [
                'date' => (string) $day->date,
                'attendance_rate' => (float) $day->attendance_rate,
                'is_holiday' => (bool) $day->is_holiday,
            ]);
    }
}
