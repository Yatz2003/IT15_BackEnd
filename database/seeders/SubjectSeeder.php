<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
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

        Subject::query()->delete();

        $subjectPool = [
            'Introduction to University Learning',
            'Fundamentals of Communication',
            'Quantitative Methods',
            'Ethics and Society',
            'Research Methods',
            'Academic Writing',
            'Understanding the Self',
            'Science, Technology and Society',
            'Creative and Critical Thinking',
            'Physics 1',
            'Calculus 1',
            'General Chemistry',
            'Database Fundamentals',
            'Database Systems',
            'Systems Analysis',
            'Programming Fundamentals',
            'Data Structures',
            'Discrete Mathematics',
            'Applied Statistics',
            'Human Computer Interaction',
            'Operating Systems',
            'Networks and Security',
            'Web Application Development',
            'Cloud Fundamentals',
            'Information Assurance',
            'Artificial Intelligence Fundamentals',
            'Software Engineering Principles',
            'Advanced Project Management',
            'Capstone Project 1',
            'Capstone Project 2',
        ];

        foreach ($programIds as $programId) {
            $count = $faker->numberBetween(6, 8);
            $baseSubjects = collect($subjectPool)->shuffle()->take($count)->values();
            $createdIds = [];

            foreach ($baseSubjects as $index => $name) {
                $subject = Subject::query()->create([
                    'subject_name' => $name,
                    'program_id' => $programId,
                    'prerequisite_id' => $index > 0 && $faker->boolean(50)
                        ? $createdIds[array_rand($createdIds)]
                        : null,
                ]);

                $createdIds[] = $subject->id;
            }
        }
    }
}
