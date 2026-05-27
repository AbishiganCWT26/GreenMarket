@php
	$view = request()->get('view', 'table');
	$perPage = $view === 'card' ? 12 : 15;
	$currentPage = request()->get('page', 1);
	$ordersPaginated = $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders : new \Illuminate\Pagination\LengthAwarePaginator($orders, $orders->count(), $perPage, $currentPage);
@endphp

@if($view === 'card')
	<div class="dispatch-card-grid">
		@forelse($ordersPaginated as $order)
			<div class="dispatch-card">
				<div class="dispatch-card-head">
					<span class="order-code">#{{ $order->order_number }}</span>
					<span class="status-tag status-{{ $order->order_status }}">
						{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
					</span>
				</div>
				<div class="dispatch-card-body">
					<div class="info-line">
						<i class="fas fa-user"></i>
						<span>{{ $order->buyer->name }}</span>
					</div>
					<div class="info-line">
						<i class="fas fa-map-marker-alt"></i>
						<span>{{ $order->buyer->district }}</span>
					</div>
					<div class="info-line">
						<i class="fas fa-box"></i>
						<span class="product-list">
							@foreach($order->orderItems as $item)
								{{ $item->product_name_snapshot }} x{{ $item->quantity_ordered }}@if(!$loop->last), @endif
							@endforeach
						</span>
					</div>
				</div>
				<div class="dispatch-card-foot">
					<button class="dispatch-action open-dispatch-modal" data-order='@json($order->load('orderItems'))'>
						<i class="fas fa-bus"></i> Dispatch
					</button>
				</div>
			</div>
		@empty
			<div class="empty-place">
				<i class="fas fa-truck-loading"></i>
				<h3>No orders ready for dispatch</h3>
				<p>Confirmed orders waiting for shipment will appear here.</p>
			</div>
		@endforelse
	</div>
@else
	<div class="dispatch-table-holder">
		<table class="dispatch-table">
			<thead>
				<tr>
					<th>Order #</th>
					<th>Buyer</th>
					<th>District</th>
					<th>Products</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@forelse($ordersPaginated as $order)
					<tr>
						<td data-label="Order #"><span class="order-code">#{{ $order->order_number }}</span></td>
						<td data-label="Buyer">{{ $order->buyer->name }}</td>
						<td data-label="District">{{ $order->buyer->district }}</td>
						<td data-label="Products">
							<div class="product-items">
								@foreach($order->orderItems as $item)
									<span class="product-badge">{{ $item->product_name_snapshot }} ({{ $item->quantity_ordered }})</span>
								@endforeach
							</div>
						</td>
						<td data-label="Status">
							<span class="status-tag status-{{ $order->order_status }}">
								{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
							</span>
							</td>
						<td data-label="Action">
							<button class="dispatch-action open-dispatch-modal" data-order='@json($order->load('orderItems'))'>
								<i class="fas fa-bus"></i> Dispatch
							</button>
							</td>
						</tr>
				@empty
					<tr>
						<td colspan="6" class="empty-cell">
							<div class="empty-place">
								<i class="fas fa-truck-loading"></i>
								<h3>No orders ready for dispatch</h3>
								<p>Confirmed orders waiting for shipment will appear here.</p>
							</div>
							</td>
						</tr>
				@endforelse
			</tbody>
		</table>
	</div>
@endif

@if($ordersPaginated->hasPages())
	<div class="pagination-wrap">
		{{ $ordersPaginated->appends(['view' => $view])->links() }}
	</div>
@endif