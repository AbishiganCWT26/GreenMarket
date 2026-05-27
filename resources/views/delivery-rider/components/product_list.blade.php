<div class="product-list-container card-premium p-4 mt-4">
    <h5 class="fw-bold mb-3"><i class="fa-solid fa-carrot text-primary me-2"></i> Package Itemized Contents</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle custom-product-table">
            <thead class="table-light">
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Grade</th>
                    <th scope="col" class="text-end">Qty</th>
                    <th scope="col" class="text-end">Price</th>
                    <th scope="col" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->product && $item->product->product_photo)
                                    <img src="{{ asset('uploads/products/' . $item->product->product_photo) }}" alt="{{ $item->product_name_snapshot }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item->product_name_snapshot }}</div>
                                    <small class="text-muted">{{ $item->product->unit_of_measure ?? 'Units' }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $item->quality_grade_snapshot }}</span></td>
                        <td class="text-end fw-semibold">{{ number_format($item->quantity_ordered, 1) }}</td>
                        <td class="text-end">Rs. {{ number_format($item->unit_price_snapshot, 2) }}</td>
                        <td class="text-end fw-bold text-dark">Rs. {{ number_format($item->item_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No items in this shipment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
