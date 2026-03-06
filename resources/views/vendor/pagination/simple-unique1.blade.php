@if ($paginator->hasPages())
<nav class="pager">
	<div class="pager-box">
		@if ($paginator->onFirstPage())
		<span class="pager-btn disabled">
			<i class="pager-icon">❮</i>
		</span>
		@else
		<a href="{{ $paginator->previousPageUrl() }}" class="pager-btn">
			<i class="pager-icon">❮</i>
		</a>
		@endif

		<div class="pager-numbers">
			@foreach ($elements as $element)
				@if (is_string($element))
				<span class="pager-dots">•••</span>
				@endif

				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
						<span class="pager-num active">{{ $page }}</span>
						@else
						<a href="{{ $url }}" class="pager-num">{{ $page }}</a>
						@endif
					@endforeach
				@endif
			@endforeach
		</div>

		@if ($paginator->hasMorePages())
		<a href="{{ $paginator->nextPageUrl() }}" class="pager-btn">
			<i class="pager-icon">❯</i>
		</a>
		@else
		<span class="pager-btn disabled">
			<i class="pager-icon">❯</i>
		</span>
		@endif
	</div>
</nav>
@endif

<style>
.pager{
	width:100%;
	display:flex;
	justify-content:center;
	align-items:center;
	margin:10px 0;
	animation:fadeIn .4s ease
}

@keyframes fadeIn{
	from{opacity:0;transform:translateY(8px)}
	to{opacity:1;transform:translateY(0)}
}

.pager-box{
	display:flex;
	align-items:center;
	gap:6px;
	background:#ffffff;
	padding:6px 10px;
	border-radius:30px;
	border:1px solid #e6e9ef;
	box-shadow:0 2px 8px rgba(0,0,0,.05);
	transition:.3s
}

.pager-box:hover{
	box-shadow:0 6px 14px rgba(0,0,0,.08)
}

.pager-btn{
	width:32px;
	height:32px;
	display:flex;
	align-items:center;
	justify-content:center;
	border-radius:50%;
	background:#f7f8fa;
	color:#444;
	text-decoration:none;
	font-size:14px;
	transition:.25s
}

.pager-btn:hover{
	background:#10b981;
	color:#fff;
	transform:translateY(-2px) scale(1.05);
	box-shadow:0 4px 10px rgba(16,185,129,.3)
}

.pager-btn.disabled{
	opacity:.35;
	pointer-events:none
}

.pager-icon{
	font-style:normal;
	font-weight:600
}

.pager-numbers{
	display:flex;
	align-items:center;
	gap:4px
}

.pager-num{
	min-width:30px;
	height:30px;
	display:flex;
	align-items:center;
	justify-content:center;
	border-radius:20px;
	font-size:13px;
	color:#444;
	text-decoration:none;
	background:#f4f6f9;
	transition:.25s
}

.pager-num:hover{
	background:#3b82f6;
	color:#fff;
	transform:translateY(-2px);
	box-shadow:0 4px 10px rgba(59,130,246,.25)
}

.pager-num.active{
	background:#10b981;
	color:#fff;
	font-weight:600;
	animation:pulse 2s infinite
}

@keyframes pulse{
	0%{box-shadow:0 0 0 0 rgba(16,185,129,.4)}
	70%{box-shadow:0 0 0 10px rgba(16,185,129,0)}
	100%{box-shadow:0 0 0 0 rgba(16,185,129,0)}
}

.pager-dots{
	padding:0 6px;
	font-size:12px;
	color:#aaa
}

@media screen and (min-width:2560px){
	.pager-box{padding:12px 18px;gap:10px}
	.pager-btn{width:48px;height:48px;font-size:20px}
	.pager-num{min-width:46px;height:46px;font-size:18px}
}

@media screen and (min-width:1501px) and (max-width:2559px){
	.pager-btn{width:40px;height:40px}
	.pager-num{min-width:38px;height:38px;font-size:16px}
}

@media screen and (min-width:1400px) and (max-width:1500px){
	.pager-btn{width:36px;height:36px}
	.pager-num{min-width:34px;height:34px}
}

@media screen and (min-width:1200px) and (max-width:1399px){
	.pager-btn{width:34px;height:34px}
	.pager-num{min-width:32px;height:32px}
}

@media screen and (min-width:1001px) and (max-width:1199px){
	.pager-btn{width:32px;height:32px}
	.pager-num{min-width:30px;height:30px}
}

@media screen and (max-width:1000px){
	.pager-box{padding:5px 8px}
}

@media screen and (min-width:992px) and (max-width:999px){
	.pager-num{min-width:28px;height:28px;font-size:12px}
}

@media screen and (min-width:768px) and (max-width:991px){
	.pager-num{min-width:26px;height:26px;font-size:12px}
}

@media screen and (min-width:576px) and (max-width:767px){
	.pager-num:nth-child(n+5){display:none}
}

@media screen and (min-width:481px) and (max-width:575px){
	.pager-num:not(.active):not(:first-child):not(:last-child){display:none}
}

@media screen and (min-width:380px) and (max-width:480px){
	.pager-btn{width:26px;height:26px}
	.pager-num{min-width:24px;height:24px;font-size:11px}
}

@media screen and (max-width:379px){
	.pager-btn{width:26px;height:26px}
	.pager-num{min-width:24px;height:24px;font-size:11px}
}
</style>