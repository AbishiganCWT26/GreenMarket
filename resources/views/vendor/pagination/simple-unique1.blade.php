@if ($paginator->hasPages())
<nav class="pagination-container" aria-label="Page navigation">
    <div class="pagination-wrapper">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <button class="pagination-btn prev-btn disabled" aria-disabled="true">
            <svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            <span>Previous</span>
        </button>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn prev-btn" rel="prev">
            <svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            <span>Previous</span>
        </a>
        @endif

        <div class="pagination-numbers">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                <span class="pagination-dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                        <span class="pagination-number active" aria-current="page">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn next-btn" rel="next">
            <span>Next</span>
            <svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </a>
        @else
        <button class="pagination-btn next-btn disabled" aria-disabled="true">
            <span>Next</span>
            <svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        @endif
    </div>
</nav>
@endif

<style>
    :root {
        --primary-green: #10B981;
        --dark-green: #059669;
        --body-bg: #f6f8fa;
        --card-bg: #ffffff;
        --text-color: #0f1724;
        --muted: #6b7280;
        --border-color: #e5e7eb;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--card-bg);
        padding: 12px 20px;
        border-radius: 16px;
        box-shadow: 
            0 4px 20px rgba(15, 23, 36, 0.05),
            0 1px 3px rgba(15, 23, 36, 0.04);
        border: 1px solid var(--border-color);
        flex-wrap: wrap;
        justify-content: center;
        max-width: 100%;
    }

    .pagination-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--body-bg);
        border: 2px solid transparent;
        border-radius: 12px;
        color: var(--text-color);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .pagination-btn:not(.disabled):hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
    }

    .pagination-btn:not(.disabled):hover .pagination-icon {
        stroke: white;
        transform: translateX(-2px);
    }

    .next-btn:not(.disabled):hover .pagination-icon {
        transform: translateX(2px);
    }

    .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: var(--body-bg);
        color: var(--muted);
    }

    .pagination-icon {
        width: 18px;
        height: 18px;
        stroke: var(--text-color);
        transition: var(--transition);
    }

    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-number {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 4px;
        background: var(--body-bg);
        border: 2px solid transparent;
        border-radius: 10px;
        color: var(--text-color);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .pagination-number:hover:not(.active) {
        background: var(--primary-green);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .pagination-number.active {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        animation: gentlePulse 3s ease-in-out infinite;
        border-color: transparent;
    }

    .pagination-dots {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        color: var(--muted);
        font-weight: 600;
        user-select: none;
    }

    /* Ripple effect */
    .pagination-number:after,
    .pagination-btn:after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: radial-gradient(circle at center, rgba(255,255,255,0.3) 0%, transparent 80%);
        transform: scale(0);
        transition: transform 0.5s ease-out;
    }

    .pagination-number:active:after,
    .pagination-btn:active:after {
        transform: scale(2);
        opacity: 0;
    }

    @keyframes gentlePulse {
        0%, 100% {
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
            transform: scale(1.02);
        }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .pagination-wrapper {
            padding: 10px 16px;
            gap: 12px;
            border-radius: 12px;
        }

        .pagination-btn {
            padding: 8px 16px;
            font-size: 13px;
        }

        .pagination-number {
            min-width: 36px;
            height: 36px;
            font-size: 13px;
        }

        .pagination-dots {
            width: 36px;
            height: 36px;
        }

        .pagination-btn span {
            display: none;
        }

        .pagination-btn .pagination-icon {
            width: 16px;
            height: 16px;
        }
    }

    @media (max-width: 480px) {
        .pagination-wrapper {
            flex-direction: column;
            gap: 10px;
            padding: 16px;
        }

        .pagination-numbers {
            order: 2;
            width: 100%;
            justify-content: center;
        }

        .pagination-btn {
            order: 1;
            width: 100%;
            justify-content: center;
        }

        .prev-btn {
            order: 1;
        }

        .next-btn {
            order: 3;
        }

        .pagination-number:not(.active):not(:first-child):not(:last-child):not(.pagination-dots) {
            display: none;
        }

        .pagination-dots,
        .pagination-number.active,
        .pagination-number:first-child,
        .pagination-number:last-child {
            display: flex;
        }
    }

    @media (max-width: 360px) {
        .pagination-wrapper {
            padding: 12px;
        }

        .pagination-number {
            min-width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .pagination-dots {
            width: 32px;
            height: 32px;
        }
    }
</style>