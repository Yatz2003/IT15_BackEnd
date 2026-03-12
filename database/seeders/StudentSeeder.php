<?php

namespace Database\Seeders;

use App\Models\Course;
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
        $courseIds = Course::query()->pluck('id');

        if ($courseIds->isEmpty()) {
            return;
        }

        $rows = [];

        for ($i = 0; $i < 500; $i++) {
            $rows[] = [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'age' => $faker->numberBetween(16, 28),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'course_id' => $courseIds->random(),
                'created_at' => Carbon::now()->subDays($faker->numberBetween(0, 540)),
                'updated_at' => now(),
            ];
        }

        Student::query()->insert($rows);
    }
}
