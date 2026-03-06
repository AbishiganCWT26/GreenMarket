@if ($paginator->hasPages())
<div class="pagination-wrapper-compact">
	@if ($paginator->onFirstPage())
	<span class="pagination-btn-compact disabled-compact">
		<i class="fa-solid fa-chevron-left"></i>
	</span>
	@else
	<a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn-compact" rel="prev">
		<i class="fa-solid fa-chevron-left"></i>
	</a>
	@endif

	@foreach ($elements as $element)
		@if (is_string($element))
		<span class="pagination-dots-compact">{{ $element }}</span>
		@endif

		@if (is_array($element))
			@foreach ($element as $page => $url)
				@if ($page == $paginator->currentPage())
				<span class="pagination-number-compact active-compact">{{ $page }}</span>
				@else
				<a href="{{ $url }}" class="pagination-number-compact">{{ $page }}</a>
				@endif
			@endforeach
		@endif
	@endforeach

	@if ($paginator->hasMorePages())
	<a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn-compact" rel="next">
		<i class="fa-solid fa-chevron-right"></i>
	</a>
	@else
	<span class="pagination-btn-compact disabled-compact">
		<i class="fa-solid fa-chevron-right"></i>
	</span>
	@endif
</div>
@endif