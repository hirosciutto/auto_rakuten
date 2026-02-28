<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'item_id',
        'title',
        'body',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function cosmeCategories(): BelongsToMany
    {
        return $this->belongsToMany(CosmeCategory::class, 'cosme_category_posts', 'post_id', 'cosme_category_id')
            ->withTimestamps();
    }

    /**
     * 指定サイトに紐づく item を持つ post にスコープ
     */
    public function scopeForSite($query, Site $site)
    {
        return $query->whereIn('item_id', $site->items()->pluck('items.id'));
    }
}
