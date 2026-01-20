@if ($paginator->hasPages())
<nav class="pagination-container" aria-label="Page navigation">
	<ul class="pagination-list">
		{{-- Previous Page Link --}}
		@if ($paginator->onFirstPage())
		<li class="pagination-item disabled" aria-disabled="true">
			<span class="pagination-link">
				<i class="fas fa-chevron-left"></i>
			</span>
		</li>
		@else
		<li class="pagination-item">
			<a href="{{ $paginator->previousPageUrl() }}" class="pagination-link" rel="prev">
				<i class="fas fa-chevron-left"></i>
			</a>
		</li>
		@endif

		{{-- Pagination Elements --}}
		@foreach ($elements as $element)
			{{-- "Three Dots" Separator --}}
			@if (is_string($element))
			<li class="pagination-item dots" aria-disabled="true">
				<span class="pagination-link">{{ $element }}</span>
			</li>
			@endif

			{{-- Array Of Links --}}
			@if (is_array($element))
				@foreach ($element as $page => $url)
					@if ($page == $paginator->currentPage())
					<li class="pagination-item active" aria-current="page">
						<span class="pagination-link">{{ $page }}</span>
					</li>
					@else
					<li class="pagination-item">
						<a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
					</li>
					@endif
				@endforeach
			@endif
		@endforeach

		{{-- Next Page Link --}}
		@if ($paginator->hasMorePages())
		<li class="pagination-item">
			<a href="{{ $paginator->nextPageUrl() }}" class="pagination-link" rel="next">
				<i class="fas fa-chevron-right"></i>
			</a>
		</li>
		@else
		<li class="pagination-item disabled" aria-disabled="true">
			<span class="pagination-link">
				<i class="fas fa-chevron-right"></i>
			</span>
		</li>
		@endif
	</ul>
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
        --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
        --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem;
        background: var(--body-bg);
    }

    .pagination-list {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        margin: 0;
        list-style: none;
        background: var(--card-bg);
        border-radius: 50px;
        box-shadow: var(--shadow-md);
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-item {
        display: inline-block;
    }

    .pagination-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 8px;
        text-decoration: none;
        color: var(--text-color);
        font-weight: 600;
        font-size: 14px;
        border-radius: 50%;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .pagination-item:not(.active):not(.disabled) .pagination-link:hover {
        background-color: var(--body-bg);
        color: var(--primary-green);
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
    }

    .pagination-item.active .pagination-link {
        background-color: var(--primary-green);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        animation: pulse 2s infinite;
    }

    .pagination-item.disabled .pagination-link {
        color: var(--muted);
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-item.dots .pagination-link {
        cursor: default;
        border: none;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    @media (max-width: 768px) {
        .pagination-list {
            gap: 4px;
            padding: 8px;
            border-radius: 12px;
        }

        .pagination-link {
            min-width: 35px;
            height: 35px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .pagination-item:not(.active):not(:first-child):not(:last-child):not(.dots) {
            display: none;
        }

        .pagination-item.active,
        .pagination-item:first-child,
        .pagination-item:last-child,
        .pagination-item.dots {
            display: inline-block;
        }

        .pagination-list {
            width: 100%;
            border-radius: 8px;
        }
    }
</style>
