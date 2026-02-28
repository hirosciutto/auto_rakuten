@extends('cosmetica.layout')

@section('title', 'COSMETICA｜コスメ・美容のトレンド')

@section('content')
    <div class="c-container">
        {{-- カテゴリグリッド --}}
        <section class="c-section" aria-labelledby="category-heading">
            <h2 id="category-heading" class="c-h2 c-section__title">カテゴリ</h2>
            <div class="c-grid" style="grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));">
                @foreach($categories as $cat)
                    <a href="{{ url('/?cosme_category_id=' . $cat->id) }}" class="c-category-card">
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- トレンドカルーセル --}}
        @if($trending->isNotEmpty())
            <section class="c-section" aria-labelledby="trending-heading">
                <h2 id="trending-heading" class="c-h2 c-section__title">いま人気</h2>
                <div class="c-carousel" role="list">
                    @foreach($trending as $item)
                        <div class="c-carousel__item" role="listitem">
                            @include('cosmetica.partials.product-card', ['item' => $item])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- トップランキング --}}
        @if($ranking->isNotEmpty())
            <section class="c-section" aria-labelledby="ranking-heading">
                <h2 id="ranking-heading" class="c-h2 c-section__title">高評価ランキング</h2>
                <div class="c-grid">
                    @foreach($ranking->take(5) as $item)
                        @include('cosmetica.partials.product-card', ['item' => $item])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ムードフィルター + 商品グリッド --}}
        <section class="c-section" aria-labelledby="mood-heading">
            <h2 id="mood-heading" class="c-h2 c-section__title">ムードで選ぶ</h2>
            <div class="c-pills" role="tablist" aria-label="ムードフィルター">
                <button type="button" class="c-pill is-active" role="tab" aria-selected="true" data-cosme-category-id="">すべて</button>
                @foreach($moods as $mood)
                    <button type="button" class="c-pill" role="tab" aria-selected="false" data-cosme-category-id="{{ $mood->id }}">{{ $mood->name }}</button>
                @endforeach
            </div>

            <div id="product-grid-wrap" class="c-section" style="padding-top: 24px;">
                <h2 id="product-grid-heading" class="visually-hidden">商品一覧</h2>
                <div id="product-grid" class="c-grid" role="list">
                    @foreach($items as $item)
                        <div role="listitem">@include('cosmetica.partials.product-card', ['item' => $item])</div>
                    @endforeach
                </div>
                <div id="product-grid-skeleton" class="c-grid" style="display: none;" aria-hidden="true">
                    @for($i = 0; $i < 10; $i++)
                        <div class="c-card">
                            <div class="c-skeleton c-skeleton--image"></div>
                            <div class="c-card__body">
                                <div class="c-skeleton c-skeleton--line short"></div>
                                <div class="c-skeleton c-skeleton--line" style="margin-top:8px;"></div>
                                <div class="c-skeleton c-skeleton--line" style="margin-top:8px; width:60%;"></div>
                            </div>
                        </div>
                    @endfor
                </div>
                <p id="product-grid-more" style="text-align:center; margin-top:24px; display: {{ $items->hasMorePages() ? 'block' : 'none' }};">
                    <button type="button" id="load-more-btn" class="c-card__cta">もっと見る</button>
                </p>
                <p id="product-grid-end" class="c-small" style="text-align:center; color: var(--text-muted); margin-top: 24px; display: {{ !$items->hasMorePages() && $items->total() > 0 ? 'block' : 'none' }};">以上です</p>
            </div>
        </section>
    </div>

    {{-- JSON-LD --}}
    @php
        $jsonLdItems = $items->take(10);
    @endphp
    @if($jsonLdItems->isNotEmpty())
        <script type="application/ld+json">
        [
            @foreach($jsonLdItems as $item)
            {
                "@context": "https://schema.org",
                "@type": "Product",
                "name": {{ json_encode($item->item_name) }},
                "image": {{ json_encode($item->medium_image_urls[0] ?? $item->small_image_urls[0] ?? null) }},
                "offers": {
                    "@type": "Offer",
                    "price": {{ (int) $item->item_price }},
                    "priceCurrency": "JPY"
                }
                @if($item->review_count && $item->review_average)
                ,"aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "{{ $item->review_average }}",
                    "reviewCount": "{{ $item->review_count }}",
                    "bestRating": "5"
                }
                @endif
            }@if(!$loop->last),@endif
            @endforeach
        ]
        </script>
    @endif

    <script>
        (function() {
            var grid = document.getElementById('product-grid');
            var skeleton = document.getElementById('product-grid-skeleton');
            var moreWrap = document.getElementById('product-grid-more');
            var endWrap = document.getElementById('product-grid-end');
            var loadMoreBtn = document.getElementById('load-more-btn');
            var pills = document.querySelectorAll('.c-pill[data-cosme-category-id]');
            var currentPage = {{ $items->currentPage() }};
            var lastPage = {{ $items->lastPage() }};
            var cosmeCategoryId = '{{ request("cosme_category_id", "") }}';

            function setPillActive(id) {
                pills.forEach(function(p) {
                    p.classList.toggle('is-active', p.getAttribute('data-cosme-category-id') === String(id));
                    p.setAttribute('aria-selected', p.classList.contains('is-active'));
                });
            }

            function updateUrl(page, moodId) {
                var params = new URLSearchParams(window.location.search);
                if (moodId) params.set('cosme_category_id', moodId); else params.delete('cosme_category_id');
                if (page > 1) params.set('page', page); else params.delete('page');
                var url = (params.toString() ? '?' + params.toString() : window.location.pathname) + window.location.hash;
                window.history.replaceState({ page: page, cosme_category_id: moodId }, '', url);
            }

            function renderCards(items) {
                return items.map(function(item) {
                    var urls = item.medium_image_urls || item.small_image_urls;
                    var img = urls && urls[0] ? urls[0] : '';
                    var price = parseInt(item.item_price, 10) || 0;
                    var tier = price < 1500 ? 'low' : (price < 4000 ? 'mid' : 'high');
                    var tierLabel = price < 1500 ? 'お手頃' : (price < 4000 ? 'スタンダード' : 'プレミアム');
                    var shopName = item.shop ? (item.shop.name || '') : '';
                    var rating = item.review_average ? Math.round(parseFloat(item.review_average)) : 0;
                    var reviewCount = item.review_count || 0;
                    var stars = '';
                    for (var i = 1; i <= 5; i++) stars += (i <= rating ? '★' : '☆');
                    var url = item.affiliate_url || item.item_url || '#';
                    return '<div role="listitem"><article class="c-card" data-item-id="' + item.id + '">' +
                        '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="c-card__image-wrap">' +
                        (img ? '<img src="' + img + '" alt="" class="c-card__image" loading="lazy" width="256" height="256" decoding="async">' : '<div class="c-skeleton c-skeleton--image"></div>') +
                        '<span class="c-card__badge c-card__badge--' + tier + '">' + tierLabel + '</span></a>' +
                        '<div class="c-card__body">' +
                        (shopName ? '<p class="c-card__brand">' + escapeHtml(shopName) + '</p>' : '') +
                        '<h3 class="c-card__name">' + escapeHtml(item.item_name || '') + '</h3>' +
                        '<p class="c-card__price">¥' + (price ? price.toLocaleString() : '') + '</p>' +
                        (reviewCount ? '<div class="c-card__rating" aria-label="評価 ' + item.review_average + '">' + stars + ' <span class="c-small" style="margin-left:4px;color:var(--text-muted);">(' + reviewCount + ')</span></div>' : '') +
                        '<div class="c-card__actions"><a href="' + url + '" target="_blank" rel="noopener noreferrer" class="c-card__cta">商品を見る</a>' +
                        '<button type="button" class="c-card__favorite" data-item-id="' + item.id + '" aria-pressed="false" aria-label="お気に入りに追加">♡</button></div></div></article></div>';
                }).join('');
            }

            function escapeHtml(s) {
                var div = document.createElement('div');
                div.textContent = s;
                return div.innerHTML;
            }

            function loadItems(page, moodId, append) {
                var params = 'page=' + page;
                if (moodId) params += '&cosme_category_id=' + encodeURIComponent(moodId);
                skeleton.style.display = grid.style.display === 'none' ? 'grid' : 'none';
                if (!append) grid.innerHTML = '';
                grid.style.display = 'none';
                fetch('{{ url("/cosmetica/items") }}?' + params, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        skeleton.style.display = 'none';
                        grid.style.display = 'grid';
                        if (res.data && res.data.length) {
                            grid.insertAdjacentHTML(append ? 'beforeend' : 'afterbegin', renderCards(res.data));
                        }
                        var meta = res.meta || {};
                        currentPage = meta.current_page || 1;
                        lastPage = meta.last_page || 1;
                        moreWrap.style.display = currentPage < lastPage ? 'block' : 'none';
                        endWrap.style.display = currentPage >= lastPage && (meta.total || 0) > 0 ? 'block' : 'none';
                        updateUrl(currentPage, moodId || null);
                    })
                    .catch(function() {
                        skeleton.style.display = 'none';
                        grid.style.display = 'grid';
                    });
            }

            pills.forEach(function(pill) {
                pill.addEventListener('click', function() {
                    var id = this.getAttribute('data-cosme-category-id');
                    cosmeCategoryId = id;
                    setPillActive(id);
                    loadItems(1, id || undefined, false);
                });
            });

            if (cosmeCategoryId) setPillActive(cosmeCategoryId);

            loadMoreBtn.addEventListener('click', function() {
                var next = currentPage + 1;
                loadItems(next, cosmeCategoryId || undefined, true);
            });

            // お気に入りトグル（デモ用）
            document.addEventListener('click', function(e) {
                if (e.target.closest('.c-card__favorite')) {
                    var btn = e.target.closest('.c-card__favorite');
                    btn.classList.toggle('is-active');
                    btn.setAttribute('aria-pressed', btn.classList.contains('is-active'));
                    btn.textContent = btn.classList.contains('is-active') ? '♥' : '♡';
                }
            });
        })();
    </script>
@endsection
