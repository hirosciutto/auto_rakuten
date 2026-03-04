<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexItemsRequest;
use App\Http\Requests\Api\ShowItemRequest;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    /**
     * 指定サイトに紐づく商品を取得する。
     * keyword / or_flag / ng_keyword / genre_id / tag_id で絞り込み可能。
     */
    public function index(IndexItemsRequest $request): JsonResponse
    {
        $site = Site::where('access_code', $request->input('access_code'))->firstOrFail();

        Log::info('Api ItemController.index', [
            'site_id' => $site->id,
            'search' => [
                'keyword' => $request->input('keyword'),
                'or_flag' => $request->input('or_flag'),
                'ng_keyword' => $request->input('ng_keyword'),
                'genre_id' => $request->input('genre_id'),
                'tag_id' => $request->input('tag_id'),
                'page' => $request->input('page'),
                'per_page' => $request->input('per_page'),
            ],
        ]);

        $query = $site->items()->with('shop');

        if ($request->filled('keyword')) {
            $keywords = preg_split('/\s+/u', trim($request->input('keyword')), -1, PREG_SPLIT_NO_EMPTY);
            $orFlag = (int) $request->input('or_flag', 0);

            if ($orFlag === 1) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere(function ($q2) use ($word) {
                            $q2->where('item_name', 'like', '%' . $word . '%')
                                ->orWhere('catchcopy', 'like', '%' . $word . '%')
                                ->orWhere('item_caption', 'like', '%' . $word . '%');
                        });
                    }
                });
            } else {
                foreach ($keywords as $word) {
                    $query->where(function ($q) use ($word) {
                        $q->where('item_name', 'like', '%' . $word . '%')
                            ->orWhere('catchcopy', 'like', '%' . $word . '%')
                            ->orWhere('item_caption', 'like', '%' . $word . '%');
                    });
                }
            }
        }

        if ($request->filled('ng_keyword')) {
            $ngWords = preg_split('/\s+/u', trim($request->input('ng_keyword')), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($ngWords as $ng) {
                $query->where(function ($q) use ($ng) {
                    $q->where('item_name', 'not like', '%' . $ng . '%')
                        ->where('catchcopy', 'not like', '%' . $ng . '%')
                        ->where('item_caption', 'not like', '%' . $ng . '%');
                });
            }
        }

        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->input('genre_id'));
        }

        if ($request->filled('tag_id')) {
            $tagIds = array_map('intval', array_filter(explode(',', $request->input('tag_id'))));
            foreach ($tagIds as $tid) {
                $query->whereJsonContains('tag_ids', $tid);
            }
        }

        $perPage = (int) $request->input('per_page', 30);
        $items = $query->orderBy('id')->paginate($perPage);

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

    /**
     * 指定サイトに紐づく商品を1件取得する。
     */
    public function show(ShowItemRequest $request, string $id): JsonResponse
    {
        $site = Site::where('access_code', $request->input('access_code'))->firstOrFail();
        $itemId = (int) $id;
        if ($itemId < 1) {
            return response()->json(['message' => 'Invalid item id.'], 422);
        }

        $item = $site->items()->where('items.id', $itemId)->with('shop')->first();

        if (! $item) {
            return response()->json(['message' => '指定された商品が見つかりません。'], 404);
        }

        return response()->json(['data' => $item]);
    }
}
