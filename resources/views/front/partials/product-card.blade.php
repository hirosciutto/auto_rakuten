@php
    $item = $post->item;
    $imageUrl = $item ? ($item->medium_image_urls[0] ?? $item->small_image_urls[0] ?? null) : null;
    $price = $item ? (int) $item->item_price : 0;
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
    $url = $item ? ($item->affiliate_url ?? $item->item_url ?? '#') : '#';
@endphp
<article class="c-card" data-post-id="{{ $post->id }}">
    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="c-card__image-wrap">
        @if($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $post->title }}"
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
        @if($item && $item->shop)
            <p class="c-card__brand">{{ $item->shop->shop_name ?? '' }}</p>
        @endif
        <h3 class="c-card__name">{{ $post->title }}</h3>
        @if($post->body)
            <p class="c-card__intro">{{ Str::limit($post->body, 80) }}</p>
        @endif
        <p class="c-card__price">¥{{ number_format($price) }}</p>
        @if($item && $item->review_count && $item->review_average)
            <div class="c-card__rating" aria-label="評価 {{ $item->review_average }}（{{ $item->review_count }}件のレビュー）">
                @for($i = 1; $i <= 5; $i++)
                    <span aria-hidden="true">{{ $i <= round($item->review_average) ? '★' : '☆' }}</span>
                @endfor
                <span class="c-small" style="margin-left:4px;color:var(--text-muted);">({{ $item->review_count }})</span>
            </div>
        @endif
        <div class="c-card__actions">
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="c-card__cta">商品を見る</a>
            <button type="button" class="c-card__favorite" data-post-id="{{ $post->id }}" aria-pressed="false" aria-label="お気に入りに追加">
                ♡
            </button>
        </div>
    </div>
</article>
