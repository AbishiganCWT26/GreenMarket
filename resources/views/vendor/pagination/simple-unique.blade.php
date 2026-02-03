@if ($paginator->hasPages())
<nav class="pagination-wrapper" aria-label="Page navigation">
    <ul class="pagination-group">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link icon">
                <i class="fas fa-chevron-left"></i>
            </span>
        </li>
        @else
        <li class="page-item">
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link icon" rel="prev">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        <div class="page-numbers">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link dots">{{ $element }}</span>
                </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link icon" rel="next">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link icon">
                <i class="fas fa-chevron-right"></i>
            </span>
        </li>
        @endif
    </ul>
</nav>
@endif

<style>
    :root {
        --pg-font: 'Inter', system-ui, -apple-system, sans-serif;
        --pg-active-bg: #1ea534fe; /* Modern Blue */
        --pg-active-text: #ffffff;
        --pg-hover-bg: #f3f4f6;
        --pg-text: #374151;
        --pg-border: #e5e7eb;
        --pg-bg: #ffffff;
        --pg-size: 36px; /* Compact height */
        --pg-radius: 8px;
    }

    /* Wrapper to center everything */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: 2rem 0;
        font-family: var(--pg-font);
    }

    /* The main container floating bar */
    .pagination-group {
        display: inline-flex;
        align-items: center;
        background: var(--pg-bg);
        border: 1px solid var(--pg-border);
        border-radius: 12px;
        padding: 4px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 
                    0 2px 4px -1px rgba(0, 0, 0, 0.03);
        list-style: none;
        margin: 0;
        gap: 2px;
    }

    /* Grouping numbers allows us to hide them easily on mobile */
    .page-numbers {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    /* Individual Items */
    .page-item {
        display: flex;
    }

    /* The Link Styling */
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: var(--pg-size);
        height: var(--pg-size);
        padding: 0 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--pg-text);
        text-decoration: none;
        border-radius: var(--pg-radius);
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }

    /* Hover Effects */
    .page-item:not(.active):not(.disabled) .page-link:hover {
        background-color: var(--pg-hover-bg);
        color: #000;
        transform: translateY(-1px);
    }

    /* Active State (Current Page) */
    .page-item.active .page-link {
        background-color: var(--pg-active-bg);
        color: var(--pg-active-text);
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
    }

    /* Icons (Arrows) specific styling */
    .page-link.icon {
        font-size: 0.75rem; /* Smaller arrows for compactness */
        color: #6b7280;
    }
    
    .page-link.icon:hover {
        color: var(--pg-active-bg);
    }

    /* Disabled State */
    .page-item.disabled .page-link {
        color: #9ca3af;
        cursor: not-allowed;
        background: transparent;
    }

    /* Three Dots */
    .page-link.dots {
        cursor: default;
        letter-spacing: 2px;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    /* --- RESPONSIVE DESIGN --- */

    @media (max-width: 640px) {
        /* On mobile, hide the number list, keep only arrows and current page if needed */
        .pagination-group {
            padding: 3px;
        }

        /* Smart hiding: Hide numbers that aren't active */
        .page-numbers .page-item:not(.active) {
            display: none;
        }
        
        /* Show dots if you want, or hide them too for ultra-compact */
        .page-numbers .page-item:has(.dots) {
            display: inline-flex;
        }
        
        /* If active is hidden inside numbers, ensure it shows */
        .page-numbers .page-item.active {
            display: flex;
        }

        /* Adjust size for touch targets */
        :root {
            --pg-size: 40px; 
        }
    }
</style>