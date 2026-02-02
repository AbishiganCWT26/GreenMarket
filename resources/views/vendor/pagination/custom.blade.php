@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: inline-block !important;">
        <div class="pagination" style="display: flex !important; align-items: center !important; gap: 2px !important; font-size: 12px !important; line-height: 1 !important; margin: 0 !important; padding: 0 !important;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn disabled" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #f3f4f6 !important; color: #9ca3af !important; text-decoration: none !important; cursor: not-allowed !important; user-select: none !important;">
                    <i class="fas fa-chevron-left" style="font-size: 10px !important;"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #ffffff !important; border: 1px solid #d1d5db !important; color: #374151 !important; text-decoration: none !important; transition: all 0.2s !important; cursor: pointer !important;">
                    <i class="fas fa-chevron-left" style="font-size: 10px !important;"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-dots" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; color: #6b7280 !important; user-select: none !important;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-btn active" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #068039ff !important; color: #ffffff !important; font-weight: 600 !important; text-decoration: none !important; user-select: none !important;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #ffffff !important; border: 1px solid #d1d5db !important; color: #374151 !important; text-decoration: none !important; transition: all 0.2s !important; cursor: pointer !important; font-size: 11px !important;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #ffffff !important; border: 1px solid #d1d5db !important; color: #374151 !important; text-decoration: none !important; transition: all 0.2s !important; cursor: pointer !important;">
                    <i class="fas fa-chevron-right" style="font-size: 10px !important;"></i>
                </a>
            @else
                <span class="page-btn disabled" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 3px !important; background-color: #f3f4f6 !important; color: #9ca3af !important; text-decoration: none !important; cursor: not-allowed !important; user-select: none !important;">
                    <i class="fas fa-chevron-right" style="font-size: 10px !important;"></i>
                </span>
            @endif
        </div>
    </nav>
@endif

