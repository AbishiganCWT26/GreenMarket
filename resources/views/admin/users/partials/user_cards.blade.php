@if($users->count() > 0)
<div class="user-cards">
    @foreach($users as $user)
        @php
            if ($user->is_active) {
                $statusClass = 'status-active';
                $statusText = 'Active';
            } else {
                $statusClass = 'status-inactive';
                $statusText = 'Inactive';
            }

            // Define imageSrc here before using it
            $profilePhoto = $user->profile_photo ?? 'default-avatar.png';
            $imagePath = 'uploads/profile_pictures/' . $profilePhoto;
            $defaultPath = 'uploads/profile_pictures/default-avatar.png';

            // Check if the image exists in the public folder
            $fullPath = public_path('uploads/profile_pictures/' . $profilePhoto);
            $imageSrc = file_exists($fullPath) && !empty($profilePhoto) ? asset($imagePath) : asset($defaultPath);
        @endphp

        <div class="user-card" data-role="{{ $user->role }}">
            <div class="user-top">
                <div class="avatar" onclick="viewProfilePhoto('{{ addslashes($user->username) }}', '{{ $imageSrc }}', '{{ $user->role }}', '{{ addslashes($user->display_name ?? $user->username) }}')" style="cursor: pointer;" title="Click to view profile photo">
                    <img src="{{ $imageSrc }}" alt="{{ $user->username }}" class="profile-photo">
                </div>
                <div>
                    <div class="user-name">{{ $user->display_name ?? $user->username }}</div>
                    <div class="user-email">{{ $user->email ?? 'No email' }}</div>
                </div>
            </div>

            <div class="badges">
                <span class="role-badge role-{{ $user->role }}">
                    <i class="fas fa-user"></i>{{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>

                <span class="status-badge {{ $statusClass }}">
                    <i class="fas fa-circle"></i>{{ $statusText }}
                </span>
            </div>

            <div class="user-meta">
                <span class="meta-item">
                    <i class="fas fa-phone"></i> {{ $user->contact_number ?? 'N/A' }}
                </span>
                <span class="meta-item">
                    <i class="fas fa-clock"></i>
                    {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('M d, Y') : 'Never' }}
                </span>
            </div>

            <div class="card-actions">
                <button class="btn-action view view-user" data-id="{{ $user->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </button>

                {{-- Hide edit button for farmers and lead farmers --}}
                @if($user->role !== 'farmer' && $user->role !== 'lead_farmer')
                    <button class="btn-action edit edit-user" data-id="{{ $user->id }}" title="Edit User">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </button>
                @endif

                @if($user->role === 'farmer')
                    <button class="btn-action promote promote-user" data-id="{{ $user->id }}" title="Promote to Lead Farmer">
                        <i class="fas fa-user-plus"></i>
                        <span>Promote</span>
                    </button>
                @endif

                @if($user->role === 'admin')
                    <button class="btn-action make-subadmin" data-id="{{ $user->id }}" title="Make Sub-Admin">
                        <i class="fas fa-user-shield"></i>
                        <span>Make Sub-Admin</span>
                    </button>
                @endif

                @if($user->is_active)
                    <button class="btn-action suspend suspend-user" data-id="{{ $user->id }}" title="Suspend User">
                        <i class="fas fa-pause"></i>
                        <span>Suspend</span>
                    </button>
                @else
                    <button class="btn-action activate activate-user" data-id="{{ $user->id }}" title="Activate User">
                        <i class="fas fa-play"></i>
                        <span>Activate</span>
                    </button>
                @endif

                <button class="btn-action delete delete-user" data-id="{{ $user->id }}" title="Deactivate User">
                    <i class="fas fa-trash"></i>
                    <span>Delete</span>
                </button>
            </div>
        </div>
    @endforeach
</div>

@else
<div class="no-users">
    <i class="fas fa-users-slash"></i>
    <h3>No users found</h3>
    <p>Try adjusting your search query</p>
</div>
@endif

<script>
function viewProfilePhoto(username, imageSrc, userRole = '', displayName = '') {
    // Get user role for display
    const roleMap = {
        'admin': 'Administrator',
        'subadmin': 'Sub Administrator',
        'farmer': 'Farmer',
        'lead_farmer': 'Lead Farmer',
        'buyer': 'Buyer',
        'facilitator': 'Facilitator'
    };

    const roleDisplay = roleMap[userRole] || userRole;
    const nameDisplay = displayName || username;

    Swal.fire({
        title: `${nameDisplay}'s Profile`,
        html: `
            <div style="text-align: center;">
                <img src="${imageSrc}"
                     alt="${username}"
                     style="width: 200px; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; border: 3px solid #f0f0f0;">
                <div style="text-align: left; margin-top: 15px;">
                    <p><strong>Username:</strong> ${username}</p>
                    <p><strong>Name:</strong> ${nameDisplay}</p>
                    <p><strong>Role:</strong> ${roleDisplay}</p>
                </div>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    <i class="fas fa-external-link-alt"></i> Click image to view full size
                </p>
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: 500,
        padding: '25px',
        background: '#fff',
        backdrop: 'rgba(0,0,0,0.5)',
        customClass: {
            popup: 'profile-photo-popup'
        },
        didOpen: () => {
            // Make the image clickable to open in new tab
            const img = document.querySelector('.swal2-html-container img');
            if (img) {
                img.style.cursor = 'zoom-in';
                img.title = 'Click to view full size';
                img.addEventListener('click', () => {
                    window.open(imageSrc, '_blank');
                });
            }
        }
    });
}
</script>
