<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $programIds = Course::query()->pluck('id');

        if ($programIds->isEmpty()) {
            return;
        }

        $subjects = [
            ['code' => 'CS101', 'subject_name' => 'Introduction to Computing', 'department' => 'Computing', 'units' => 3],
            ['code' => 'CS201', 'subject_name' => 'Data Structures and Algorithms', 'department' => 'Computing', 'units' => 3],
            ['code' => 'CS301', 'subject_name' => 'Database Systems', 'department' => 'Computing', 'units' => 3],
            ['code' => 'ENG101', 'subject_name' => 'Engineering Mathematics', 'department' => 'Engineering', 'units' => 3],
            ['code' => 'ENG201', 'subject_name' => 'Engineering Drawing', 'department' => 'Engineering', 'units' => 2],
            ['code' => 'BUS101', 'subject_name' => 'Principles of Management', 'department' => 'Business', 'units' => 3],
            ['code' => 'BUS201', 'subject_name' => 'Business Communication', 'department' => 'Business', 'units' => 3],
            ['code' => 'SCI101', 'subject_name' => 'General Biology', 'department' => 'Sciences', 'units' => 3],
            ['code' => 'SCI201', 'subject_name' => 'General Chemistry', 'department' => 'Sciences', 'units' => 3],
            ['code' => 'HUM101', 'subject_name' => 'Purposive Communication', 'department' => 'Humanities', 'units' => 3],
            ['code' => 'SOC101', 'subject_name' => 'Introduction to Psychology', 'department' => 'Social Sciences', 'units' => 3],
            ['code' => 'SOC201', 'subject_name' => 'Contemporary World', 'department' => 'Social Sciences', 'units' => 3],
        ];

        foreach ($subjects as $subject) {
            Subject::query()->updateOrCreate(
                ['code' => $subject['code']],
                [
                    'subject_name' => $subject['subject_name'],
                    'program_id' => $programIds->random(),
                    'department' => $subject['department'],
                    'units' => $subject['units'],
                ]
            );
        }
    }
}
