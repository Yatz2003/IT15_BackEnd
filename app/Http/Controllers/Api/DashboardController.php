<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DashboardOverviewRequest;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Carbon;
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
        return response()->json($this->enrollmentAnalyticsData(12));
    }

    public function enrollmentAnalytics(): JsonResponse
    {
        return response()->json($this->enrollmentAnalyticsByYearPercentageData(2015, (int) now()->format('Y')));
    }

    public function enrollmentAnalyticsYearly(): JsonResponse
    {
        return response()->json($this->enrollmentAnalyticsYearlyData(5));
    }

    public function programDistribution(): JsonResponse
    {
        $rows = Program::query()
            ->withCount('students')
            ->having('students_count', '>', 0)
            ->orderByDesc('students_count')
            ->get(['id', 'program_name']);

        return response()->json($rows->map(fn (Program $program) => [
            'program' => $program->program_name,
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

        if ($rows->isEmpty()) {
            return response()->json(
                collect(range(0, 6))->map(fn (int $offset) => [
                    'date' => now()->subDays(6 - $offset)->toDateString(),
                    'day' => now()->subDays(6 - $offset)->toDateString(),
                    'attendance_rate' => 0.0,
                ])->all()
            );
        }

        return response()->json($rows->map(fn ($row) => [
            'date' => Carbon::parse((string) $row->date)->toDateString(),
            'day' => Carbon::parse((string) $row->date)->toDateString(),
            'attendance_rate' => round((float) $row->attendance_rate, 2),
        ]));
    }

    public function reliabilitySnapshot(): JsonResponse
    {
        $averageAttendance = round((float) SchoolDay::query()
            ->where('is_holiday', false)
            ->avg('attendance_rate'), 2);

        return response()->json([
            'total_students' => Student::query()->count(),
            'total_programs' => Program::query()->count(),
            'total_subjects' => Subject::query()->count(),
            'active_programs' => Program::query()->where('is_active', true)->count(),
            'average_attendance' => $averageAttendance,
            'attendance_average' => $averageAttendance,
        ]);
    }

    public function roomAssignments(): JsonResponse
    {
        $rows = [
            [
                'room' => 'A-301',
                'room_name' => 'A-301',
                'roomName' => 'A-301',
                'program' => 'BS Information Technology',
                'program_name' => 'BS Information Technology',
                'programName' => 'BS Information Technology',
                'course' => 'Programming Fundamentals',
                'course_name' => 'Programming Fundamentals',
                'courseName' => 'Programming Fundamentals',
                'instructor' => 'Prof. Maria Santos',
                'instructor_name' => 'Prof. Maria Santos',
                'instructorName' => 'Prof. Maria Santos',
                'schedule' => 'Mon/Wed 08:00-09:30',
                'time_slot' => 'Mon/Wed 08:00-09:30',
                'timeSlot' => 'Mon/Wed 08:00-09:30',
                'capacity' => 45,
                'enrolled' => 41,
            ],
            [
                'room' => 'Room 204',
                'room_name' => 'Room 204',
                'roomName' => 'Room 204',
                'program' => 'BS Computer Science',
                'program_name' => 'BS Computer Science',
                'programName' => 'BS Computer Science',
                'course' => 'Physics 1',
                'course_name' => 'Physics 1',
                'courseName' => 'Physics 1',
                'instructor' => 'Prof. Carlo Reyes',
                'instructor_name' => 'Prof. Carlo Reyes',
                'instructorName' => 'Prof. Carlo Reyes',
                'schedule' => 'Mon/Wed 10:00-11:30',
                'time_slot' => 'Mon/Wed 10:00-11:30',
                'timeSlot' => 'Mon/Wed 10:00-11:30',
                'capacity' => 40,
                'enrolled' => 40,
            ],
            [
                'room' => 'Room 305',
                'room_name' => 'Room 305',
                'roomName' => 'Room 305',
                'program' => 'BS Business Administration',
                'program_name' => 'BS Business Administration',
                'programName' => 'BS Business Administration',
                'course' => 'Calculus 1',
                'course_name' => 'Calculus 1',
                'courseName' => 'Calculus 1',
                'instructor' => 'Prof. Angelica Cruz',
                'instructor_name' => 'Prof. Angelica Cruz',
                'instructorName' => 'Prof. Angelica Cruz',
                'schedule' => 'Tue/Thu 09:00-10:30',
                'time_slot' => 'Tue/Thu 09:00-10:30',
                'timeSlot' => 'Tue/Thu 09:00-10:30',
                'capacity' => 50,
                'enrolled' => 44,
            ],
            [
                'room' => 'Lab 2',
                'room_name' => 'Lab 2',
                'roomName' => 'Lab 2',
                'program' => 'BS Information Technology',
                'program_name' => 'BS Information Technology',
                'programName' => 'BS Information Technology',
                'course' => 'Database Systems',
                'course_name' => 'Database Systems',
                'courseName' => 'Database Systems',
                'instructor' => 'Prof. Daniel Ramos',
                'instructor_name' => 'Prof. Daniel Ramos',
                'instructorName' => 'Prof. Daniel Ramos',
                'schedule' => 'Fri 13:00-16:00',
                'time_slot' => 'Fri 13:00-16:00',
                'timeSlot' => 'Fri 13:00-16:00',
                'capacity' => 36,
                'enrolled' => 33,
            ],
            [
                'room' => 'D-110',
                'room_name' => 'D-110',
                'roomName' => 'D-110',
                'program' => 'BS Information Systems',
                'program_name' => 'BS Information Systems',
                'programName' => 'BS Information Systems',
                'course' => 'General Chemistry',
                'course_name' => 'General Chemistry',
                'courseName' => 'General Chemistry',
                'instructor' => 'Prof. Leanne Villanueva',
                'instructor_name' => 'Prof. Leanne Villanueva',
                'instructorName' => 'Prof. Leanne Villanueva',
                'schedule' => 'Tue/Thu 14:00-15:30',
                'time_slot' => 'Tue/Thu 14:00-15:30',
                'timeSlot' => 'Tue/Thu 14:00-15:30',
                'capacity' => 42,
                'enrolled' => 29,
            ],
        ];

        return response()->json(collect($rows)->map(function (array $row): array {
            $availableSeats = max(0, (int) $row['capacity'] - (int) $row['enrolled']);
            $occupancyRate = (int) $row['capacity'] > 0
                ? round(((int) $row['enrolled'] / (int) $row['capacity']) * 100, 1)
                : 0;

            $status = $availableSeats > 0 ? 'Available' : 'Full';

            $row['available_seats'] = $availableSeats;
            $row['availableSeats'] = $availableSeats;
            $row['occupancy_rate_percent'] = $occupancyRate;
            $row['occupancyRatePercent'] = $occupancyRate;
            $row['subject_name'] = $row['course_name'];
            $row['room_number'] = $row['room_name'];
            $row['availability'] = $status;
            $row['subject'] = $row['course_name'];
            $row['room'] = $row['room_name'];
            $row['status'] = $status;
            $row['student_capacity'] = (int) $row['enrolled'].'/'.(int) $row['capacity'];
            $row['capacity_text'] = $row['student_capacity'];

            return $row;
        })->values());
    }

    public function rooms(): JsonResponse
    {
        $rows = $this->roomAssignments()->getData(true);

        return response()->json(collect($rows)->map(fn (array $row): array => [
            'subject' => (string) ($row['subject_name'] ?? $row['course_name'] ?? ''),
            'room' => (string) ($row['room_number'] ?? $row['room_name'] ?? ''),
            'capacity' => (string) ($row['student_capacity'] ?? ((int) ($row['enrolled'] ?? 0).'/'.(int) ($row['capacity'] ?? 0))),
            'student_capacity' => (string) ($row['student_capacity'] ?? ((int) ($row['enrolled'] ?? 0).'/'.(int) ($row['capacity'] ?? 0))),
            'schedule' => (string) ($row['schedule'] ?? ''),
        ])->values());
    }

    public function roomAvailability(): JsonResponse
    {
        $rows = [
            [
                'room' => 'A-101',
                'room_name' => 'A-101',
                'roomName' => 'A-101',
                'building' => 'Academic Building A',
                'building_name' => 'Academic Building A',
                'buildingName' => 'Academic Building A',
                'capacity' => 40,
                'status' => 'Available',
                'next_slot' => '09:30-11:00',
                'nextSlot' => '09:30-11:00',
            ],
            [
                'room' => 'A-202',
                'room_name' => 'A-202',
                'roomName' => 'A-202',
                'building' => 'Academic Building A',
                'building_name' => 'Academic Building A',
                'buildingName' => 'Academic Building A',
                'capacity' => 45,
                'status' => 'Full',
                'next_slot' => '13:00-14:30',
                'nextSlot' => '13:00-14:30',
            ],
            [
                'room' => 'B-104',
                'room_name' => 'B-104',
                'roomName' => 'B-104',
                'building' => 'Business Building',
                'building_name' => 'Business Building',
                'buildingName' => 'Business Building',
                'capacity' => 55,
                'status' => 'Available',
                'next_slot' => '11:00-12:30',
                'nextSlot' => '11:00-12:30',
            ],
            [
                'room' => 'C-LAB-1',
                'room_name' => 'C-LAB-1',
                'roomName' => 'C-LAB-1',
                'building' => 'Computing Center',
                'building_name' => 'Computing Center',
                'buildingName' => 'Computing Center',
                'capacity' => 35,
                'status' => 'Maintenance',
                'next_slot' => 'Unavailable today',
                'nextSlot' => 'Unavailable today',
            ],
            [
                'room' => 'C-LAB-3',
                'room_name' => 'C-LAB-3',
                'roomName' => 'C-LAB-3',
                'building' => 'Computing Center',
                'building_name' => 'Computing Center',
                'buildingName' => 'Computing Center',
                'capacity' => 38,
                'status' => 'Available',
                'next_slot' => '14:30-16:00',
                'nextSlot' => '14:30-16:00',
            ],
        ];

        return response()->json(collect($rows)->map(function (array $row): array {
            $status = (string) $row['status'];
            $isAvailable = strcasecmp($status, 'Available') === 0;

            $row['availability'] = $status;
            $row['availability_status'] = $status;
            $row['availabilityStatus'] = $status;
            $row['is_available'] = $isAvailable;
            $row['isAvailable'] = $isAvailable;

            return $row;
        })->values());
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

    private function enrollmentAnalyticsData(int $months = 12): array
    {
        $months = max(1, $months);
        $periodStart = now()->startOfMonth()->subMonths($months - 1);
        $driver = DB::connection()->getDriverName();

        $monthKeyExpression = $driver === 'pgsql'
            ? "to_char(created_at, 'YYYY-MM')"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $totalsByMonth = Student::query()
            ->selectRaw("{$monthKeyExpression} as month_key")
            ->selectRaw('COUNT(*) as students')
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month_key')
            ->pluck('students', 'month_key');

        $rows = [];
        $cumulative = 0;

        for ($index = 0; $index < $months; $index++) {
            $monthDate = Carbon::instance($periodStart)->addMonths($index);
            $monthKey = $monthDate->format('Y-m');
            $newStudents = (int) ($totalsByMonth[$monthKey] ?? 0);
            $cumulative += $newStudents;

            $previousStudents = $index === 0 ? null : ($rows[$index - 1]['students'] ?? null);
            $growthRate = $previousStudents && $previousStudents > 0
                ? round((($newStudents - $previousStudents) / $previousStudents) * 100, 1)
                : null;

            $rows[] = [
                'month' => $monthDate->format('F'),
                'students' => $newStudents,
                'new_students' => $newStudents,
                'cumulative_students' => $cumulative,
                'growth_rate_percent' => $growthRate,
            ];
        }

        return $rows;
    }

    private function enrollmentAnalyticsYearlyData(int $years = 5): array
    {
        $years = max(1, $years);
        $endYear = (int) now()->format('Y');
        $startYear = $endYear - ($years - 1);

        $rows = $this->enrollmentAnalyticsByYearData($startYear, $endYear);
        $runningTotal = 0;

        $rows = array_map(function (array $row) use (&$runningTotal): array {
            $runningTotal += (int) $row['students'];

            return [
                'year' => $row['year'],
                'students' => $row['students'],
                'new_students' => $row['students'],
                'total_students' => $runningTotal,
                'is_sample_data' => false,
            ];
        }, $rows);

        return [
            'title' => 'Enrollment Analytics',
            'description' => 'Yearly student enrollment totals for dashboard reporting.',
            'data' => $rows,
        ];
    }

    private function enrollmentAnalyticsByYearData(int $startYear, int $endYear): array
    {
        $startYear = min($startYear, $endYear);
        $driver = DB::connection()->getDriverName();

        $yearExpression = $driver === 'pgsql'
            ? "to_char(created_at, 'YYYY')"
            : 'YEAR(created_at)';

        $totalsByYear = Student::query()
            ->selectRaw("{$yearExpression} as year_key")
            ->selectRaw('COUNT(*) as students')
            ->whereYear('created_at', '>=', $startYear)
            ->whereYear('created_at', '<=', $endYear)
            ->groupBy('year_key')
            ->pluck('students', 'year_key');

        $rows = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearKey = (string) $year;

            $rows[] = [
                'year' => $year,
                'students' => (int) ($totalsByYear[$yearKey] ?? 0),
            ];
        }

        return $rows;
    }

    private function enrollmentAnalyticsByYearPercentageData(int $startYear, int $endYear): array
    {
        $rows = $this->enrollmentAnalyticsByYearData($startYear, $endYear);
        $previous = null;

        return array_map(function (array $row) use (&$previous): array {
            $current = (int) $row['students'];
            $percentage = 0.0;

            if ($previous !== null && $previous > 0) {
                $percentage = round((($current - $previous) / $previous) * 100, 2);
            }

            $previous = $current;

            return [
                'year' => (int) $row['year'],
                'percentage' => $percentage,
            ];
        }, $rows);
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
