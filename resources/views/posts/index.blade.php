<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts by Category</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 28px;
            color: #111;
            line-height: 1.5;
        }

        .container {
            max-width: 1024px;
            margin: 0 auto;
        }

        .title {
            margin-bottom: 18px;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 14px;
            align-items: start;
        }

        .panel {
            background: #fff;
            border: 1px solid #e7e7e7;
            border-radius: 10px;
            padding: 16px;
        }

        .category-list {
            list-style: none;
        }

        .category-item + .category-item {
            margin-top: 8px;
        }

        .category-link {
            display: block;
            text-decoration: none;
            color: #111;
            border: 1px solid #ededed;
            border-radius: 6px;
            padding: 9px 11px;
            background: #fff;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }

        .category-link.active {
            background: #f2f2f2;
            border-color: #d9d9d9;
            font-weight: 600;
        }

        .category-link:hover {
            border-color: #dcdcdc;
            background: #fafafa;
        }

        .cards {
            display: grid;
            gap: 10px;
        }

        .card {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 8px;
            padding: 14px 15px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #111;
        }

        .card p {
            color: #4b4b4b;
            font-size: 14px;
        }

        .empty {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 800px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="title">Posts</h1>

    <div class="layout">
        <aside class="panel">
            <h2 style="font-size: 14px; margin-bottom: 10px; font-weight: 600;">Categories</h2>

            <ul class="category-list">
                @forelse ($categories as $category)
                    <li class="category-item">
                        <a
                            href="{{ route('posts.index', ['category' => $category->id]) }}"
                            class="category-link {{ $selectedCategory && $selectedCategory->id === $category->id ? 'active' : '' }}"
                        >
                            {{ $category->name }}
                        </a>
                    </li>
                @empty
                    <li class="empty">No categories found.</li>
                @endforelse
            </ul>
        </aside>

        <section class="panel">
            <h2 style="font-size: 14px; margin-bottom: 12px; font-weight: 600;">
                {{ $selectedCategory ? $selectedCategory->name . ' Posts' : 'Posts' }}
            </h2>

            <div class="cards">
                @forelse ($posts as $post)
                    <article class="card">
                        <h3 class="card-title">{{ $post->title }}</h3>
                        <p>{{ $post->description }}</p>
                    </article>
                @empty
                    <p class="empty">No posts found for this category.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
</body>
</html>
