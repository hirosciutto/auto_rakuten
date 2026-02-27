<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchCondition extends Model
{
    protected $table = 'search_conditions';

    protected $fillable = [
        'site_id',
        'total_hits',
        'frequency',
        'keyword',
        'or_flag',
        'ng_keyword',
        'shop_code',
        'item_code',
        'genre_id',
        'tag_id',
        'page',
        'min_price',
        'max_price',
        'availability',
        'purchase_type',
        'overwrite',
        'is_active',
    ];

    protected $casts = [
        'overwrite' => 'integer',
        'is_active' => 'integer',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function searchLogs(): HasMany
    {
        return $this->hasMany(SearchLog::class, 'search_condition_id');
    }
}
