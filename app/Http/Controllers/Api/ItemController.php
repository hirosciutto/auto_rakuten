<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexItemsRequest;
use App\Models\Site;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    /**
     * 指定サイトに紐づく商品を取得する。
     * keyword / or_flag / ng_keyword / genre_id / tag_id で絞り込み可能。
     */
    public function index(IndexItemsRequest $request): JsonResponse
    {
        $site = Site::where('access_code', $request->input('access_code'))->firstOrFail();

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
            $ng = trim($request->input('ng_keyword'));
            $query->where(function ($q) use ($ng) {
                $q->where('item_name', 'not like', '%' . $ng . '%')
                    ->where('catchcopy', 'not like', '%' . $ng . '%')
                    ->where('item_caption', 'not like', '%' . $ng . '%');
            });
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
}
