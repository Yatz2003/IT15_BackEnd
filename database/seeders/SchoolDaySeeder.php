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
        $faker = fake();
        $startDate = Carbon::create(2025, 8, 1);
        $endDate = Carbon::create(2026, 6, 30);

        $rows = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $isHoliday = $faker->boolean(8);
            $attendanceRate = $isHoliday ? 0 : $faker->randomFloat(2, 72, 99.8);

            $rows[] = [
                'date' => $date->toDateString(),
                'attendance_rate' => $attendanceRate,
                'is_holiday' => $isHoliday,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        SchoolDay::query()->upsert(
            $rows,
            ['date'],
            ['attendance_rate', 'is_holiday', 'updated_at']
        );
    }
}
