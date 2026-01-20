<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-shopping-cart"></i>
        <h3>For Buyers</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="How_Works_For_Buyers_para">
                <i class="fas fa-list-alt"></i> Buyer Instructions
            </label>
            <textarea
                id="How_Works_For_Buyers_para"
                name="config[How_Works_For_Buyers_para]"
                class="config-input"
                rows="6"
                placeholder="Enter step-by-step instructions for buyers..."
            >{{ $settings->where('config_key', 'How_Works_For_Buyers_para')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="image-upload-card full-width">
            <div class="image-preview-container">
                <img
                    id="preview_How_Works_For_Buyers_image"
                    src="{{ asset('assets/images/' . ($settings->where('config_key', 'How_Works_For_Buyers_image')->first()->config_value ?? '')) }}"
                    onerror="this.style.display='none'"
                    class="image-preview"
                >
                <div class="image-placeholder" id="placeholder_How_Works_For_Buyers_image">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Buyer Process Image</span>
                </div>
            </div>
            <div class="image-controls">
                <label for="image_How_Works_For_Buyers_image" class="btn-upload">
                    <i class="fas fa-upload"></i> Upload Buyer Image
                    <input
                        type="file"
                        id="image_How_Works_For_Buyers_image"
                        name="image_How_Works_For_Buyers_image"
                        accept="image/*"
                        onchange="previewImage('image_How_Works_For_Buyers_image')"
                        hidden
                    >
                </label>
                <button type="button" onclick="removeImage('image_How_Works_For_Buyers_image')" class="btn-remove" id="remove_image_How_Works_For_Buyers_image" style="display: none;">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-tractor"></i>
        <h3>For Farmers</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="How_Works_For_Farmers_para">
                <i class="fas fa-list-alt"></i> Farmer Instructions
            </label>
            <textarea
                id="How_Works_For_Farmers_para"
                name="config[How_Works_For_Farmers_para]"
                class="config-input"
                rows="6"
                placeholder="Enter step-by-step instructions for farmers..."
            >{{ $settings->where('config_key', 'How_Works_For_Farmers_para')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="image-upload-card full-width">
            <div class="image-preview-container">
                <img
                    id="preview_How_Works_For_Farmer_image"
                    src="{{ asset('assets/images/' . ($settings->where('config_key', 'How_Works_For_Farmer_image')->first()->config_value ?? '')) }}"
                    onerror="this.style.display='none'"
                    class="image-preview"
                >
                <div class="image-placeholder" id="placeholder_How_Works_For_Farmer_image">
                    <i class="fas fa-tractor"></i>
                    <span>Farmer Process Image</span>
                </div>
            </div>
            <div class="image-controls">
                <label for="image_How_Works_For_Farmer_image" class="btn-upload">
                    <i class="fas fa-upload"></i> Upload Farmer Image
                    <input
                        type="file"
                        id="image_How_Works_For_Farmer_image"
                        name="image_How_Works_For_Farmer_image"
                        accept="image/*"
                        onchange="previewImage('image_How_Works_For_Farmer_image')"
                        hidden
                    >
                </label>
                <button type="button" onclick="removeImage('image_How_Works_For_Farmer_image')" class="btn-remove" id="remove_image_How_Works_For_Farmer_image" style="display: none;">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    </div>
</div>
