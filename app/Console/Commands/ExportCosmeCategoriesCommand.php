<?php

namespace App\Console\Commands;

use App\Models\CosmeCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportCosmeCategoriesCommand extends Command
{
    protected $signature = 'cosme-categories:export';

    protected $description = 'cosme_categories の id / type / name を JSON で storage/app/dev/cosmetic_categories.json に保存する（AI用）';

    public function handle(): int
    {
        $path = storage_path('app/dev/cosmetic_categories.json');

        File::ensureDirectoryExists(dirname($path));

        $rows = CosmeCategory::query()
            ->orderBy('type')
            ->orderBy('id')
            ->get(['id', 'type', 'name'])
            ->map(fn ($row) => [
                'id' => $row->id,
                'type' => $row->type,
                'name' => $row->name,
            ])
            ->values()
            ->toArray();

        File::put($path, json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info("Saved " . count($rows) . " rows to {$path}");

        return self::SUCCESS;
    }
}
