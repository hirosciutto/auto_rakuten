<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CosmeCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopController extends Controller
{
    /**
     * トップページ（フロントの「商品」= posts）
     */
    public function index(): View
    {
        $categories = CosmeCategory::categoryType()->orderBy('sort_order')->get();
        $moods = CosmeCategory::moodType()->orderBy('sort_order')->get();

        $postsQuery = Post::query()->published()->with(['item.shop', 'cosmeCategories']);

        if (request()->filled('cat')) {
            $postsQuery->whereHas('cosmeCategories', fn ($q) => $q->where('cosme_categories.slug', request('cat')));
        }
        if (request()->filled('keyword')) {
            $keyword = trim(request('keyword'));
            $postsQuery->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('body', 'like', '%' . $keyword . '%');
            });
        }

        $baseQuery = Post::query()->published()->with(['item.shop', 'cosmeCategories']);
        $trending = (clone $baseQuery)
            ->join('items', 'posts.item_id', '=', 'items.id')
            ->orderBy('items.review_count', 'desc')
            ->select('posts.*')
            ->limit(10)
            ->get();
        $ranking = (clone $baseQuery)
            ->join('items', 'posts.item_id', '=', 'items.id')
            ->where('items.review_count', '>=', 1)
            ->orderBy('items.review_average', 'desc')
            ->select('posts.*')
            ->limit(10)
            ->get();
        $posts = (clone $postsQuery)->orderBy('posts.id')->paginate(24);

        return view('front.home', [
            'categories' => $categories,
            'moods' => $moods,
            'trending' => $trending,
            'ranking' => $ranking,
            'posts' => $posts,
        ]);
    }

    /**
     * 商品一覧（posts）（無限スクロール用 JSON）
     */
    public function items(Request $request)
    {
        $query = Post::query()->published()->with(['item.shop', 'cosmeCategories']);

        $cosmeCategorySlug = $request->input('cat');
        if ($cosmeCategorySlug) {
            $query->whereHas('cosmeCategories', fn ($q) => $q->where('cosme_categories.slug', $cosmeCategorySlug));
        }

        $posts = $query->orderBy('posts.id')->paginate(24);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}
