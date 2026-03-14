<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = fake();

        Enrollment::query()->delete();

        $students = Student::query()->get(['id', 'program_id', 'created_at']);

        if ($students->isEmpty()) {
            return;
        }

        $subjectsByProgram = Subject::query()
            ->get(['id', 'program_id'])
            ->groupBy('program_id');

        $rows = [];

        foreach ($students as $student) {
            $programSubjects = $subjectsByProgram->get($student->program_id);

            if (! $programSubjects || $programSubjects->isEmpty()) {
                continue;
            }

            $maxSubjects = min(7, $programSubjects->count());
            $minSubjects = min(3, $maxSubjects);
            $subjectsForStudent = $programSubjects->random($faker->numberBetween($minSubjects, $maxSubjects));

            foreach ($subjectsForStudent as $subject) {
                $enrolledAt = $faker->dateTimeBetween($student->created_at ?? '-1 year', 'now');

                $rows[] = [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'enrolled_at' => $enrolledAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($rows === []) {
            return;
        }

        Enrollment::query()->insert($rows);
    }
}
