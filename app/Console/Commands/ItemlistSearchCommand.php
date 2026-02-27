<?php

namespace App\Console\Commands;

use App\Models\SearchCondition;
use App\Models\SearchLog;
use App\Models\Site;
use App\Services\RakutenIchibaService;
use Illuminate\Console\Command;

class ItemlistSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itemlist:search
                            {site_id : サイトID（sites.id）}
                            {frequency : 実行対象の頻度（once|daily|weekly|monthly）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定サイト・頻度の検索条件で楽天APIを実行し、商品・ショップ・item_sites を保存する';

    public function handle(RakutenIchibaService $service): int
    {
        $siteId = (int) $this->argument('site_id');
        $frequency = $this->argument('frequency');

        $validFrequencies = ['once', 'daily', 'weekly', 'monthly'];
        if (! in_array($frequency, $validFrequencies, true)) {
            $this->error("frequency は " . implode('|', $validFrequencies) . " のいずれかを指定してください。");
            return self::FAILURE;
        }

        $site = Site::find($siteId);
        if (! $site) {
            $this->error("サイト ID={$siteId} は存在しません。");
            return self::FAILURE;
        }

        $query = SearchCondition::query()
            ->where('site_id', $siteId)
            ->where('is_active', 1)
            ->where('frequency', $frequency);

        if ($frequency === 'once') {
            $query->whereDoesntHave('searchLogs', function ($q) {
                $q->where('status', SearchLog::STATUS_SUCCESS);
            });
        }

        $conditions = $query->get();
        if ($conditions->isEmpty()) {
            $this->info("対象の検索条件がありません。（site_id={$siteId}, frequency={$frequency}）");
            return self::SUCCESS;
        }

        $this->info("検索条件 {$conditions->count()} 件を実行します。");

        foreach ($conditions as $condition) {
            $this->runOne($service, $condition, $siteId);
        }

        return self::SUCCESS;
    }

    protected function runOne(RakutenIchibaService $service, SearchCondition $condition, int $siteId): void
    {
        $log = SearchLog::create([
            'search_condition_id' => $condition->id,
            'frequency' => $condition->frequency,
            'status' => SearchLog::STATUS_RUNNING,
        ]);

        try {
            $result = $service->searchAndSave($condition, $siteId);

            $log->update(['status' => SearchLog::STATUS_SUCCESS]);
            $condition->update(['total_hits' => $result['count']]);

            $this->line("  [OK] search_condition_id={$condition->id} count={$result['count']} saved_items={$result['saved_items']} saved_shops={$result['saved_shops']}");
        } catch (\Throwable $e) {
            $log->update(['status' => SearchLog::STATUS_FAILED]);
            $this->error("  [FAIL] search_condition_id={$condition->id} " . $e->getMessage());
            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }
        }
    }
}
