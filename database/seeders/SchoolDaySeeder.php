<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SchoolDaySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2025, 8, 1);
        $endDate = Carbon::create(2026, 5, 31);

        $eventMap = [
            '2025-08-04' => 'Orientation Day',
            '2025-10-13' => 'Midterm Exams',
            '2025-11-14' => 'Research Symposium',
            '2026-01-23' => 'Sports Festival',
            '2026-03-23' => 'Final Exams',
            '2026-05-25' => 'Graduation Day',
        ];

        $holidayDates = [
            '2025-08-21',
            '2025-11-01',
            '2025-11-30',
            '2025-12-08',
            '2025-12-25',
            '2026-01-01',
            '2026-02-25',
            '2026-04-02',
            '2026-04-03',
            '2026-05-01',
        ];

        $holidayLookup = collect($holidayDates)
            ->mapWithKeys(fn (string $date): array => [$date => true])
            ->all();

        $rows = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateString = $date->toDateString();
            $isHoliday = isset($holidayLookup[$dateString]);
            $eventName = $eventMap[$dateString] ?? ($isHoliday ? 'University Holiday' : 'Regular Classes');
            $attendanceRate = $isHoliday ? 0 : random_int(80, 98);

            $rows[] = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'is_holiday' => $isHoliday,
                'event_name' => $eventName,
                'attendance_rate' => $attendanceRate,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($rows) < 200) {
            throw new \RuntimeException('SchoolDaySeeder must generate at least 200 records.');
        }

        SchoolDay::query()->upsert(
            $rows,
            ['date'],
            ['day_name', 'is_holiday', 'event_name', 'attendance_rate', 'updated_at']
        );
    }
}
