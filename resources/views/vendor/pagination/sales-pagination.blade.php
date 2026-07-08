@if ($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <span class="results-text">
                Showing
                <strong>{{ $paginator->firstItem() }}</strong>
                to
                <strong>{{ $paginator->lastItem() }}</strong>
                of
                <strong>{{ $paginator->total() }}</strong>
                results
            </span>
        </div>

        <nav role="navigation" aria-label="Pagination Navigation" class="pagination-nav">
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="page-link disabled" aria-disabled="true">
                        <i class="fas fa-chevron-left"></i>
                        <span class="nav-text">Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                        <span class="nav-text">Previous</span>
                    </a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="page-dots">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="page-link active" aria-current="page">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="page-link">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">
                        <span class="nav-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="page-link disabled" aria-disabled="true">
                        <span class="nav-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </nav>
    </div>
@endif
