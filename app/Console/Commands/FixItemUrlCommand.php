<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\RakutenIchibaService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixItemUrlCommand extends Command
{
    protected $signature = 'items:fix-item-url
                            {--dry-run : 更新せずに対象件数とサンプルだけ表示する}
                            {--list-skipped : 取得不可の一覧を表示する（実行時または --dry-run 時）}';

    protected $description = '既存 items の item_url を affiliate_url の ?pc= パラメータから再取得して更新する';

    public function handle(RakutenIchibaService $service): int
    {
        $dryRun = $this->option('dry-run');
        $listSkipped = $this->option('list-skipped');

        $query = Item::query()->whereNotNull('affiliate_url')->where('affiliate_url', '!=', '');
        $total = $query->count();
        if ($total === 0) {
            $this->info('affiliate_url が設定されている item がありません。');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("[dry-run] 対象: {$total} 件");
            $sample = $query->take(3)->get();
            foreach ($sample as $item) {
                $newUrl = $service->getItemUrlFromAffiliateUrl($item->affiliate_url);
                $this->line("  id={$item->id} item_code={$item->item_code}");
                $this->line('    現在の item_url: ' . ($item->item_url ?? '(null)'));
                $this->line('    取得する item_url: ' . ($newUrl ?? '(null)'));
            }
            if ($listSkipped) {
                $this->outputSkippedList($query->orderBy('id')->get(), $service);
            }
            return self::SUCCESS;
        }

        $updated = 0;
        $skippedRows = [];
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('id')->chunkById(100, function ($items) use ($service, &$updated, &$skippedRows, $bar) {
            foreach ($items as $item) {
                $newUrl = $service->getItemUrlFromAffiliateUrl($item->affiliate_url);
                if ($newUrl !== null && $newUrl !== $item->item_url) {
                    $item->update(['item_url' => $newUrl]);
                    $updated++;
                } else {
                    if ($newUrl === null) {
                        $skippedRows[] = [
                            'id' => $item->id,
                            'item_code' => $item->item_code,
                            'affiliate_url' => $item->affiliate_url,
                            'item_url' => $item->item_url,
                        ];
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $skippedTotal = $total - $updated;
        $this->info("完了: 更新 {$updated} 件 / スキップ {$skippedTotal} 件 / 対象 {$total} 件");

        if ($listSkipped && count($skippedRows) > 0) {
            $this->outputSkippedListTable($skippedRows);
        }

        return self::SUCCESS;
    }

    /**
     * @param  iterable<Item>  $items
     */
    protected function outputSkippedList(iterable $items, RakutenIchibaService $service): void
    {
        $rows = [];
        foreach ($items as $item) {
            $newUrl = $service->getItemUrlFromAffiliateUrl($item->affiliate_url);
            if ($newUrl !== null) {
                continue;
            }
            $rows[] = [
                $item->id,
                $item->item_code,
                Str::limit($item->affiliate_url ?? '', 60),
                Str::limit($item->item_url ?? '(null)', 50),
            ];
        }
        if ($rows === []) {
            $this->info('取得不可の件はありません。');
            return;
        }
        $this->newLine();
        $this->table(['id', 'item_code', 'affiliate_url', 'item_url'], $rows);
    }

    /**
     * @param  array<int, array{id: int, item_code: string, affiliate_url: ?string, item_url: ?string}>  $skippedRows
     */
    protected function outputSkippedListTable(array $skippedRows): void
    {
        $this->newLine();
        $this->info('--- 取得不可一覧 ---');
        $tableRows = array_map(fn ($r) => [
            $r['id'],
            $r['item_code'],
            Str::limit($r['affiliate_url'] ?? '', 60),
            Str::limit($r['item_url'] ?? '(null)', 50),
        ], $skippedRows);
        $this->table(['id', 'item_code', 'affiliate_url', 'item_url'], $tableRows);
    }
}
