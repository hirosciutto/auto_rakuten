<?php

namespace App\Console\Commands;

use App\Models\SearchCondition;
use App\Models\SearchLog;
use App\Models\Site;
use App\Services\RakutenIchibaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ItemlistSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itemlist:search
                            {frequency : 実行対象の頻度（once|daily|weekly|monthly）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '検索条件で楽天APIを実行し、商品・ショップ・item_sites を保存する';

    public function handle(RakutenIchibaService $service): int
    {
        $frequency = $this->argument('frequency');

        $validFrequencies = ['once', 'daily', 'weekly', 'monthly'];
        if (! in_array($frequency, $validFrequencies, true)) {
            $this->error("frequency は " . implode('|', $validFrequencies) . " のいずれかを指定してください。");
            return self::FAILURE;
        }

        $query = SearchCondition::query()
            ->where('is_active', 1)
            ->where('frequency', $frequency);

        if ($frequency === 'once') {
            $query->whereDoesntHave('searchLogs', function ($q) {
                $q->where('status', SearchLog::STATUS_SUCCESS);
            });
        }

        $conditions = $query->get();
        if ($conditions->isEmpty()) {
            Log::info('ItemlistSearchCommand: no conditions', ['frequency' => $frequency]);
            $this->info("対象の検索条件がありません。（frequency={$frequency}）");
            return self::SUCCESS;
        }

        Log::info('ItemlistSearchCommand: start', [
            'frequency' => $frequency,
            'condition_count' => $conditions->count(),
            'condition_ids' => $conditions->pluck('id')->toArray(),
        ]);
        $this->info("検索条件 {$conditions->count()} 件を実行します。");

        foreach ($conditions as $condition) {
            $this->runOne($service, $condition, (int) $condition->site_id);
        }

        return self::SUCCESS;
    }

    protected function runOne(RakutenIchibaService $service, SearchCondition $condition, int $siteId): void
    {
        Log::info('ItemlistSearchCommand.runOne start', [
            'search_condition_id' => $condition->id,
            'site_id' => $siteId,
        ]);

        $log = SearchLog::create([
            'search_condition_id' => $condition->id,
            'frequency' => $condition->frequency,
            'status' => SearchLog::STATUS_RUNNING,
        ]);

        try {
            $result = $service->searchAndSave($condition, $siteId);

            $log->update(['status' => SearchLog::STATUS_SUCCESS]);

            Log::info('ItemlistSearchCommand.runOne success', [
                'search_condition_id' => $condition->id,
                'search_log_id' => $log->id,
                'fetched' => $result['count'],
                'saved_items' => $result['saved_items'],
                'saved_shops' => $result['saved_shops'],
            ]);
            $this->line("  [OK] search_condition_id={$condition->id} fetched={$result['count']} saved_items={$result['saved_items']} saved_shops={$result['saved_shops']}");
        } catch (\Throwable $e) {
            $log->update([
                'status' => SearchLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
            Log::warning('ItemlistSearchCommand.runOne failed', [
                'search_condition_id' => $condition->id,
                'search_log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);
            $this->error("  [FAIL] search_condition_id={$condition->id} " . $e->getMessage());
            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }
        }
    }
}
