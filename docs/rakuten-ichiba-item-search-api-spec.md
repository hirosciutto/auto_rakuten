# Rakuten Ichiba Item Search API 仕様書

**バージョン:** 2022-06-01
**参照:** https://webservice.rakuten.co.jp/documentation/ichiba-item-search

---

## 1. 概要

楽天市場に掲載されている商品データを取得するAPI。以下の商品は対象外：  
- 共同出品商品
- オークション出品
- フリマ出品
- C2Cオークション出品

キーワード検索を起点に、ショップやジャンル情報で絞り込み可能。旧APIからの主な改善点：  
- 出力パラメータの指定
- 商品コードでの検索
- レビュー有無での検索
- 配送日指定可否での検索 など

---

## 2. エンドポイント

```
https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/20220601?[parameter]=[value]…
```

- **JSONP:** コールバック関数名は入力パラメータで指定可能
- **エンコーディング:** `keyword` および `sort` の値は UTF-8 で URL エンコードすること（リクエスト全体ではなく、各パラメータ値のみ）
- **注意:** 同一URLへの短時間の大量アクセスは、一定時間レスポンス不能になる場合あり  

---

## 3. 入力ヘッダー

| ID | ヘッダー名 | ヘッダー | 型（最大バイト） | 必須 | デフォルト | 備考 |
|----|------------|----------|------------------|------|------------|------|
| 1 | Access key token【NEW】 | Authorization | String | ✓ | - | `Bearer {accessKey}` 形式。ヘッダーまたはパラメータのどちらかで指定。App ID とともに必須。[確認](https://webservice.rakuten.co.jp/app/list) |  

---

## 4. 入力パラメータ

### 4.1 共通パラメータ

| ID | パラメータ名 | パラメータ | 型（最大バイト） | 必須 | デフォルト | 備考 |
|----|--------------|------------|------------------|------|------------|------|
| 1 | App ID | applicationId | String | ✓ | - | Access Key とともに必須 |
| 2 | Access Key【NEW】 | accessKey | String | ✓ | - | ヘッダーまたはパラメータで指定可。App ID とともに必須 |
| 3 | Affiliate ID | affiliateId | String | - | - | アフィリエイト用 |
| 4 | レスポンス形式 | format | String | - | json | json または xml。JSON の場合は callback で JSONP 指定可 |
| 5 | コールバック関数名 | callback | String | - | - | JSONP 用。UTF-8 URL エンコード。英数字・ピリオド・アンダースコア |
| 6 | 出力フィールド選択 | elements | String | - | - | カンマ区切りで返却フィールドを指定。例: `elements=itemName,itemPrice,itemUrl` |
| 7 | フォーマットバージョン | formatVersion | int | - | 1 | 1: `items[0].item.itemName` 形式 / 2: `items[0].itemName` 形式（フラット） |  

#### 出力フォーマットのバージョンに関して

2 を指定すると、JSONの出力方法が改善され以下のようになります。  

```

formatVersion=1 の場合:
配列データに関して、以下の様にデータが返ります。
したがって、最初の itemName にアクセスするためにitems[0].item.itemNameとたどる必要があります。  

{"items": [
    {"item": {
        "itemName": "a",
        "itemPrice": 10
    }},
    {"item": {
        "itemName": "b",
        "itemPrice": 20
    }}
]}

formatVersion=2 の場合:
下記のように、配列中の重複するオブジェクトが省略されます。
最初の itemName にアクセスするためにitems[0].itemNameでアクセスできます。  

{"items": [
    {
        "itemName": "a",
        "itemPrice": 10
    },
    {
        "itemName": "b",
        "itemPrice": 20
    }
]}

```

### 4.2 サービス固有パラメータ

※次のいずれか1つは必須: **keyword**, **genreId**, **itemCode**, **shopCode**  

| ID | パラメータ名 | パラメータ | 型 | 必須 | デフォルト | 備考 |
|----|--------------|------------|-----|------|------------|------|
| 1 | 検索キーワード | keyword | String | (*1) | - | UTF-8 URL エンコード。最大128半角文字。半角スペース区切りで AND 検索（orFlag=1 で OR）。キーワードは2半角文字以上または1全角文字以上（ひらがな・カタカナ・記号の場合は2文字以上） |
| 2 | ショップコード | shopCode | String | (*1) | - | ショップURLの `http://www.rakuten.co.jp/[xyz]` の [xyz] 部分 |
| 3 | 商品コード | itemCode | String | (*1) | - | Item Search/Ranking API の出力形式。`shop:1234` 形式 |
| 4 | ジャンルID | genreId | long | (*1) | 0 | 楽天市場ジャンル。ジャンル検索APIで名称・階層を確認 |
| 5 | タグID | tagId | long | - | - | カンマ区切り、最大10個 |
| 6 | 1ページあたりの取得件数 | hits | int | - | 30 | 1〜30 |
| 7 | 取得ページ | page | int | - | 1 | 1〜100 |
| 8 | ソート | sort | String | - | standard | UTF-8 URL エンコード必須。下記参照 |  
| 9 | 最低価格 | minPrice | long | - | - | 0超、999,999,999未満 |
| 10 | 最高価格 | maxPrice | long | - | - | 0超、999,999,999未満。minPrice より大きいこと |
| 11 | 在庫ありのみ | availability | int(1) | - | 1 | 0: すべて / 1: 在庫ありのみ |
| 12 | 検索範囲 | field | int(1) | - | 1 | 0: 広い（多めにマッチ） / 1: 狭い（少なめにマッチ） |
| 13 | プラットフォーム | carrier | int(1) | - | 0 | 0: PC / 1: モバイル（日本のみ） / 2: スマートフォン |
| 14 | 画像ありフラグ | imageFlag | int(1) | - | 0 | 0: すべて / 1: 画像ありのみ |
| 15 | OR検索フラグ | orFlag | int(1) | - | 0 | 0: AND / 1: OR。（A and B）or C のような複合条件は不可 |
| 16 | 除外キーワード | NGKeyword | String | - | - | UTF-8 URL エンコード。keyword と同形式 |
| 17 | 購入タイプ | purchaseType | int(1) | - | 0 | 0: 通常 / 1: 定期購入 / 2: 頒布会 |
| 18 | 海外配送フラグ | shipOverseasFlag | int(1) | - | 0 | 0: すべて / 1: 海外配送可のみ |
| 19 | 海外配送地域 | shipOverseasArea | String | - | ALL | 地域コードで指定。shipOverseasFlag=1 のとき有効 |
| 20 | あす楽フラグ | asurakuFlag | int(1) | - | 0 | 0: すべて / 1: あす楽対象のみ ※2024/7/1以降は常に 0 |
| 21 | あす楽エリア | asurakuArea | int | - | 0 | asurakuFlag=1 のとき有効 |
| 22 | ポイント倍率フラグ | pointRateFlag | int(1) | - | 0 | 0: すべて / 1: ポイント倍率ありのみ |
| 23 | ポイント倍率 | pointRate | int | - | - | 2〜10（例: 5 で5倍）。pointRateFlag=1 のとき有効 |
| 24 | 送料フラグ | postageFlag | int(1) | - | 0 | 0: すべて / 1: 送料込み（送料無料）のみ |
| 25 | クレジットカードフラグ | creditCardFlag | int(1) | - | 0 | 0: すべて / 1: クレジットカード可のみ |
| 26 | ラッピングフラグ | giftFlag | int(1) | - | 0 | 0: すべて / 1: ラッピング可のみ |
| 27 | レビューありフラグ | hasReviewFlag | int(1) | - | 0 | 0: すべて / 1: レビューありのみ |
| 28 | 最大アフィリエイト率 | maxAffiliateRate | float | - | - | 1.0〜99.9（小数点1桁）。指定値より高い率は非表示 |
| 29 | 最小アフィリエイト率 | minAffiliateRate | float | - | - | 1.0〜99.9。指定値より低い率は非表示。max より小さくすること |
| 30 | 動画ありフラグ | hasMovieFlag | int(1) | - | 0 | 0: すべて / 1: 動画ありのみ（動画URLを返す） |
| 31 | パンフレットフラグ | pamphletFlag | int(1) | - | 0 | 0: すべて / 1: パンフレットありのみ |
| 32 | 配送日指定可フラグ | appointDeliveryDateFlag | int(1) | - | 0 | 0: すべて / 1: 配送日指定可のみ |
| 33 | 出力要素 | elements | String | - | ALL | カンマ区切り。例: `elements=reviewCount,reviewAverage` |
| 34 | ジャンル情報フラグ | genreInformationFlag | int(1) | - | 0 | 0: ジャンル別件数なし / 1: ジャンル別件数あり |
| 35 | タグ情報フラグ | tagInformationFlag | int(1) | - | 0 | 0: タグ別件数なし / 1: タグ別件数あり |

### 4.3 ソート (sort) の値

| 値 | 説明 |
|----|------|
| +affiliateRate | アフィリエイト率 昇順 |
| -affiliateRate | アフィリエイト率 降順 |
| +reviewCount | レビュー数 昇順 |
| -reviewCount | レビュー数 降順 |
| +reviewAverage | レビュー平均 昇順 |
| -reviewAverage | レビュー平均 降順 |
| +itemPrice | 価格 昇順 |
| -itemPrice | 価格 降順 |
| +updateTimestamp | 更新日時 昇順 |
| -updateTimestamp | 更新日時 降順 |
| standard | 楽天標準ソート |

---

## 5. 出力パラメータ

### 5.1 全体・ページ情報

| 説明 | パラメータ | 備考 |
|------|------------|------|
| 検索結果総件数 | count | 検索にヒットした商品総数 |
| 現在ページ | page | 現在のページ番号 |
| 表示開始番号 | first | 何件目から表示か |
| 表示終了番号 | last | 何件目まで表示か |
| 取得件数 | hits | 返却件数 |
| プラットフォーム | carrier | 0: PC / 1: モバイル / 2: スマートフォン |
| 総ページ数 | pageCount | 最大100 |

### 5.2 商品（Item）情報

| 説明 | パラメータ | 備考 |
|------|------------|------|
| 商品名 | itemName | キャッチコピー＋商品名で表示推奨。carrier で変動あり |
| キャッチコピー | catchcopy | 販売メッセージ |
| 商品コード | itemCode | |
| 商品価格 | itemPrice | |
| 商品説明 | itemCaption | carrier で変動あり |
| 商品URL | itemUrl | https 始まり。affiliateId 指定時は affiliateUrl と同値（2015/7/1〜） |
| 価格ベースフィールド | itemPriceBaseField | "item_price_min1", "item_price_min2", "item_price_min3" のいずれか |
| 価格 max1〜min3 | itemPriceMax1, itemPriceMax2, itemPriceMax3, itemPriceMin1, itemPriceMin2, itemPriceMin3 | 検索用・購入用の価格範囲 |
| アフィリエイトURL | affiliateUrl | affiliateId 指定時のみ。https 始まり。carrier に依存しない |
| 画像ありフラグ | imageFlag | 0: 画像なし / 1: 画像あり |
| 小さい画像URL(64x64) | smallImageUrls | 最大3件の配列。https |
| 中画像URL(128x128) | mediumImageUrls | 最大3件の配列。https |
| 在庫フラグ | availability | 0: 在庫切れ / 1: 在庫あり |
| 税フラグ | taxFlag | 0: 税込 / 1: 税抜 |
| 送料フラグ | postageFlag | 0: 送料込み / 1: 送料別 |
| クレジットカードフラグ | creditCardFlag | 0: 不可 / 1: 可 |
| ショップ・オブ・ザ・イヤー | shopOfTheYearFlag | 0: 未受賞 / 1: 受賞 |
| 海外配送フラグ | shipOverseasFlag | 0: 不可 / 1: 可 |
| 海外配送エリア | shipOverseasArea | 対応国を / 区切り |
| あす楽フラグ | asurakuFlag | 0: 対象外 / 1: あす楽可 ※2024/7/1以降は常に 0 |
| あす楽締め切り | asurakuClosingTime | "HH:MM" 形式。asurakuFlag=1 のときのみ |
| あす楽配送エリア | asurakuArea | / 区切り。asurakuFlag=1 のときのみ |
| アフィリエイト率 | affiliateRate | |
| セール開始 | startTime | 期間限定セール時のみ。"YYYY-MM-DD HH:MM" |
| セール終了 | endTime | 期間限定セール時のみ。"YYYY-MM-DD HH:MM" |
| レビュー数 | reviewCount | |
| レビュー平均 | reviewAverage | |
| ポイント倍率 | pointRate | 例: 5＝5倍。24時間以内に終了するものは含まない |
| ポイント倍率開始 | pointRateStartTime | 24時間以内終了のものは含まない |
| ポイント倍率終了 | pointRateEndTime | 同上 |
| ラッピングフラグ | giftFlag | 0: 不可 / 1: 可 |

### 5.3 ショップ情報

| 説明 | パラメータ | 備考 |
|------|------------|------|
| ショップ名 | shopName | |
| ショップコード | shopCode | URL の [xyz] 部分 |
| ショップURL | shopUrl | https。affiliateId 指定時は shopAffiliateUrl と同値（2015/7/1〜） |
| ショップアフィリエイトURL | shopAffiliateUrl | affiliateId 指定時のみ。https |

### 5.4 ジャンル・タグ情報

- **ジャンル情報** (genreInformationFlag=1 時): genreId, genreName, genreLevel, itemCount、親ジャンル(parent)・現在(current)・子ジャンル(child)の階層  
- **タグ情報** (tagInformationFlag=1 時): tagIds（配列）、tagGroup / tagGroupName / tagGroupId、tags 配下の tagId, tagName, parentTagId, itemCount  

---

## 6. エラー

レスポンスは HTTP ステータスコードとレスポンスボディで表現。

| HTTPステータス | 内容 | レスポンス例（JSON） |
|----------------|------|----------------------|
| 400 | パラメータ誤り・不足 | applicationId 未設定: `{"error":"wrong_parameter","error_description":"specify valid applicationId"}` / keyword 不正: `{"error":"wrong_parameter","error_description":"keyword parameter is not valid"}` |
| 404 | データなし | `{"error":"not_found","error_description":"not found"}` |
| 429 | リクエスト数超過 | `{"error":"too_many_requests","error_description":"number of allowed requests has been exceeded for this API. please try again soon."}` |
| 500 | 楽天側内部エラー | `{"error":"system_error","error_description":"api logic error"}` |
| 503 | メンテナンス・過負荷 | `{"error":"service_unavailable","error_description":"XXX/XXX is under maintenance"}` |

- レスポンス形式は入力の `format` に従う（json / xml）。  

---

## 7. 補足

### 7.1 商品ポイント倍率

通常は購入額の1%がポイント。商品ごとに期間限定で倍率を設定可能。本APIは**商品単位**のポイント倍率のみ対応（ショップ単位の倍率は非対応）。  

### 7.2 アフィリエイト

`affiliateId` を付与するとレスポンスにアフィリエイトURLが含まれる。そのURL経由の成果でアフィリエイト報酬が付与される。PC/モバイル同一の手順。  

### 7.3 リクエスト例（キーワード「福袋」、価格昇順）

```
https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/20220601?applicationId=[APPLICATION ID]&keyword=%E7%A6%8F%E8%A2%8B&sort=%2BitemPrice
```

（実際のリクエストでは改行せず1行で。accessKey はヘッダー `Authorization: Bearer {accessKey}` またはパラメータで指定。）  

---

## 8. 旧バージョン

- 2017-07-06 版は別途ドキュメントを参照。  
