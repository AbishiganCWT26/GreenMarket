<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-info-circle"></i>
        <h3>About Us Content</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="about_us_1st_para">
                <i class="fas fa-paragraph"></i> First Paragraph
            </label>
            <textarea
                id="about_us_1st_para"
                name="config[about_us_1st_para]"
                class="config-input"
                rows="4"
                placeholder="Enter first paragraph..."
            >{{ $settings->where('config_key', 'about_us_1st_para')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="form-group">
            <label for="about_us_Our_Story_para_1">
                <i class="fas fa-book-open"></i> Our Story - Paragraph 1
            </label>
            <textarea
                id="about_us_Our_Story_para_1"
                name="config[about_us_Our_Story_para_1]"
                class="config-input"
                rows="3"
                placeholder="Enter our story first paragraph..."
            >{{ $settings->where('config_key', 'about_us_Our_Story_para_1')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="form-group">
            <label for="about_us_Our_Story_para_2">
                <i class="fas fa-book"></i> Our Story - Paragraph 2
            </label>
            <textarea
                id="about_us_Our_Story_para_2"
                name="config[about_us_Our_Story_para_2]"
                class="config-input"
                rows="3"
                placeholder="Enter our story second paragraph..."
            >{{ $settings->where('config_key', 'about_us_Our_Story_para_2')->first()->config_value ?? '' }}</textarea>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-eye"></i>
        <h3>Vision</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="about_us_Vision_para">
                <i class="fas fa-bullseye"></i> Vision Paragraph
            </label>
            <textarea
                id="about_us_Vision_para"
                name="config[about_us_Vision_para]"
                class="config-input"
                rows="3"
                placeholder="Enter vision statement..."
            >{{ $settings->where('config_key', 'about_us_Vision_para')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="vision-points">
            <div class="form-group">
                <label for="about_us_Vision_1st_point">
                    <i class="fas fa-leaf"></i> Vision Point 1
                </label>
                <input
                    type="text"
                    id="about_us_Vision_1st_point"
                    name="config[about_us_Vision_1st_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Vision_1st_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Sustainable Farming"
                >
            </div>

            <div class="form-group">
                <label for="about_us_Vision_2nd_point">
                    <i class="fas fa-hand-holding-heart"></i> Vision Point 2
                </label>
                <input
                    type="text"
                    id="about_us_Vision_2nd_point"
                    name="config[about_us_Vision_2nd_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Vision_2nd_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Economic Empowerment"
                >
            </div>

            <div class="form-group">
                <label for="about_us_Vision_3rd_point">
                    <i class="fas fa-users"></i> Vision Point 3
                </label>
                <input
                    type="text"
                    id="about_us_Vision_3rd_point"
                    name="config[about_us_Vision_3rd_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Vision_3rd_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Community Growth"
                >
            </div>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-flag"></i>
        <h3>Mission</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="about_us_Mission_para">
                <i class="fas fa-bullhorn"></i> Mission Paragraph
            </label>
            <textarea
                id="about_us_Mission_para"
                name="config[about_us_Mission_para]"
                class="config-input"
                rows="3"
                placeholder="Enter mission statement..."
            >{{ $settings->where('config_key', 'about_us_Mission_para')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="mission-points">
            <div class="form-group">
                <label for="about_us_Mission_1st_point">
                    <i class="fas fa-link"></i> Mission Point 1
                </label>
                <input
                    type="text"
                    id="about_us_Mission_1st_point"
                    name="config[about_us_Mission_1st_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Mission_1st_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Direct Connections"
                >
            </div>

            <div class="form-group">
                <label for="about_us_Mission_2nd_point">
                    <i class="fas fa-money-bill-wave"></i> Mission Point 2
                </label>
                <input
                    type="text"
                    id="about_us_Mission_2nd_point"
                    name="config[about_us_Mission_2nd_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Mission_2nd_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Fair Pricing"
                >
            </div>

            <div class="form-group">
                <label for="about_us_Mission_3rd_point">
                    <i class="fas fa-laptop-code"></i> Mission Point 3
                </label>
                <input
                    type="text"
                    id="about_us_Mission_3rd_point"
                    name="config[about_us_Mission_3rd_point]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'about_us_Mission_3rd_point')->first()->config_value ?? '' }}"
                    placeholder="e.g., Technology Integration"
                >
            </div>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-images"></i>
        <h3>About Us Images</h3>
    </div>

    <div class="section-body">
        <div class="image-upload-grid">
            <div class="image-upload-card">
                <div class="image-preview-container">
                    <img
                        id="preview_about_us_image_1"
                        src="{{ asset('assets/images/' . ($settings->where('config_key', 'about_us_image_1')->first()->config_value ?? '')) }}"
                        onerror="this.style.display='none'"
                        class="image-preview"
                    >
                    <div class="image-placeholder" id="placeholder_about_us_image_1">
                        <i class="fas fa-image"></i>
                        <span>About Us Image 1</span>
                    </div>
                </div>
                <div class="image-controls">
                    <label for="image_about_us_image_1" class="btn-upload">
                        <i class="fas fa-upload"></i> Upload Image
                        <input
                            type="file"
                            id="image_about_us_image_1"
                            name="image_about_us_image_1"
                            accept="image/*"
                            onchange="previewImage('image_about_us_image_1')"
                            hidden
                        >
                    </label>
                    <button type="button" onclick="removeImage('image_about_us_image_1')" class="btn-remove" id="remove_image_about_us_image_1" style="display: none;">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>

            <div class="image-upload-card">
                <div class="image-preview-container">
                    <img
                        id="preview_about_us_image_2"
                        src="{{ asset('assets/images/' . ($settings->where('config_key', 'about_us_image_2')->first()->config_value ?? '')) }}"
                        onerror="this.style.display='none'"
                        class="image-preview"
                    >
                    <div class="image-placeholder" id="placeholder_about_us_image_2">
                        <i class="fas fa-image"></i>
                        <span>About Us Image 2</span>
                    </div>
                </div>
                <div class="image-controls">
                    <label for="image_about_us_image_2" class="btn-upload">
                        <i class="fas fa-upload"></i> Upload Image
                        <input
                            type="file"
                            id="image_about_us_image_2"
                            name="image_about_us_image_2"
                            accept="image/*"
                            onchange="previewImage('image_about_us_image_2')"
                            hidden
                        >
                    </label>
                    <button type="button" onclick="removeImage('image_about_us_image_2')" class="btn-remove" id="remove_image_about_us_image_2" style="display: none;">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
