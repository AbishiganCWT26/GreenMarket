@php
	$view = request()->get('view', 'table');
	$perPage = $view === 'card' ? 12 : 15;
	$currentPage = request()->get('page', 1);
	$ordersPaginated = $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders : new \Illuminate\Pagination\LengthAwarePaginator($orders, $orders->count(), $perPage, $currentPage);
@endphp

@if($view === 'card')
	<div class="card-grid">
		@forelse($ordersPaginated as $order)
			<div class="card-item">
				<div class="card-item-head">
					<span class="order-code">#{{ $order->order_number }}</span>
					<span class="status-tag status-{{ $order->paymentDeliveryOrder->payment_status }}">
						{{ ucfirst(str_replace('_', ' ', $order->paymentDeliveryOrder->payment_status)) }}
					</span>
				</div>
				<div class="card-item-body">
					<div class="info-line">
						<i class="fas fa-user"></i>
						<span>{{ $order->buyer->name }}</span>
					</div>
					<div class="info-line">
						<i class="fas fa-map-marker-alt"></i>
						<span>{{ $order->buyer->district }}</span>
					</div>
					<div class="info-line">
						<i class="fas fa-money-bill-wave"></i>
						<span class="price-tag">LKR {{ number_format($order->total_amount, 2) }}</span>
					</div>
				</div>
				<div class="card-item-foot">
					<button class="verify-btn open-verify-modal" data-order='@json($order)'>
						<i class="fas fa-search-dollar"></i> Verify
					</button>
				</div>
			</div>
		@empty
			<div class="empty-place">
				<i class="fas fa-clipboard-check"></i>
				<h3>No pending verifications</h3>
				<p>All delivery payments are currently verified.</p>
			</div>
		@endforelse
	</div>
@else
	<div class="table-holder">
		<table class="data-table">
			<thead>
				<tr>
					<th>Order #</th>
					<th>Buyer</th>
					<th>District</th>
					<th>Total Amount</th>
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
						<td data-label="Amount">LKR {{ number_format($order->total_amount, 2) }}</td>
						<td data-label="Status">
							<span class="status-tag status-{{ $order->paymentDeliveryOrder->payment_status }}">
								{{ ucfirst(str_replace('_', ' ', $order->paymentDeliveryOrder->payment_status)) }}
							</span>
						</td>
						<td data-label="Action">
							<button class="action-verify open-verify-modal" data-order='@json($order)'>
								<i class="fas fa-search-dollar"></i> Verify
							</button>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="empty-cell">
							<div class="empty-place">
								<i class="fas fa-clipboard-check"></i>
								<h3>No pending verifications</h3>
								<p>All delivery payments are currently verified.</p>
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