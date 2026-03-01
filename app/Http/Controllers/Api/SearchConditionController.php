<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSearchConditionRequest;
use App\Models\SearchCondition;
use App\Models\Site;
use Illuminate\Http\JsonResponse;

class SearchConditionController extends Controller
{
    /**
     * 検索条件を登録する。
     * 認証は access_code（sites.access_code）で行う。
     */
    public function store(StoreSearchConditionRequest $request): JsonResponse
    {
        $site = Site::where('access_code', $request->input('access_code'))->firstOrFail();

        $attributes = $request->only([
            'frequency',
            'keyword',
            'or_flag',
            'ng_keyword',
            'shop_code',
            'item_code',
            'genre_id',
            'tag_id',
            'min_price',
            'max_price',
            'availability',
            'purchase_type',
            'overwrite',
            'is_active',
            'total_hits',
        ]);

        $attributes['site_id'] = $site->id;
        $attributes['total_hits'] = $attributes['total_hits'] ?? 300;
        $attributes['overwrite'] = $attributes['overwrite'] ?? 0;
        $attributes['is_active'] = $attributes['is_active'] ?? 1;

        $condition = SearchCondition::create($attributes);

        return response()->json([
            'message' => '検索条件を登録しました。',
            'data' => [
                'id' => $condition->id,
                'site_id' => $condition->site_id,
                'frequency' => $condition->frequency,
                'keyword' => $condition->keyword,
                'is_active' => $condition->is_active,
                'created_at' => $condition->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
