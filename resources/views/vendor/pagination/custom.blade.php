@if ($paginator->hasPages())
<nav class="pager" aria-label="Pagination">
    <ul>
        @if ($paginator->onFirstPage())
            <li><span class="pager__btn is-disabled" aria-disabled="true"><i class="bi bi-chevron-left" aria-hidden="true"></i><span class="pager__label">Précédent</span></span></li>
        @else
            <li><a class="pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left" aria-hidden="true"></i><span class="pager__label">Précédent</span></a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="pager__gap">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="pager__num is-current" aria-current="page">{{ $page }}</span></li>
                    @else
                        <li><a class="pager__num" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li><a class="pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next"><span class="pager__label">Suivant</span><i class="bi bi-chevron-right" aria-hidden="true"></i></a></li>
        @else
            <li><span class="pager__btn is-disabled" aria-disabled="true"><span class="pager__label">Suivant</span><i class="bi bi-chevron-right" aria-hidden="true"></i></span></li>
        @endif
    </ul>
</nav>
@endif
