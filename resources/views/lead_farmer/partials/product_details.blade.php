<style>
:root{
    --primary-accent:#10B981;
    --primary-dark:#059669;
    --card-bg:#ffffff;
    --body-bg:#f6f8fa;
    --text-dark:#0f1724;
    --muted:#6b7280;
    --sidebar-width:240px;
    --sidebar-width-collapsed:60px;
    --shadow-xs:0 1px 3px rgba(15,23,36,0.04);
    --shadow-sm:0 4px 10px rgba(15,23,36,0.06);
    --shadow-md:0 7px 15px rgba(15,23,36,0.08);
    --accent-amber:#f59e0b;
    --blue:#3b82f6;
    --purple:#8b5cf6;
    --yellow:#f59e0b;
    --text-color:#0f1724;
    --primary-green:#10B981;
    --dark-green:#059669;
}

.product-modal-container {
    padding: 0;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

.modal-header-section {
    padding: 24px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
    border-bottom: 1px solid rgba(16, 185, 129, 0.1);
    position: relative;
    overflow: hidden;
}

.modal-header-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    background: var(--primary-green);
}

.product-title-container {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.product-title-icon {
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
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-title-icon:hover {
    transform: rotate(15deg) scale(1.1);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.product-title-text {
    flex: 1;
}

.product-title-text h3 {
    color: var(--text-dark);
    font-weight: 700;
    font-size: 24px;
    margin: 0;
    line-height: 1.3;
}

.product-variant-chip {
    background: rgba(16, 185, 129, 0.1);
    color: var(--primary-green);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgba(16, 185, 129, 0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    cursor: default;
}

.product-variant-chip:hover {
    background: rgba(16, 185, 129, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
}

.modal-content-area {
    padding: 24px;
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.product-image-wrapper {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(16, 185, 129, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 300px;
}

.product-image-wrapper:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(15, 23, 36, 0.15);
    border-color: var(--primary-green);
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-image-wrapper:hover .product-image {
    transform: scale(1.05);
}

.image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
    color: var(--muted);
}

.image-placeholder-icon {
    font-size: 72px;
    color: rgba(16, 185, 129, 0.3);
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.image-placeholder:hover .image-placeholder-icon {
    color: var(--primary-green);
    transform: rotate(10deg) scale(1.1);
}

.product-description-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    border-left: 4px solid var(--primary-green);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.product-description-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateX(4px);
}

.description-text {
    color: var(--text-color);
    line-height: 1.6;
    margin: 0;
    font-size: 15px;
}

.product-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.info-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(16, 185, 129, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.info-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.info-card:hover::before {
    transform: scaleX(1);
}

.info-item {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding: 10px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(16, 185, 129, 0.05);
    transform: translateX(5px);
}

.info-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-green);
    font-size: 18px;
    transition: all 0.3s ease;
}

.info-item:hover .info-icon {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    transform: rotate(10deg) scale(1.1);
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    display: block;
}

.info-value {
    font-size: 15px;
    color: var(--text-color);
    font-weight: 500;
    line-height: 1.4;
}

.info-highlight {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.price-highlight {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
    color: var(--accent-amber);
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.quantity-highlight {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
    color: var(--blue);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.info-highlight:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: default;
}

.status-available {
    background: rgba(16, 185, 129, 0.15);
    color: var(--primary-green);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-soldout {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-indicator:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.pickup-info-section {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    border: 1px solid rgba(16, 185, 129, 0.1);
    transition: all 0.3s ease;
}

.pickup-info-section:hover {
    border-color: var(--primary-green);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    color: var(--text-dark);
    font-size: 18px;
    font-weight: 600;
}

.section-title i {
    color: var(--primary-green);
    transition: all 0.3s ease;
}

.pickup-info-section:hover .section-title i {
    transform: rotate(15deg) scale(1.1);
}

.pickup-address-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 20px;
    border-left: 3px solid var(--primary-green);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.pickup-address-card:hover {
    transform: translateX(5px);
    box-shadow: var(--shadow-md);
}

.address-text {
    margin: 0;
    color: var(--text-color);
    line-height: 1.5;
}

.map-button {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-sm);
    cursor: pointer;
}

.map-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    color: white;
}

.action-buttons-container {
    display: flex;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(16, 185, 129, 0.1);
}

.action-button {
    flex: 1;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    cursor: pointer;
}

.edit-button {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    box-shadow: var(--shadow-sm);
}

.edit-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.close-button {
    background: var(--card-bg);
    color: var(--muted);
    border: 2px solid rgba(100, 116, 139, 0.2);
}

.close-button:hover {
    background: #64748b;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(100, 116, 139, 0.2);
}

@media (max-width: 1200px) {
    .product-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .modal-header-section {
        padding: 20px;
    }
    
    .product-title-text h3 {
        font-size: 22px;
    }
    
    .product-image-wrapper {
        height: 260px;
    }
    
    .modal-content-area {
        padding: 20px;
    }
    
    .product-info-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .info-card {
        padding: 18px;
    }
}

@media (max-width: 768px) {
    .modal-header-section {
        padding: 16px;
    }
    
    .product-title-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .product-title-text h3 {
        font-size: 20px;
    }
    
    .product-image-wrapper {
        height: 220px;
    }
    
    .image-placeholder-icon {
        font-size: 56px;
    }
    
    .product-description-card {
        padding: 16px;
    }
    
    .description-text {
        font-size: 14px;
    }
    
    .pickup-info-section {
        padding: 20px;
    }
    
    .action-buttons-container {
        flex-direction: column;
        gap: 12px;
    }
    
    .action-button {
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .info-item {
        gap: 12px;
    }
    
    .info-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .modal-header-section {
        padding: 12px;
    }
    
    .product-title-text h3 {
        font-size: 18px;
    }
    
    .product-title-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .product-image-wrapper {
        height: 200px;
    }
    
    .image-placeholder-icon {
        font-size: 48px;
    }
    
    .product-variant-chip {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .info-card {
        padding: 16px;
    }
    
    .info-label {
        font-size: 11px;
    }
    
    .info-value {
        font-size: 14px;
    }
    
    .info-highlight {
        padding: 6px 12px;
        font-size: 14px;
    }
    
    .status-indicator {
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .pickup-info-section {
        padding: 16px;
    }
    
    .section-title {
        font-size: 16px;
    }
    
    .map-button {
        padding: 10px 20px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .product-image-wrapper {
        height: 180px;
    }
    
    .image-placeholder-icon {
        font-size: 40px;
    }
    
    .product-title-text h3 {
        font-size: 16px;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .info-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .info-highlight {
        font-size: 13px;
    }
    
    .action-button {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .map-button {
        padding: 8px 16px;
        font-size: 12px;
    }
}
</style>

<div class="product-modal-container">
    <div class="modal-header-section">
        <div class="product-title-container">
            <div class="product-title-icon">
                <i class="fa-solid fa-seedling"></i>
            </div>
            <div class="product-title-text">
                <h3>{{ $product->product_name }}</h3>
                @if($product->type_variant)
                    <div class="product-variant-chip">
                        <i class="fa-solid fa-leaf"></i>
                        {{ $product->type_variant }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="modal-content-area">
        <div class="row">
            <div class="col-md-5">
                <div class="product-image-wrapper">
                    @if($product->product_photo)
                        <img src="{{ asset('uploads/product_images/' . $product->product_photo) }}" 
                             alt="{{ $product->product_name }}"
                             class="product-image"
                             onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                    @else
                        <div class="image-placeholder">
                            <i class="fa-solid fa-seedling image-placeholder-icon"></i>
                            <p>No image available</p>
                        </div>
                    @endif
                </div>
                
                @if($product->product_description)
                    <div class="product-description-card">
                        <p class="description-text">{{ $product->product_description }}</p>
                    </div>
                @endif
            </div>
            
            <div class="col-md-7">
                <div class="product-info-grid">
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
                                <div class="info-highlight quantity-highlight">
                                    <i class="fa-solid fa-box"></i>
                                    <strong>{{ number_format($product->quantity, 2) }}</strong> {{ $product->unit_of_measure }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fa-solid fa-tag"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Selling Price</span>
                                <div class="info-highlight price-highlight">
                                    <i class="fa-solid fa-coins"></i>
                                    <strong>LKR {{ number_format($product->selling_price, 2) }}</strong>
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
                                    <span class="status-indicator status-available">
                                        <i class="fa-solid fa-check"></i> Available for Purchase
                                    </span>
                                @else
                                    <span class="status-indicator status-soldout">
                                        <i class="fa-solid fa-xmark"></i> Currently Unavailable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($product->pickup_address)
                    <div class="pickup-info-section">
                        <div class="section-title">
                            <i class="fa-solid fa-location-dot"></i>
                            Pickup Information
                        </div>
                        
                        <div class="pickup-address-card">
                            <p class="address-text">{{ $product->pickup_address }}</p>
                        </div>
                        
                        @if($product->pickup_map_link)
                            <a href="{{ $product->pickup_map_link }}" target="_blank" class="map-button">
                                <i class="fa-solid fa-map-location-dot"></i> View Location on Map
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <div class="action-buttons-container">
            <button type="button" class="action-button edit-button" onclick="handleEditProduct()">
                <i class="fa-solid fa-pen-to-square"></i> Edit Product Details
            </button>
            <button type="button" class="action-button close-button" onclick="closeProductModal()">
                <i class="fa-solid fa-times"></i> Close
            </button>
        </div>
    </div>
</div>
