<?php

namespace App\Console\Commands;

use App\Models\CosmeCategory;
use App\Models\Item;
use App\Models\Post;
use App\Services\OllamaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePostsFromItemsCommand extends Command
{
    protected $signature = 'posts:generate-from-items
                            {--dry-run : 記事・カテゴリは生成せず対象商品数だけ表示}';

    protected $description = 'site_id=1 に紐づく商品を review_count 順にループし、Ollama で記事生成・カテゴリ割り当てして posts / cosme_category_posts に保存する';

    public function handle(): int
    {
        $baseUrl = config('services.ollama.base_url', 'http://host.docker.internal:11434');
        $postModel = config('services.ollama.post_model');
        $categoryModel = config('services.ollama.category_model');

        if (! $postModel || ! $categoryModel) {
            $this->error('設定してください: OLLAMA_POST_MODEL, OLLAMA_CATEGORY_MODEL');
            return self::FAILURE;
        }

        $categoriesPath = storage_path('app/dev/cosmetic_categories.json');
        if (! File::exists($categoriesPath)) {
            $this->error("カテゴリJSONがありません: {$categoriesPath} (先に php artisan cosme-categories:export を実行してください)");
            return self::FAILURE;
        }

        $allCategories = json_decode(File::get($categoriesPath), true);
        if (! is_array($allCategories)) {
            $this->error('カテゴリJSONの形式が不正です。');
            return self::FAILURE;
        }
        $validIds = CosmeCategory::query()->pluck('id')->toArray();
        $byType = [
            'category' => array_values(array_filter($allCategories, fn ($c) => ($c['type'] ?? '') === 'category')),
            'mood' => array_values(array_filter($allCategories, fn ($c) => ($c['type'] ?? '') === 'mood')),
        ];

        $items = Item::query()
            ->whereHas('sites', fn ($q) => $q->where('sites.id', 1))
            ->orderByDesc('review_count')
            ->get(['id', 'item_name', 'catchcopy', 'item_caption']);

        if ($items->isEmpty()) {
            $this->warn('site_id=1 に紐づく商品がありません。');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("対象商品数: {$items->count()} 件（--dry-run のため実行しません）");
            return self::SUCCESS;
        }

        $ollama = new OllamaService($baseUrl);

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        foreach ($items as $item) {
            try {
                $post = $this->generatePostForItem($ollama, $item, $postModel);
                $this->assignCategories($ollama, $post, $item, $categoryModel, $byType, $validIds);
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  item_id={$item->id} {$item->item_name}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('完了しました。');

        return self::SUCCESS;
    }

    protected function generatePostForItem(OllamaService $ollama, Item $item, string $model): Post
    {
        $prompt = <<<PROMPT
あなたはコスメ紹介サイトのライターです。以下の商品情報だけを元に、読者に役立つ紹介記事を400字程度で書いてください。
記事以外の説明や挨拶は不要です。本文だけを返してください。

- 商品名: {$item->item_name}
- キャッチコピー: {$item->catchcopy}
- 商品説明: {$item->item_caption}
PROMPT;

        $body = trim($ollama->generate($model, $prompt));

        $post = Post::query()->firstOrNew(['item_id' => $item->id]);
        $post->title = $item->item_name;
        $post->body = $body;
        $post->published_at = null;
        $post->save();

        return $post;
    }

    protected function assignCategories(
        OllamaService $ollama,
        Post $post,
        Item $item,
        string $model,
        array $byType,
        array $validIds
    ): void {
        $selectedIds = [];

        $itemInfo = "- 商品名: {$item->item_name}\n- キャッチコピー: " . ($item->catchcopy ?? '') . "\n- 商品説明: " . ($item->item_caption ?? '');

        foreach (['category', 'mood'] as $type) {
            $list = $byType[$type] ?? [];
            if ($list === []) {
                continue;
            }
            $typeLabel = $type === 'category' ? 'カテゴリ（ジャンル）' : 'ムード（雰囲気）';
            $categoriesJson = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $prompt = <<<PROMPT
以下は「{$typeLabel}」の選択肢一覧（JSON）です。
{$categoriesJson}

次の商品に当てはまる id を、上記JSONの id だけから選び、カンマ区切りで数字だけ返してください。複数可。該当が無い場合は 0 だけ返してください（0 は「選択なし」の意味で、id ではありません）。

{$itemInfo}

例: 1,3 または 0
PROMPT;

            $response = trim($ollama->generate($model, $prompt));
            $ids = array_map('intval', array_filter(preg_split('/[\s,]+/', $response)));
            $ids = array_values(array_intersect($ids, $validIds));
            $selectedIds = array_merge($selectedIds, $ids);
        }

        $post->cosmeCategories()->sync(array_values(array_unique($selectedIds)));
    }
}
