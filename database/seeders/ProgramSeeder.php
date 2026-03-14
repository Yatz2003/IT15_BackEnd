<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $programs = [
            ['program_name' => 'BS Information Technology', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Computer Science', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Information Systems', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Data Science', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Software Engineering', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Cybersecurity', 'department' => 'Computing', 'is_active' => true],
            ['program_name' => 'BS Computer Engineering', 'department' => 'Engineering', 'is_active' => true],
            ['program_name' => 'BS Civil Engineering', 'department' => 'Engineering', 'is_active' => true],
            ['program_name' => 'BS Mechanical Engineering', 'department' => 'Engineering', 'is_active' => true],
            ['program_name' => 'BS Electrical Engineering', 'department' => 'Engineering', 'is_active' => true],
            ['program_name' => 'BS Industrial Engineering', 'department' => 'Engineering', 'is_active' => true],
            ['program_name' => 'BS Accountancy', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Business Administration', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Marketing Management', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Financial Management', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Hospitality Management', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Tourism Management', 'department' => 'Business', 'is_active' => true],
            ['program_name' => 'BS Biology', 'department' => 'Sciences', 'is_active' => true],
            ['program_name' => 'BS Chemistry', 'department' => 'Sciences', 'is_active' => true],
            ['program_name' => 'BS Physics', 'department' => 'Sciences', 'is_active' => true],
        ];

        Program::query()
            ->whereNotIn('program_name', collect($programs)->pluck('program_name'))
            ->delete();

        foreach ($programs as $program) {
            Program::query()->updateOrCreate(
                ['program_name' => $program['program_name']],
                [
                    'department' => $program['department'],
                    'is_active' => $program['is_active'],
                ]
            );
        }
    }
}
