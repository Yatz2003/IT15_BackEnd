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
        $programIds = Program::query()->pluck('id');

        if ($programIds->isEmpty()) {
            return;
        }

        Student::query()->delete();

        $rows = [];

        for ($i = 0; $i < 500; $i++) {
            $programId = $programIds->random();

            $rows[] = [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'program_id' => $programId,
                'year_level' => $faker->numberBetween(1, 4),
                'created_at' => Carbon::now()->subDays($faker->numberBetween(0, 540)),
                'updated_at' => now(),
            ];
        }

        Student::query()->insert($rows);
    }
}
