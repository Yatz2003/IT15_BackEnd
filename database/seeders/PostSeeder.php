<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technology = Category::query()->where('name', 'Technology')->first();
        $education = Category::query()->where('name', 'Education')->first();
        $health = Category::query()->where('name', 'Health')->first();
        $business = Category::query()->where('name', 'Business')->first();

        $posts = [
            [
                'category_id' => $technology?->id,
                'title' => 'Simple Guide to Web Development',
                'description' => 'Web development combines HTML, CSS, and JavaScript to build modern websites and applications.',
            ],
            [
                'category_id' => $technology?->id,
                'title' => 'Why Databases Matter',
                'description' => 'Databases help store and retrieve data efficiently, which is essential for dynamic websites.',
            ],
            [
                'category_id' => $technology?->id,
                'title' => 'Understanding APIs',
                'description' => 'APIs allow applications to communicate and share data in a structured and reliable way.',
            ],
            [
                'category_id' => $technology?->id,
                'title' => 'Intro to Version Control',
                'description' => 'Version control tools like Git help teams collaborate, track changes, and avoid losing work.',
            ],
            [
                'category_id' => $technology?->id,
                'title' => 'Frontend vs Backend',
                'description' => 'Frontend focuses on user interfaces, while backend handles data, logic, and server-side processing.',
            ],
            [
                'category_id' => $technology?->id,
                'title' => 'Basic Web Security Tips',
                'description' => 'Use strong passwords, validate user input, and keep software updated to reduce vulnerabilities.',
            ],
            [
                'category_id' => $education?->id,
                'title' => 'Learning with Small Daily Goals',
                'description' => 'Consistent daily practice can help students improve faster than long but irregular study sessions.',
            ],
            [
                'category_id' => $education?->id,
                'title' => 'How to Take Better Notes',
                'description' => 'Summarize key ideas in your own words and review them regularly for stronger retention.',
            ],
            [
                'category_id' => $education?->id,
                'title' => 'Active Learning Methods',
                'description' => 'Teaching others, solving practice problems, and self-testing improve understanding.',
            ],
            [
                'category_id' => $education?->id,
                'title' => 'Building a Study Routine',
                'description' => 'A weekly schedule helps balance school tasks, deadlines, and rest periods effectively.',
            ],
            [
                'category_id' => $education?->id,
                'title' => 'Using Online Learning Resources',
                'description' => 'Free tutorials, documentation, and educational videos can support classroom learning.',
            ],
            [
                'category_id' => $health?->id,
                'title' => 'Basic Healthy Habits',
                'description' => 'Drinking enough water, sleeping on time, and exercising regularly support better health.',
            ],
            [
                'category_id' => $health?->id,
                'title' => 'Benefits of Daily Walking',
                'description' => 'Walking for 20 to 30 minutes each day can improve heart health and reduce stress.',
            ],
            [
                'category_id' => $health?->id,
                'title' => 'Simple Stress Management',
                'description' => 'Breathing exercises, proper sleep, and short breaks help manage daily stress levels.',
            ],
            [
                'category_id' => $health?->id,
                'title' => 'Eating Balanced Meals',
                'description' => 'Include vegetables, protein, and whole grains to maintain energy and nutrition.',
            ],
            [
                'category_id' => $health?->id,
                'title' => 'Why Sleep Is Important',
                'description' => 'Quality sleep supports memory, mood, and physical recovery after daily activities.',
            ],
            [
                'category_id' => $business?->id,
                'title' => 'Starting a Small Business',
                'description' => 'Begin with a clear problem to solve, understand your market, and track your finances carefully.',
            ],
            [
                'category_id' => $business?->id,
                'title' => 'Basic Marketing for Beginners',
                'description' => 'Start by identifying your audience and choosing clear messaging for your product or service.',
            ],
            [
                'category_id' => $business?->id,
                'title' => 'Tracking Business Expenses',
                'description' => 'Recording expenses consistently helps maintain healthy cash flow and better planning.',
            ],
            [
                'category_id' => $business?->id,
                'title' => 'Customer Service Essentials',
                'description' => 'Fast responses and clear communication build trust and improve customer retention.',
            ],
            [
                'category_id' => $business?->id,
                'title' => 'Setting Realistic Business Goals',
                'description' => 'Use measurable and time-based goals to monitor growth and keep your team aligned.',
            ],
        ];

        foreach ($posts as $post) {
            if ($post['category_id']) {
                Post::query()->create($post);
            }
        }
    }
}
