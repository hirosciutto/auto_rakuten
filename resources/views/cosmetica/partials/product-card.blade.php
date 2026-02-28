@php
    $imageUrl = $item->medium_image_urls[0] ?? $item->small_image_urls[0] ?? null;
    $price = (int) $item->item_price;
    if ($price < 1500) {
        $tier = 'low';
        $tierLabel = 'お手頃';
    } elseif ($price < 4000) {
        $tier = 'mid';
        $tierLabel = 'スタンダード';
    } else {
        $tier = 'high';
        $tierLabel = 'プレミアム';
    }
@endphp
<article class="c-card" data-item-id="{{ $item->id }}">
    <a href="{{ $item->affiliate_url ?? $item->item_url ?? '#' }}" target="_blank" rel="noopener noreferrer" class="c-card__image-wrap">
        @if($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $item->item_name }}"
                class="c-card__image"
                loading="lazy"
                width="256"
                height="256"
                decoding="async"
            />
        @else
            <div class="c-skeleton c-skeleton--image" aria-hidden="true"></div>
        @endif
        <span class="c-card__badge c-card__badge--{{ $tier }}">{{ $tierLabel }}</span>
    </a>
    <div class="c-card__body">
        @if($item->shop)
            <p class="c-card__brand">{{ $item->shop->name ?? '' }}</p>
        @endif
        <h3 class="c-card__name">{{ $item->item_name }}</h3>
        <p class="c-card__price">¥{{ number_format($price) }}</p>
        @if($item->review_count && $item->review_average)
            <div class="c-card__rating" aria-label="評価 {{ $item->review_average }}（{{ $item->review_count }}件のレビュー）">
                @for($i = 1; $i <= 5; $i++)
                    <span aria-hidden="true">{{ $i <= round($item->review_average) ? '★' : '☆' }}</span>
                @endfor
                <span class="c-small" style="margin-left:4px;color:var(--text-muted);">({{ $item->review_count }})</span>
            </div>
        @endif
        <div class="c-card__actions">
            <a href="{{ $item->affiliate_url ?? $item->item_url ?? '#' }}" target="_blank" rel="noopener noreferrer" class="c-card__cta">商品を見る</a>
            <button type="button" class="c-card__favorite" data-item-id="{{ $item->id }}" aria-pressed="false" aria-label="お気に入りに追加">
                ♡
            </button>
        </div>
    </div>
</article>
