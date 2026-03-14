<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StudentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = fake();
        $programIds = Program::query()->where('is_active', true)->pluck('id');

        if ($programIds->isEmpty()) {
            $programIds = Program::query()->pluck('id');
        }

        if ($programIds->isEmpty()) {
            return;
        }

        Student::query()->delete();

        $monthWeights = [
            1 => 0.85,
            2 => 0.95,
            3 => 1.05,
            4 => 1.15,
            5 => 1.25,
            6 => 1.35,
            7 => 1.55,
            8 => 2.25,
            9 => 2.45,
            10 => 1.9,
            11 => 1.5,
            12 => 1.0,
        ];

        $periodStart = now()->startOfMonth()->subMonths(11);
        $baseMonthlyCount = 32;
        $studentNumber = 1;
        $rows = [];

        for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
            $monthDate = Carbon::instance($periodStart)->addMonths($monthIndex);
            $monthStart = $monthDate->copy()->startOfMonth();
            $monthEnd = $monthDate->copy()->endOfMonth();

            if ($monthEnd->isFuture()) {
                $monthEnd = now();
            }

            if ($monthEnd->lt($monthStart)) {
                continue;
            }

            $weight = $monthWeights[$monthDate->month] ?? 1.0;
            $target = (int) round(($baseMonthlyCount * $weight) + $faker->numberBetween(6, 14));

            for ($i = 0; $i < $target; $i++) {
                $programId = $programIds->random();
                $yearLevelRoll = $faker->numberBetween(1, 100);
                $yearLevel = match (true) {
                    $yearLevelRoll <= 34 => 1,
                    $yearLevelRoll <= 62 => 2,
                    $yearLevelRoll <= 83 => 3,
                    default => 4,
                };

                $rows[] = [
                    'name' => $faker->name(),
                    'email' => 'student'.str_pad((string) $studentNumber, 5, '0', STR_PAD_LEFT).'@university.local',
                    'program_id' => $programId,
                    'year_level' => $yearLevel,
                    'created_at' => $faker->dateTimeBetween($monthStart, $monthEnd),
                    'updated_at' => now(),
                ];

                $studentNumber++;
            }
        }

        Student::query()->insert($rows);
    }
}
