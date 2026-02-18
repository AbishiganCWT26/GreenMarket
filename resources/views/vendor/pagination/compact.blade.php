@if ($paginator->hasPages())
<div class="compact-pagination">
<nav>
<ul class="pagination-list">
@if ($paginator->onFirstPage())
<li class="disabled" aria-disabled="true">
<span><i class="fa-solid fa-chevron-left"></i></span>
</li>
@else
<li>
<a href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-chevron-left"></i></a>
</li>
@endif

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

@if ($paginator->hasMorePages())
<li>
<a href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fa-solid fa-chevron-right"></i></a>
</li>
@else
<li class="disabled" aria-disabled="true">
<span><i class="fa-solid fa-chevron-right"></i></span>
</li>
@endif
</ul>
</nav>
<div class="pagination-info">
{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}
</div>
</div>
@endif