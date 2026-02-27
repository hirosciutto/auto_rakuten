@if ($paginator->hasPages())
<ul class="pagination pull-right">
    {{-- 前へ --}}
    @if ($paginator->onFirstPage())
        <li class="disabled" aria-disabled="true"><span>{{ __('pagination.previous') }}</span></li>
    @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('pagination.previous') }}</a></li>
    @endif

    {{-- ページ番号 --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- 次へ --}}
    @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('pagination.next') }}</a></li>
    @else
        <li class="disabled" aria-disabled="true"><span>{{ __('pagination.next') }}</span></li>
    @endif
</ul>
@endif
