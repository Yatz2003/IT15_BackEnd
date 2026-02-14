<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()->orderBy('name')->get();

        $selectedCategory = null;
        $selectedCategoryId = (int) $request->query('category');

        if ($selectedCategoryId > 0) {
            $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
        }

        if (! $selectedCategory && $categories->isNotEmpty()) {
            $selectedCategory = $categories->first();
        }

        $posts = collect();

        if ($selectedCategory) {
            $posts = Post::query()
                ->where('category_id', $selectedCategory->id)
                ->latest()
                ->get();
        }

        return view('posts.index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'posts' => $posts,
        ]);
    }
}
