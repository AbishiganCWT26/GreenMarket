<div class="config-section-card">
    <div class="section-header">
        <i class="fas fa-envelope"></i>
        <h3>Admin Email</h3>
    </div>

    <div class="section-body">
        <div class="form-group">
            <label for="admin_email">
                <i class="fas fa-at"></i> Admin Email Address
            </label>
            <input
                type="email"
                id="admin_email"
                name="config[admin_email]"
                class="config-input"
                value="{{ $settings->where('config_key', 'admin_email')->first()->config_value ?? '' }}"
                placeholder="Enter admin email address..."
            >
        </div>
    </div>
</div>
