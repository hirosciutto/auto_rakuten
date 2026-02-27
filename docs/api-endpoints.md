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
| page | integer | 取得ページ（1〜100） |
| min_price | integer | 最低価格（0〜999999998） |
| max_price | integer | 最高価格（0〜999999999） |
| availability | integer | 在庫。`0`: すべて / `1`: 在庫ありのみ |
| purchase_type | integer | 購入タイプ。`0`: 通常 / `1`: 定期購入 / `2`: 頒布会 |
| overwrite | integer | 既存商品の上書き。`0`: スキップ / `1`: 上書き。省略時は `0` |
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

`data` 内の各要素には、`items` テーブルの項目（商品名・価格・URL・画像URL・レビュー数など）が含まれます。  
`shop` は紐づくショップ情報です。

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
