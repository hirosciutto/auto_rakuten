<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'shop_id',
        'item_code',
        'item_name',
        'catchcopy',
        'item_price',
        'item_caption',
        'item_url',
        'affiliate_url',
        'item_price_base_field',
        'item_price_max1',
        'item_price_max2',
        'item_price_max3',
        'item_price_min1',
        'item_price_min2',
        'item_price_min3',
        'image_flag',
        'small_image_urls',
        'medium_image_urls',
        'availability',
        'tax_flag',
        'postage_flag',
        'credit_card_flag',
        'shop_of_the_year_flag',
        'ship_overseas_flag',
        'ship_overseas_area',
        'asuraku_flag',
        'asuraku_closing_time',
        'asuraku_area',
        'affiliate_rate',
        'start_time',
        'end_time',
        'review_count',
        'review_average',
        'point_rate',
        'point_rate_start_time',
        'point_rate_end_time',
        'gift_flag',
        'genre_id',
        'tag_ids',
    ];

    protected $casts = [
        'small_image_urls' => 'array',
        'medium_image_urls' => 'array',
        'tag_ids' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'item_sites', 'item_id', 'site_id')
            ->withTimestamps();
    }

    public function cosmeCategories(): BelongsToMany
    {
        return $this->belongsToMany(CosmeCategory::class, 'item_cosme_categories', 'item_id', 'cosme_category_id')
            ->withTimestamps();
    }
}
