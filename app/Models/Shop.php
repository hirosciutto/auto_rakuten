<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $table = 'shops';

    protected $fillable = [
        'shop_code',
        'shop_name',
        'shop_url',
        'shop_affiliate_url',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'shop_id');
    }
}
