<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchLog extends Model
{
    protected $table = 'search_logs';

    const STATUS_RUNNING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 99;

    protected $fillable = [
        'search_condition_id',
        'frequency',
        'status',
    ];

    public function searchCondition(): BelongsTo
    {
        return $this->belongsTo(SearchCondition::class, 'search_condition_id');
    }
}
