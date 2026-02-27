<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\DataType;

class AjaxController extends Controller
{
    /**
     * BREAD browse 画面のトグルスイッチから呼ばれる汎用ステータス更新。
     * slug（テーブル名）と id, カラム名, ステータス値を受け取って更新する。
     */
    public function statusUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'slug'   => 'required|string',
            'id'     => 'required',
            'name'   => 'required|string',
            'status' => 'required|in:0,1,2,99',
        ]);

        $slug   = $request->input('slug');
        $id     = $request->input('id');
        $name   = $request->input('name');
        $status = (int) $request->input('status');

        $dataType = DataType::where('slug', $slug)->first();
        if (!$dataType) {
            return response()->json(['status' => 'error', 'message' => "DataType '{$slug}' が見つかりません。"], 404);
        }

        $tableName = $dataType->name;
        $primaryKey = 'id';

        // カラムがテーブルに存在するか確認
        if (!Schema::hasColumn($tableName, $name)) {
            return response()->json(['status' => 'error', 'message' => "カラム '{$name}' が存在しません。"], 422);
        }

        try {
            DB::table($tableName)
                ->where($primaryKey, $id)
                ->update([$name => $status]);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}
