<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-shoe-prints"></i>
        <h3>Footer Information</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="footer_copyright">
                <i class="fas fa-copyright"></i> Copyright Text
            </label>
            <textarea
                id="footer_copyright"
                name="config[footer_copyright]"
                class="config-input"
                rows="2"
                placeholder="Enter copyright text..."
            >{{ $settings->where('config_key', 'footer_copyright')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="footer_contact_no">
                    <i class="fas fa-phone"></i> Contact Number
                </label>
                <input
                    type="text"
                    id="footer_contact_no"
                    name="config[footer_contact_no]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_contact_no')->first()->config_value ?? '' }}"
                    placeholder="e.g., 011 205 3252"
                >
            </div>

            <div class="form-group">
                <label for="footer_email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input
                    type="email"
                    id="footer_email"
                    name="config[footer_email]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_email')->first()->config_value ?? '' }}"
                    placeholder="e.g., contact.pmu@csiap.lk"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="footer_fax_no">
                <i class="fas fa-fax"></i> Fax Number
            </label>
            <input
                type="text"
                id="footer_fax_no"
                name="config[footer_fax_no]"
                class="config-input"
                value="{{ $settings->where('config_key', 'footer_fax_no')->first()->config_value ?? '' }}"
                placeholder="e.g., 011 205 3167"
            >
        </div>

        <div class="form-group">
            <label for="footer_address">
                <i class="fas fa-map-marker-alt"></i> Address
            </label>
            <textarea
                id="footer_address"
                name="config[footer_address]"
                class="config-input"
                rows="4"
                placeholder="Enter full address..."
            >{{ $settings->where('config_key', 'footer_address')->first()->config_value ?? '' }}</textarea>
        </div>

        <div class="form-group">
            <label for="footer_small_para">
                <i class="fas fa-paragraph"></i> Small Description
            </label>
            <textarea
                id="footer_small_para"
                name="config[footer_small_para]"
                class="config-input"
                rows="3"
                placeholder="Enter small description..."
            >{{ $settings->where('config_key', 'footer_small_para')->first()->config_value ?? '' }}</textarea>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-share-alt"></i>
        <h3>Social Media Links</h3>
    </div>

    <div class="section-body">
        <div class="form-row">
            <div class="form-group">
                <label for="footer_youtube">
                    <i class="fab fa-youtube" style="color: #FF0000;"></i> YouTube URL
                </label>
                <input
                    type="url"
                    id="footer_youtube"
                    name="config[footer_youtube]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_youtube')->first()->config_value ?? '' }}"
                    placeholder="https://www.youtube.com/..."
                >
            </div>

            <div class="form-group">
                <label for="footer_facebook">
                    <i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook URL
                </label>
                <input
                    type="url"
                    id="footer_facebook"
                    name="config[footer_facebook]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_facebook')->first()->config_value ?? '' }}"
                    placeholder="https://www.facebook.com/..."
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="footer_twitter">
                    <i class="fab fa-twitter" style="color: #1DA1F2;"></i> Twitter URL
                </label>
                <input
                    type="url"
                    id="footer_twitter"
                    name="config[footer_twitter]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_twitter')->first()->config_value ?? '' }}"
                    placeholder="https://twitter.com/..."
                >
            </div>

            <div class="form-group">
                <label for="footer_blogspot">
                    <i class="fab fa-blogger" style="color: #FF5722;"></i> Blogspot URL
                </label>
                <input
                    type="url"
                    id="footer_blogspot"
                    name="config[footer_blogspot]"
                    class="config-input"
                    value="{{ $settings->where('config_key', 'footer_blogspot')->first()->config_value ?? '' }}"
                    placeholder="https://blogspot.com/..."
                >
            </div>
        </div>
    </div>
</div>

<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-file-contract"></i>
        <h3>Legal Documents</h3>
    </div>

    <div class="section-body">
        <div class="legal-documents-grid">
            <div class="legal-document-card">
                <div class="document-info">
                    <div class="document-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="document-details">
                        <h4>Privacy Policy</h4>
                        <p class="current-file">
                            @php
                                $privacyPolicy = $settings->where('config_key', 'footer_privacy_policy')->first();
                            @endphp
                            @if($privacyPolicy && $privacyPolicy->config_value)
                                <i class="fas fa-file-pdf"></i> {{ $privacyPolicy->config_value }}
                            @else
                                <span class="no-file">No file uploaded</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="document-upload">
                    <label for="legal_footer_privacy_policy" class="btn-upload-legal">
                        <i class="fas fa-upload"></i> Upload
                        <input
                            type="file"
                            id="legal_footer_privacy_policy"
                            name="legal_footer_privacy_policy"
                            accept=".pdf,.doc,.docx,.txt"
                            onchange="previewLegalFile('legal_footer_privacy_policy')"
                            hidden
                        >
                    </label>
                    <div class="file-name" id="preview_legal_footer_privacy_policy"></div>
                </div>
            </div>

            <div class="legal-document-card">
                <div class="document-info">
                    <div class="document-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="document-details">
                        <h4>Terms of Service</h4>
                        <p class="current-file">
                            @php
                                $termsService = $settings->where('config_key', 'footer_terms_of_service')->first();
                            @endphp
                            @if($termsService && $termsService->config_value)
                                <i class="fas fa-file-pdf"></i> {{ $termsService->config_value }}
                            @else
                                <span class="no-file">No file uploaded</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="document-upload">
                    <label for="legal_footer_terms_of_service" class="btn-upload-legal">
                        <i class="fas fa-upload"></i> Upload
                        <input
                            type="file"
                            id="legal_footer_terms_of_service"
                            name="legal_footer_terms_of_service"
                            accept=".pdf,.doc,.docx,.txt"
                            onchange="previewLegalFile('legal_footer_terms_of_service')"
                            hidden
                        >
                    </label>
                    <div class="file-name" id="preview_legal_footer_terms_of_service"></div>
                </div>
            </div>
        </div>

        <div class="legal-notes">
            <p><i class="fas fa-info-circle"></i> Allowed file types: PDF, DOC, DOCX, TXT (Max: 5MB)</p>
            <p><i class="fas fa-folder-open"></i> Files are saved to: <code>uploads/Legal Documents/</code></p>
        </div>
    </div>
</div>
