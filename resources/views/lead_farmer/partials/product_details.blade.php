<style>
:root {
	--primary-green: #10B981;
	--dark-green: #059669;
	--body-bg: #f6f8fa;
	--card-bg: #ffffff;
	--text-color: #0f1724;
	--muted: #6b7280;
	--border-light: #e5e7eb;
	--success: #10B981;
	--warning: #f59e0b;
	--danger: #ef4444;
	--shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
	--shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
	--shadow-md: 0 4px 8px rgba(0,0,0,0.08);
	--shadow-lg: 0 10px 20px rgba(0,0,0,0.1);
}

.product-modal-container {
	padding: 0;
	font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
	background: var(--body-bg);
}

.modal-header {
	background: var(--card-bg);
	padding: 20px 24px;
	border-bottom: 1px solid var(--border-light);
	position: relative;
}

.modal-header::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 0;
	width: 100%;
	height: 1px;
	background: linear-gradient(90deg, var(--primary-green), transparent);
}

.header-content {
	display: flex;
	align-items: center;
	gap: 16px;
	flex-wrap: wrap;
}

.product-icon {
	width: 48px;
	height: 48px;
	background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 20px;
	box-shadow: var(--shadow-md);
	transition: all 0.3s ease;
}

.product-icon:hover {
	transform: scale(1.05) rotate(5deg);
	box-shadow: 0 6px 12px rgba(16, 185, 129, 0.2);
}

.product-title h3 {
	font-size: 22px;
	font-weight: 600;
	color: var(--text-color);
	margin: 0 0 4px 0;
	line-height: 1.3;
}

.product-subtitle {
	color: var(--muted);
	font-size: 14px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.variant-badge {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 4px 12px;
	background: rgba(16, 185, 129, 0.1);
	color: var(--primary-green);
	border-radius: 20px;
	font-size: 13px;
	font-weight: 500;
	border: 1px solid rgba(16, 185, 129, 0.2);
	transition: all 0.3s ease;
}

.variant-badge:hover {
	background: rgba(16, 185, 129, 0.2);
	transform: translateY(-1px);
}

.modal-body {
	padding: 24px;
	animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}

@keyframes slideUp {
	from {
		opacity: 0;
		transform: translateY(10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

.image-section {
	margin-bottom: 24px;
	animation: slideUp 0.5s ease 0.1s both;
}

.product-image-container {
	position: relative;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: var(--shadow-md);
	background: var(--card-bg);
	border: 1px solid var(--border-light);
	transition: all 0.3s ease;
	height: 280px;
}

.product-image-container:hover {
	transform: translateY(-4px);
	box-shadow: var(--shadow-lg);
	border-color: var(--primary-green);
}

.product-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.5s ease;
}

.product-image-container:hover .product-image {
	transform: scale(1.03);
}

.image-placeholder {
	width: 100%;
	height: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	background: linear-gradient(135deg, #f8fafc, #f1f5f9);
	color: var(--muted);
}

.placeholder-icon {
	font-size: 64px;
	color: rgba(16, 185, 129, 0.3);
	margin-bottom: 12px;
	transition: all 0.3s ease;
}

.image-placeholder:hover .placeholder-icon {
	color: var(--primary-green);
	transform: scale(1.1);
}

.info-section {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 20px;
	margin-bottom: 24px;
}

.info-card {
	background: var(--card-bg);
	border-radius: 12px;
	padding: 20px;
	box-shadow: var(--shadow-sm);
	border: 1px solid var(--border-light);
	transition: all 0.3s ease;
	animation: slideUp 0.5s ease 0.2s both;
}

.info-card:hover {
	transform: translateY(-3px);
	box-shadow: var(--shadow-md);
	border-color: var(--primary-green);
}

.info-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 0;
	border-bottom: 1px solid rgba(229, 231, 235, 0.5);
	transition: all 0.3s ease;
}

.info-item:last-child {
	border-bottom: none;
}

.info-item:hover {
	background: rgba(16, 185, 129, 0.02);
	padding-left: 8px;
	padding-right: 8px;
	border-radius: 8px;
	margin: 0 -8px;
}

.info-icon {
	width: 36px;
	height: 36px;
	min-width: 36px;
	background: rgba(16, 185, 129, 0.1);
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: var(--primary-green);
	font-size: 16px;
	transition: all 0.3s ease;
}

.info-item:hover .info-icon {
	background: var(--primary-green);
	color: white !important;
	transform: scale(1.1);
}

.info-item:hover i {
    color: white !important;
    transform: scale(1.1);
}

.info-item:hover .info-icon .i {
	color: white !important;
}

.info-content {
	flex: 1;
}

.info-label {
	font-size: 12px;
	color: var(--muted);
	font-weight: 500;
	text-transform: uppercase;
	letter-spacing: 0.3px;
	margin-bottom: 2px;
	display: block;
}

.info-value {
	font-size: 14px;
	color: var(--text-color);
	font-weight: 500;
	line-height: 1.4;
}

.highlight-value {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 6px 12px;
	background: rgba(245, 158, 11, 0.1);
	color: var(--warning);
	border-radius: 8px;
	font-weight: 600;
	font-size: 14px;
	border: 1px solid rgba(245, 158, 11, 0.2);
	transition: all 0.3s ease;
}

.highlight-value:hover {
	transform: translateY(-2px);
	box-shadow: var(--shadow-sm);
}

.status-badge {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 6px 12px;
	border-radius: 20px;
	font-size: 13px;
	font-weight: 500;
	transition: all 0.3s ease;
}

.status-available {
	background: rgba(16, 185, 129, 0.1);
	color: var(--success);
	border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-unavailable {
	background: rgba(239, 68, 68, 0.1);
	color: var(--danger);
	border: 1px solid rgba(239, 68, 68, 0.2);
}

.status-badge:hover {
	transform: scale(1.05);
}

.pickup-section {
	background: var(--card-bg);
	border-radius: 12px;
	padding: 20px;
	margin-top: 24px;
	box-shadow: var(--shadow-sm);
	border: 1px solid var(--border-light);
	transition: all 0.3s ease;
	animation: slideUp 0.5s ease 0.3s both;
}

.pickup-section:hover {
	transform: translateY(-2px);
	box-shadow: var(--shadow-md);
	border-color: var(--primary-green);
}

.section-header {
	display: flex;
	align-items: center;
	gap: 10px;
	margin-bottom: 16px;
	font-size: 16px;
	font-weight: 600;
	color: var(--text-color);
}

.section-header i {
	color: var(--primary-green);
	transition: all 0.3s ease;
}

.pickup-section:hover .section-header i {
	transform: rotate(15deg);
}

.address-card {
	background: #f8fafc;
	border-radius: 8px;
	padding: 16px;
	margin-bottom: 20px;
	border-left: 3px solid var(--primary-green);
	transition: all 0.3s ease;
}

.address-card:hover {
	transform: translateX(2px);
	background: #f1f5f9;
}

.address-text {
	margin: 0;
	color: var(--text-color);
	font-size: 14px;
	line-height: 1.5;
}

.map-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 10px 20px;
	background: var(--primary-green);
	color: white;
	text-decoration: none;
	border-radius: 8px;
	font-weight: 500;
	font-size: 14px;
	transition: all 0.3s ease;
	box-shadow: var(--shadow-sm);
}

.map-link:hover {
	background: var(--dark-green);
	transform: translateY(-2px);
	box-shadow: var(--shadow-md);
	color: white;
}

.description-card {
	background: var(--card-bg);
	border-radius: 12px;
	padding: 20px;
	margin-top: 20px;
	box-shadow: var(--shadow-sm);
	border: 1px solid var(--border-light);
	animation: slideUp 0.5s ease 0.2s both;
}

.description-card:hover {
	border-color: var(--primary-green);
	transform: translateY(-2px);
	box-shadow: var(--shadow-md);
}

.description-text {
	color: var(--text-color);
	line-height: 1.6;
	margin: 0;
	font-size: 14px;
}

@media (max-width: 1200px) {
	.info-section {
		grid-template-columns: repeat(2, 1fr);
	}
	
	.product-image-container {
		height: 250px;
	}
}

@media (max-width: 992px) {
	.modal-body {
		padding: 20px;
	}
	
	.header-content {
		gap: 12px;
	}
	
	.product-icon {
		width: 44px;
		height: 44px;
		font-size: 18px;
	}
	
	.product-title h3 {
		font-size: 20px;
	}
	
	.product-image-container {
		height: 220px;
	}
	
	.placeholder-icon {
		font-size: 56px;
	}
	
	.info-card {
		padding: 18px;
	}
	
	.pickup-section {
		padding: 18px;
	}
	
	.action-section {
		margin-top: 28px;
		padding-top: 20px;
	}
}

@media (max-width: 768px) {
	.modal-header {
		padding: 16px;
	}
	
	.modal-body {
		padding: 16px;
	}
	
	.product-title h3 {
		font-size: 18px;
	}
	
	.product-subtitle {
		font-size: 13px;
	}
	
	.product-image-container {
		height: 200px;
	}
	
	.placeholder-icon {
		font-size: 48px;
	}
	
	.info-section {
		grid-template-columns: 1fr;
		gap: 16px;
	}
	
	.info-item {
		padding: 10px 0;
	}
	
	.info-icon {
		width: 32px;
		height: 32px;
		font-size: 14px;
	}
	
	.info-value {
		font-size: 13px;
	}
	
	.highlight-value {
		padding: 5px 10px;
		font-size: 13px;
	}
	
	.status-badge {
		font-size: 12px;
	}
	
	.description-card {
		padding: 16px;
	}
	
	.description-text {
		font-size: 13px;
	}
	
	.pickup-section {
		padding: 16px;
	}
	
	.section-header {
		font-size: 15px;
	}
	
	.address-card {
		padding: 14px;
	}
	
	.address-text {
		font-size: 13px;
	}
	
	.map-link {
		padding: 9px 18px;
		font-size: 13px;
	}
}

@media (max-width: 576px) {
	.modal-header {
		padding: 14px;
	}
	
	.modal-body {
		padding: 14px;
	}
	
	.header-content {
		flex-direction: column;
		align-items: flex-start;
	}
	
	.product-icon {
		width: 40px;
		height: 40px;
		font-size: 16px;
	}
	
	.product-title h3 {
		font-size: 16px;
	}
	
	.product-image-container {
		height: 180px;
	}
	
	.placeholder-icon {
		font-size: 40px;
	}
	
	.info-card {
		padding: 16px;
	}
	
	.info-item {
		flex-direction: column;
		align-items: flex-start;
		gap: 8px;
		padding: 12px 0;
	}
	
	.info-item:hover {
		padding: 12px 8px;
	}
	
	.info-icon {
		width: 28px;
		height: 28px;
		font-size: 12px;
	}
	
	.info-label {
		font-size: 11px;
	}
	
	.info-value {
		font-size: 12px;
	}
	
	.highlight-value {
		padding: 4px 8px;
		font-size: 12px;
	}
	
	.variant-badge {
		font-size: 12px;
		padding: 3px 10px;
	}
	
	.description-card {
		padding: 14px;
	}
	
	.description-text {
		font-size: 12px;
	}
	
	.pickup-section {
		padding: 14px;
	}
	
	.section-header {
		font-size: 14px;
	}
	
	.address-card {
		padding: 12px;
	}
	
	.address-text {
		font-size: 12px;
	}
	
	.map-link {
		padding: 8px 16px;
		font-size: 12px;
		width: 100%;
		justify-content: center;
	}
	
	.action-btn {
		padding: 9px 14px;
		font-size: 12px;
	}
}

@media (max-width: 400px) {
	.product-image-container {
		height: 160px;
	}
	
	.placeholder-icon {
		font-size: 36px;
	}
	
	.info-card {
		padding: 14px;
	}
	
	.pickup-section {
		padding: 12px;
	}
	
	.address-card {
		padding: 10px;
	}
	
	.modal-header {
		padding: 12px;
	}
	
	.modal-body {
		padding: 12px;
	}
}
</style>

<div class="product-modal-container">
	<div class="modal-header">
		<div class="header-content">
			<div class="product-icon">
				<i class="fa-solid fa-seedling"></i>
			</div>
			<div class="product-title">
				<h3>{{ $product->product_name }}</h3>
				<div class="product-subtitle">
					@if($product->type_variant)
					<span class="variant-badge">
						<i class="fa-solid fa-leaf"></i>
						{{ $product->type_variant }}
					</span>
					@endif
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal-body">
		<div class="image-section">
			<div class="product-image-container">
				@if($product->product_photo)
				<img src="{{ asset('uploads/product_images/' . $product->product_photo) }}" 
					 alt="{{ $product->product_name }}"
					 class="product-image"
					 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
				@else
				<div class="image-placeholder">
					<i class="fa-solid fa-seedling placeholder-icon"></i>
					<p>No image available</p>
				</div>
				@endif
			</div>
		</div>
		
		<div class="info-section">
			<div class="info-card">
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-user-tie"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Farmer</span>
						<span class="info-value">{{ $product->farmer->name ?? 'Unknown' }}</span>
					</div>
				</div>
				
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-layer-group"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Category</span>
						<span class="info-value">{{ $product->category->category_name ?? 'Unknown' }}</span>
					</div>
				</div>
				
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-folder-open"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Subcategory</span>
						<span class="info-value">{{ $product->subcategory->subcategory_name ?? 'Unknown' }}</span>
					</div>
				</div>
			</div>
			
			<div class="info-card">
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-scale-balanced"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Quantity Available</span>
						<div class="highlight-value">
							<i class="fa-solid fa-box"></i>
							<span>{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
						</div>
					</div>
				</div>
				
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-tag"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Selling Price</span>
						<div class="highlight-value">
							<i class="fa-solid fa-coins"></i>
							<span>LKR {{ number_format($product->selling_price, 2) }}</span>
						</div>
					</div>
				</div>
				
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-star"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Quality Grade</span>
						<span class="info-value">{{ $product->quality_grade ?? 'Not specified' }}</span>
					</div>
				</div>
			</div>
			
			<div class="info-card">
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-calendar-day"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Availability Date</span>
						<span class="info-value">{{ \Carbon\Carbon::parse($product->expected_availability_date)->format('M d, Y') }}</span>
					</div>
				</div>
				
				<div class="info-item">
					<div class="info-icon">
						<i class="fa-solid fa-circle-check"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Status</span>
						@if($product->is_available && $product->quantity > 0)
						<span class="status-badge status-available">
							<i class="fa-solid fa-check"></i>
							Available for Purchase
						</span>
						@else
						<span class="status-badge status-unavailable">
							<i class="fa-solid fa-xmark"></i>
							Currently Unavailable
						</span>
						@endif
					</div>
				</div>
			</div>
		</div>
		
		@if($product->product_description)
		<div class="description-card">
            <span class="info-label">Product Description</span>
			<p class="description-text">{{ $product->product_description }}</p>
		</div>
		@endif
		
		@if($product->pickup_address)
		<div class="pickup-section">
			<div class="section-header">
				<i class="fa-solid fa-location-dot"></i>
				Pickup Information
			</div>
			
			<div class="address-card">
				<p class="address-text">{{ $product->pickup_address }}</p>
			</div>
			
			@if($product->pickup_map_link)
			<a href="{{ $product->pickup_map_link }}" target="_blank" class="map-link">
				<i class="fa-solid fa-map-location-dot"></i>
				View Location on Map
			</a>
			@endif
		</div>
		@endif
	</div>
</div>