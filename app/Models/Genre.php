<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    protected $table = 'genres';

    /** 主キー = 楽天ジャンルID（自動採番ではない） */
    public $incrementing = false;

    protected $fillable = [
        'id',
        'genre_name',
        'genre_level',
        'english_name',
        'link_genre_id',
        'chopper_flg',
        'lowest_flg',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'genre_id', 'id');
    }
}
