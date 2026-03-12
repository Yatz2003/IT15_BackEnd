<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $courses = [
            ['course_name' => 'BS Computer Science', 'department' => 'Computing'],
            ['course_name' => 'BS Information Technology', 'department' => 'Computing'],
            ['course_name' => 'BS Information Systems', 'department' => 'Computing'],
            ['course_name' => 'BS Data Science', 'department' => 'Computing'],
            ['course_name' => 'BS Software Engineering', 'department' => 'Computing'],
            ['course_name' => 'BS Civil Engineering', 'department' => 'Engineering'],
            ['course_name' => 'BS Mechanical Engineering', 'department' => 'Engineering'],
            ['course_name' => 'BS Electrical Engineering', 'department' => 'Engineering'],
            ['course_name' => 'BS Electronics Engineering', 'department' => 'Engineering'],
            ['course_name' => 'BS Industrial Engineering', 'department' => 'Engineering'],
            ['course_name' => 'BS Accountancy', 'department' => 'Business'],
            ['course_name' => 'BS Business Administration', 'department' => 'Business'],
            ['course_name' => 'BS Marketing Management', 'department' => 'Business'],
            ['course_name' => 'BS Financial Management', 'department' => 'Business'],
            ['course_name' => 'BS Entrepreneurship', 'department' => 'Business'],
            ['course_name' => 'BS Biology', 'department' => 'Sciences'],
            ['course_name' => 'BS Chemistry', 'department' => 'Sciences'],
            ['course_name' => 'BS Physics', 'department' => 'Sciences'],
            ['course_name' => 'BS Mathematics', 'department' => 'Sciences'],
            ['course_name' => 'BS Psychology', 'department' => 'Social Sciences'],
            ['course_name' => 'BA Communication', 'department' => 'Humanities'],
            ['course_name' => 'BA Political Science', 'department' => 'Social Sciences'],
        ];

        foreach ($courses as $course) {
            Course::query()->updateOrCreate(
                ['course_name' => $course['course_name']],
                ['department' => $course['department']]
            );
        }
    }
}
