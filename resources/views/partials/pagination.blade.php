@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn page-btn-disabled" aria-disabled="true" aria-label="Previous page">
                <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                <span class="btn-text">Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn" aria-label="Previous page">
                <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                <span class="btn-text">Previous</span>
            </a>
        @endif

        {{-- Mobile Page Indicator --}}
        <div class="mobile-page-indicator">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </div>

        {{-- Desktop Page Numbers --}}
        <div class="desktop-page-numbers">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-ellipsis" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-number page-number-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-number" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn" aria-label="Next page">
                <span class="btn-text">Next</span>
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </a>
        @else
            <span class="page-btn page-btn-disabled" aria-disabled="true" aria-label="Next page">
                <span class="btn-text">Next</span>
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </span>
        @endif
    </nav>
@endif
