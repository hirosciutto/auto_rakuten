<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CosmeCategory;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CosmeticaController extends Controller
{
    /**
     * コスメティカ トップページ
     */
    public function index(): View
    {
        $site = $this->getSite();
        $categories = CosmeCategory::categoryType()->orderBy('sort_order')->get();
        $moods = CosmeCategory::moodType()->orderBy('sort_order')->get();

        $itemsQuery = $site->items()->with(['shop', 'cosmeCategories']);

        if (request()->filled('cosme_category_id')) {
            $itemsQuery->whereHas('cosmeCategories', fn ($q) => $q->where('cosme_categories.id', request('cosme_category_id')));
        }
        if (request()->filled('keyword')) {
            $keyword = trim(request('keyword'));
            $itemsQuery->where(function ($q) use ($keyword) {
                $q->where('item_name', 'like', '%' . $keyword . '%')
                    ->orWhere('catchcopy', 'like', '%' . $keyword . '%')
                    ->orWhere('item_caption', 'like', '%' . $keyword . '%');
            });
        }

        $baseQuery = $site->items()->with(['shop', 'cosmeCategories']);
        $trending = (clone $baseQuery)->orderBy('review_count', 'desc')->limit(10)->get();
        $ranking = (clone $baseQuery)->orderBy('review_average', 'desc')->where('review_count', '>=', 1)->limit(10)->get();
        $items = (clone $itemsQuery)->orderBy('id')->paginate(24);

        return view('cosmetica.home', [
            'site' => $site,
            'categories' => $categories,
            'moods' => $moods,
            'trending' => $trending,
            'ranking' => $ranking,
            'items' => $items,
        ]);
    }

    /**
     * 商品一覧（無限スクロール用 JSON）
     */
    public function items(Request $request)
    {
        $site = $this->getSite();
        $query = $site->items()->with(['shop', 'cosmeCategories']);

        $cosmeCategoryId = $request->input('cosme_category_id');
        if ($cosmeCategoryId) {
            $query->whereHas('cosmeCategories', fn ($q) => $q->where('cosme_categories.id', $cosmeCategoryId));
        }

        $items = $query->orderBy('id')->paginate(24);

        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    protected function getSite(): Site
    {
        $siteId = config('cosmetica.site_id');
        return Site::findOrFail($siteId);
    }
}
