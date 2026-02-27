<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $table = 'sites';

    protected $fillable = [
        'name',
        'code',
        'access_code',
    ];

    public function searchConditions(): HasMany
    {
        return $this->hasMany(SearchCondition::class, 'site_id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_sites', 'site_id', 'item_id')
            ->withTimestamps();
    }
}
