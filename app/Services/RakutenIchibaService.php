<?php

namespace App\Services;

use App\Models\Item;
use App\Models\SearchCondition;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenIchibaService
{
    public function search(SearchCondition $condition): array
    {
        $params = $this->buildParams($condition);
        $endpoint = config('rakuten.item_search.endpoint');
        $accessKey = config('rakuten.access_key');
        $response = Http::withToken($accessKey)
            ->get($endpoint, $params);

        if (! $response->successful()) {
            Log::warning('Rakuten Ichiba API error', [
                'search_condition_id' => $condition->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Rakuten API error: ' . $response->body());
        }

        $data = $response->json();
        if (isset($data['error'])) {
            throw new \RuntimeException('Rakuten API error: ' . ($data['error_description'] ?? $data['error']));
        }

        return $data;
    }

    /**
     * 検索実行し、shops / items / item_sites に保存する。
     *
     * @return array{count: int, saved_items: int, saved_shops: int}
     */
    public function searchAndSave(SearchCondition $condition, int $siteId): array
    {
        $data = $this->search($condition);
        $items = $data['items'] ?? [];
        $count = (int) ($data['count'] ?? 0);

        $savedShops = 0;
        $savedItems = 0;

        DB::beginTransaction();
        try {
            foreach ($items as $row) {
                $shop = $this->upsertShop($row);
                if ($shop->wasRecentlyCreated) {
                    $savedShops++;
                }

                $item = $this->upsertItem($row, $shop->id, $condition->overwrite === 1);
                if ($item) {
                    $savedItems++;
                    $item->sites()->syncWithoutDetaching([$siteId]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'count' => $count,
            'saved_items' => $savedItems,
            'saved_shops' => $savedShops,
        ];
    }

    protected function buildParams(SearchCondition $condition): array
    {
        $config = config('rakuten');
        $params = [
            'applicationId' => $config['application_id'],
            'affiliateId' => $config['affiliate_id'] ?: null,
            'formatVersion' => config('rakuten.item_search.format_version'),
            'hits' => config('rakuten.item_search.default_hits'),
            'page' => $condition->page ?? config('rakuten.item_search.default_page'),
        ];

        $params = array_filter($params);

        if ($condition->keyword) {
            $params['keyword'] = $condition->keyword;
        }
        if ($condition->shop_code) {
            $params['shopCode'] = $condition->shop_code;
        }
        if ($condition->item_code) {
            $params['itemCode'] = $condition->item_code;
        }
        if ($condition->genre_id !== null && $condition->genre_id !== '') {
            $params['genreId'] = $condition->genre_id;
        }
        if ($condition->tag_id) {
            $params['tagId'] = $condition->tag_id;
        }
        if ($condition->min_price !== null) {
            $params['minPrice'] = $condition->min_price;
        }
        if ($condition->max_price !== null) {
            $params['maxPrice'] = $condition->max_price;
        }
        if ($condition->availability !== null) {
            $params['availability'] = $condition->availability;
        }
        if ($condition->or_flag !== null) {
            $params['orFlag'] = $condition->or_flag;
        }
        if ($condition->ng_keyword) {
            $params['NGKeyword'] = $condition->ng_keyword;
        }
        if ($condition->purchase_type !== null) {
            $params['purchaseType'] = $condition->purchase_type;
        }

        return $params;
    }

    protected function upsertShop(array $row): Shop
    {
        $shopCode = $row['shopCode'] ?? null;
        if (! $shopCode) {
            throw new \RuntimeException('Item has no shopCode');
        }

        return Shop::updateOrCreate(
            ['shop_code' => $shopCode],
            [
                'shop_name' => $row['shopName'] ?? null,
                'shop_url' => $row['shopUrl'] ?? null,
                'shop_affiliate_url' => $row['shopAffiliateUrl'] ?? null,
            ]
        );
    }

    /**
     * @return Item|null 保存または更新した Item。overwrite=0 で既存の場合は既存の Item を返す（サイト紐づけのみ行う）
     */
    protected function upsertItem(array $row, ?int $shopId, bool $overwrite): ?Item
    {
        $itemCode = $row['itemCode'] ?? null;
        if (! $itemCode) {
            return null;
        }

        $existing = Item::where('item_code', $itemCode)->first();
        if ($existing && ! $overwrite) {
            return $existing;
        }

        $attributes = $this->mapItemRowToAttributes($row);
        $attributes['shop_id'] = $shopId;

        $item = Item::updateOrCreate(
            ['item_code' => $itemCode],
            $attributes
        );

        return $item;
    }

    /**
     * itemCode（形式: shop_code:item_id）から商品ページの正規URLを組み立てる。
     */
    protected function buildItemUrlFromItemCode(string $itemCode): ?string
    {
        if (preg_match('/^([^:]+):(.+)$/', $itemCode, $m)) {
            return 'https://item.rakuten.co.jp/' . $m[1] . '/' . $m[2] . '/';
        }

        return null;
    }

    protected function mapItemRowToAttributes(array $row): array
    {
        $itemCode = $row['itemCode'] ?? null;
        $affiliateUrl = $row['affiliateUrl'] ?? null;
        $itemUrl = $row['itemUrl'] ?? null;

        // アフィリエイトURLが返っている場合は itemUrl と同値になるため、item_url は itemCode から組み立てる
        if ($affiliateUrl !== null && $affiliateUrl !== '') {
            $itemUrl = $this->buildItemUrlFromItemCode((string) $itemCode) ?? $itemUrl;
        }

        return [
            'item_name' => $row['itemName'] ?? null,
            'catchcopy' => $row['catchcopy'] ?? null,
            'item_price' => isset($row['itemPrice']) ? (int) $row['itemPrice'] : null,
            'item_caption' => $row['itemCaption'] ?? null,
            'item_url' => $itemUrl,
            'affiliate_url' => $affiliateUrl,
            'item_price_base_field' => $row['itemPriceBaseField'] ?? null,
            'item_price_max1' => isset($row['itemPriceMax1']) ? (int) $row['itemPriceMax1'] : null,
            'item_price_max2' => isset($row['itemPriceMax2']) ? (int) $row['itemPriceMax2'] : null,
            'item_price_max3' => isset($row['itemPriceMax3']) ? (int) $row['itemPriceMax3'] : null,
            'item_price_min1' => isset($row['itemPriceMin1']) ? (int) $row['itemPriceMin1'] : null,
            'item_price_min2' => isset($row['itemPriceMin2']) ? (int) $row['itemPriceMin2'] : null,
            'item_price_min3' => isset($row['itemPriceMin3']) ? (int) $row['itemPriceMin3'] : null,
            'image_flag' => isset($row['imageFlag']) ? (int) $row['imageFlag'] : null,
            'small_image_urls' => $row['smallImageUrls'] ?? null,
            'medium_image_urls' => $row['mediumImageUrls'] ?? null,
            'availability' => isset($row['availability']) ? (int) $row['availability'] : null,
            'tax_flag' => isset($row['taxFlag']) ? (int) $row['taxFlag'] : null,
            'postage_flag' => isset($row['postageFlag']) ? (int) $row['postageFlag'] : null,
            'credit_card_flag' => isset($row['creditCardFlag']) ? (int) $row['creditCardFlag'] : null,
            'shop_of_the_year_flag' => isset($row['shopOfTheYearFlag']) ? (int) $row['shopOfTheYearFlag'] : null,
            'ship_overseas_flag' => isset($row['shipOverseasFlag']) ? (int) $row['shipOverseasFlag'] : null,
            'ship_overseas_area' => $row['shipOverseasArea'] ?? null,
            'asuraku_flag' => isset($row['asurakuFlag']) ? (int) $row['asurakuFlag'] : null,
            'asuraku_closing_time' => $row['asurakuClosingTime'] ?? null,
            'asuraku_area' => $row['asurakuArea'] ?? null,
            'affiliate_rate' => isset($row['affiliateRate']) ? (float) $row['affiliateRate'] : null,
            'start_time' => $row['startTime'] ?? null,
            'end_time' => $row['endTime'] ?? null,
            'review_count' => isset($row['reviewCount']) ? (int) $row['reviewCount'] : null,
            'review_average' => isset($row['reviewAverage']) ? (float) $row['reviewAverage'] : null,
            'point_rate' => isset($row['pointRate']) ? (int) $row['pointRate'] : null,
            'point_rate_start_time' => $row['pointRateStartTime'] ?? null,
            'point_rate_end_time' => $row['pointRateEndTime'] ?? null,
            'gift_flag' => isset($row['giftFlag']) ? (int) $row['giftFlag'] : null,
            'genre_id' => isset($row['genreId']) ? (int) $row['genreId'] : null,
            'tag_ids' => $row['tagIds'] ?? null,
        ];
    }
}
