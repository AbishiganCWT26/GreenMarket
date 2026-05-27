@if(count($users) > 0)
    <div class="cards-container">
        @foreach($users as $user)
            @php
                $statusClass = $user->is_active ? 'status-active' : 'status-inactive';
                $statusText = $user->is_active ? 'Active' : 'Inactive';
                $roleText = str_replace('_', ' ', ucfirst($user->role));
                $defaultAvatar = match($user->role) {
                    'buyer' => asset('uploads/profile_pictures/default-buyer.png'),
                    'farmer' => asset('assets/images/farmer.png'),
                    'admin' => asset('assets/images/Profiles/default-avatar.png'),
                    'lead_farmer' => asset('assets/images/Profiles/lead-farmer.png'),
                    'facilitator' => asset('assets/images/Profiles/facilitator.png'),
                    'delivery_rider' => asset('assets/images/Profiles/Delivery-Rider.png'),
                    default => asset('assets/images/Profiles/default-avatar.png')
                };

                $avatarUrl = $user->profile_photo && $user->profile_photo !== 'default-avatar.png' 
                    ? asset('uploads/profile_pictures/' . $user->profile_photo)
                    : $defaultAvatar;
                $contactInfo = $user->contact_number ?? 'N/A';
                $nicInfo = $user->nic_number ?? 'Not provied';
                $emailInfo = $user->email ?? 'No email';
                $createdDate = $user->created_at ? date('M d, Y', strtotime($user->created_at)) : 'N/A';
            @endphp
            <div class="user-card" data-user-id="{{ $user->id }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-role="{{ $user->role }}">
                <div class="card-header">
                    <div class="avatar">
                        <img src="{{ $avatarUrl }}" alt="{{ $user->display_name }}" 
                             class="view-photo"
                             data-photo="{{ $avatarUrl }}"
                             onerror="this.src='{{ $defaultAvatar }}';">
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <span title="{{ $user->display_name }}">{{ $user->display_name }}</span>
                            <span class="role-badge role-{{ $user->role }}">{{ $roleText }}</span>
                        </div>
                        <div class="user-email" title="{{ $emailInfo }}">{{ $emailInfo }}</div>
                        <div class="user-contact">
                            <i class="fas fa-phone"></i>
                            <span title="{{ $contactInfo }}">{{ $contactInfo }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-details">
                        @if($nicInfo)
                            <div class="detail-item">
                                <i class="fas fa-id-card"></i>
                                <span>NIC No: {{ $nicInfo }}</span>
                            </div>
                        @endif
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span>Registered: {{ $createdDate }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-circle"></i>
                            <span>Status: <span class="status-badge {{ $statusClass }}">
                                <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $statusText }}
                            </span></span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if($user->role === 'farmer')
                        <button class="action-btn action-view" data-action="view" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn action-promote" data-action="promote" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-star"></i> Promote
                        </button>
                        @if($user->is_active)
                            <button class="action-btn action-suspend" data-action="suspend" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                                <i class="fas fa-ban"></i> Suspend
                            </button>
                        @else
                            <button class="action-btn action-view" style="background:#22c55e;" data-action="activate" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                                <i class="fas fa-check"></i> Activate
                            </button>
                        @endif
                        <button class="action-btn action-delete" data-action="delete" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @else
                        <button class="action-btn action-view" data-action="view" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn action-edit" data-action="edit" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        @if($user->is_active)
                            <button class="action-btn action-suspend" data-action="suspend" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                                <i class="fas fa-ban"></i> Suspend
                            </button>
                        @else
                            <button class="action-btn action-view" style="background:#22c55e;" data-action="activate" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                                <i class="fas fa-check"></i> Activate
                            </button>
                        @endif
                        <button class="action-btn action-delete" data-action="delete" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <h3>No users found</h3>
        <p>Try adjusting your search criteria</p>
    </div>
@endif