# API エンドポイント仕様

外部から検索条件の登録および商品一覧の取得を行うための API です。  
認証は **access_code**（`sites.access_code`）で行います。

**ベースURL:** `https://cosmetica.jp/api`

**Content-Type:** `application/json`  
**Accept:** `application/json`

---

## 1. 検索条件の登録

登録した検索条件は、`itemlist:search` コマンド実行時に楽天APIで商品を取得する際に使用されます。

### エンドポイント

```
POST /api/search-conditions
```

### リクエスト

#### 必須パラメータ（Body: JSON）

| パラメータ | 型 | 説明 |
|------------|-----|------|
| access_code | string | サイト識別用。`sites.access_code` と一致する値。 |
| frequency | string | 実行頻度。`once` / `daily` / `weekly` / `monthly` のいずれか。 |

#### 任意パラメータ（Body: JSON）

| パラメータ | 型 | 説明 |
|------------|-----|------|
| keyword | string | 検索キーワード（最大256文字） |
| or_flag | integer | 複数キーワード時の検索方法。`0`: AND / `1`: OR |
| ng_keyword | string | 除外キーワード（最大256文字） |
| shop_code | string | ショップコード（最大64文字） |
| item_code | string | 商品コード（最大64文字）。例: `shop:1234` |
| genre_id | integer | ジャンルID |
| tag_id | string | タグID（カンマ区切り可、最大128文字） |
| sort | string | 楽天API ソート。`standard`（省略時）/ `+itemPrice` / `-itemPrice` / `+reviewCount` / `-reviewCount` / `+reviewAverage` / `-reviewAverage` / `+affiliateRate` / `-affiliateRate` / `+updateTimestamp` / `-updateTimestamp` |
| total_hits | integer | 取得目標件数（1〜3000）。省略時は 300。hits=30 で page 1 から繰り返し、合計この件数まで取得。overwrite=0 の場合は既存紐づけをスキップし新規がこの件数になるまで最大100ページまで取得。 |
| min_price | integer | 最低価格（0〜999999998） |
| max_price | integer | 最高価格（0〜999999999） |
| availability | integer | 在庫。`0`: すべて / `1`: 在庫ありのみ |
| purchase_type | integer | 購入タイプ。`0`: 通常 / `1`: 定期購入 / `2`: 頒布会 |
| overwrite | integer | 既存商品の上書き。`0`: スキップ（既にサイトに紐づく商品は数えず、新規紐づけが total_hits 件になるまで取得を続ける） / `1`: 上書き。省略時は `0` |
| is_active | integer | 有効フラグ。`0`: 無効 / `1`: 有効。省略時は `1` |

※ keyword / shop_code / item_code / genre_id のいずれか1つは、楽天API仕様上指定が推奨されます。

### レスポンス

#### 成功時（201 Created）

```json
{
  "message": "検索条件を登録しました。",
  "data": {
    "id": 1,
    "site_id": 1,
    "frequency": "daily",
    "keyword": "福袋",
    "is_active": 1,
    "created_at": "2025-02-27T12:00:00+00:00"
  }
}
```

#### バリデーションエラー時（422 Unprocessable Entity）

- `access_code` に紐づくサイトが存在しない場合、`access_code` にエラーメッセージが返ります。
- その他、パラメータの型・範囲が不正な場合も 422 でエラー内容が返ります。

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "access_code": [
      "指定された access_code に紐づくサイトが存在しません。"
    ]
  }
}
```

### リクエスト例

```bash
curl -X POST https://cosmetica.jp/api/search-conditions \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "access_code": "your_access_code",
    "frequency": "daily",
    "keyword": "福袋",
    "is_active": 1
  }'
```

---

## 2. 商品一覧の取得

指定サイトに紐づく商品（item_sites 経由）を、キーワード・ジャンル・タグなどで絞り込んで取得します。

### エンドポイント

```
GET /api/items
```

### リクエスト

#### 必須パラメータ（クエリ）

| パラメータ | 型 | 説明 |
|------------|-----|------|
| access_code | string | サイト識別用。`sites.access_code` と一致する値。 |

#### 任意パラメータ（クエリ）

| パラメータ | 型 | 説明 |
|------------|-----|------|
| keyword | string | 商品名・キャッチコピー・商品説明のいずれかに含まれる文字列。複数語は半角スペース区切り。 |
| or_flag | integer | 複数キーワード時の一致条件。`0`: すべて含む（AND） / `1`: いずれか含む（OR）。省略時は `0` |
| ng_keyword | string | 商品名・キャッチコピー・商品説明のいずれにも含まれない商品のみ返す。 |
| genre_id | integer | ジャンルIDで完全一致。 |
| tag_id | string | タグIDでフィルタ。複数指定はカンマ区切り（例: `1,2,3`）。指定したタグのいずれかを含む商品を返す。 |
| page | integer | ページ番号（1以上）。省略時は `1` |
| per_page | integer | 1ページあたりの件数（1〜100）。省略時は `30` |

キーワードのスペースは、URL上では `+` または `%20` のどちらで送っても同じように解釈されます。

### レスポンス

#### 成功時（200 OK）

```json
{
  "data": [
    {
      "id": 1,
      "item_code": "shop:1234",
      "item_name": "商品名",
      "catchcopy": "キャッチコピー",
      "item_price": 1980,
      "item_url": "https://item.rakuten.co.jp/shop/1234/",
      "affiliate_url": "https://...",
      "genre_id": 100,
      "tag_ids": [1, 2],
      "shop": {
        "id": 1,
        "shop_code": "shop",
        "shop_name": "ショップ名",
        "shop_url": "https://...",
        "shop_affiliate_url": "https://..."
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 30,
    "total": 142
  }
}
```

**`data` 内の各要素（商品オブジェクト）のプロパティ一覧**

| プロパティ | 型 | 説明 |
|------------|-----|------|
| id | integer | 商品ID（内部） |
| shop_id | integer \| null | ショップID |
| item_code | string | 商品コード（例: shop:1234） |
| item_name | string \| null | 商品名 |
| catchcopy | string \| null | キャッチコピー |
| item_price | integer \| null | 商品価格 |
| item_caption | string \| null | 商品説明 |
| item_url | string \| null | 商品URL |
| affiliate_url | string \| null | アフィリエイトURL |
| item_price_base_field | string \| null | 価格ベースフィールド |
| item_price_max1 | integer \| null | 価格 max1 |
| item_price_max2 | integer \| null | 価格 max2 |
| item_price_max3 | integer \| null | 価格 max3 |
| item_price_min1 | integer \| null | 価格 min1 |
| item_price_min2 | integer \| null | 価格 min2 |
| item_price_min3 | integer \| null | 価格 min3 |
| image_flag | integer \| null | 0: 画像なし / 1: 画像あり |
| small_image_urls | array \| null | 小さい画像URL（64x64、最大3件） |
| medium_image_urls | array \| null | 中画像URL（128x128、最大3件） |
| availability | integer \| null | 0: 在庫切れ / 1: 在庫あり |
| tax_flag | integer \| null | 0: 税込 / 1: 税抜 |
| postage_flag | integer \| null | 0: 送料込み / 1: 送料別 |
| credit_card_flag | integer \| null | 0: 不可 / 1: 可 |
| shop_of_the_year_flag | integer \| null | 0: 未受賞 / 1: 受賞 |
| ship_overseas_flag | integer \| null | 0: 不可 / 1: 可 |
| ship_overseas_area | string \| null | 海外配送エリア |
| asuraku_flag | integer \| null | 0: 対象外 / 1: あす楽可 |
| asuraku_closing_time | string \| null | あす楽締め切り（HH:MM） |
| asuraku_area | string \| null | あす楽配送エリア |
| affiliate_rate | number \| null | アフィリエイト率 |
| start_time | string \| null | セール開始（YYYY-MM-DD HH:MM） |
| end_time | string \| null | セール終了（YYYY-MM-DD HH:MM） |
| review_count | integer \| null | レビュー数 |
| review_average | number \| null | レビュー平均 |
| point_rate | integer \| null | ポイント倍率 |
| point_rate_start_time | string \| null | ポイント倍率開始 |
| point_rate_end_time | string \| null | ポイント倍率終了 |
| gift_flag | integer \| null | 0: ラッピング不可 / 1: 可 |
| genre_id | integer \| null | ジャンルID |
| tag_ids | array \| null | タグIDの配列 |
| created_at | string | 作成日時（ISO 8601） |
| updated_at | string | 更新日時（ISO 8601） |
| shop | object | 紐づくショップ（下記） |

**`shop` オブジェクトのプロパティ一覧**

| プロパティ | 型 | 説明 |
|------------|-----|------|
| id | integer | ショップID |
| shop_code | string | ショップコード |
| shop_name | string | ショップ名 |
| shop_url | string | ショップURL |
| shop_affiliate_url | string \| null | ショップアフィリエイトURL |
| created_at | string | 作成日時 |
| updated_at | string | 更新日時 |

#### エラー時

- **access_code** に紐づくサイトが存在しない場合: **422 Unprocessable Entity**。`errors.access_code` にメッセージが入ります。
- 必須パラメータ不足など: **422** でバリデーションエラー内容が返ります。

### リクエスト例

```bash
# 基本（access_code のみ）
curl "https://cosmetica.jp/api/items?access_code=your_access_code"

# キーワード検索（AND: 「福袋」と「おすすめ」の両方を含む）
curl "https://cosmetica.jp/api/items?access_code=your_access_code&keyword=福袋+おすすめ"

# キーワード検索（OR: 「福袋」または「おすすめ」を含む）
curl "https://cosmetica.jp/api/items?access_code=your_access_code&keyword=福袋+おすすめ&or_flag=1"

# 除外キーワード・ジャンル・タグ・ページネーション
curl "https://cosmetica.jp/api/items?access_code=your_access_code&ng_keyword=中古&genre_id=100&tag_id=1,2&page=1&per_page=20"
```

---

## 共通事項

- いずれのエンドポイントでも、**access_code** が不正または未指定の場合は 422 となり、サイトが存在しない旨のメッセージが返ります。
- エラーレスポンスは Laravel の標準形式に従い、`message` および `errors`（バリデーション時）を含みます。
