<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('passwordjames'),
            ]
        );

        $this->call([
            ProgramSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            EnrollmentSeeder::class,
            SchoolDaySeeder::class,
        ]);
    }
}
