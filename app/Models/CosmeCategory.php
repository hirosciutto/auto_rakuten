<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CosmeCategory extends Model
{
    protected $table = 'cosme_categories';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'sort_order',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_cosme_categories', 'cosme_category_id', 'item_id')
            ->withTimestamps();
    }

    public function scopeCategoryType($query)
    {
        return $query->where('type', 'category');
    }

    public function scopeMoodType($query)
    {
        return $query->where('type', 'mood');
    }
}
