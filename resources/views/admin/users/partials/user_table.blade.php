@if(count($users) > 0)
    <div class="table-view-container">
        <table class="user-data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @php
                        $statusClass = $user->is_active ? 'table-status-active' : 'table-status-inactive';
                        $statusText = $user->is_active ? 'Active' : 'Inactive';
                        $roleText = str_replace('_', ' ', ucfirst($user->role));
                        $contactInfo = $user->contact_number ?? 'N/A';
                        $nicInfo = $user->nic_number ?? '';
                        $createdDate = $user->created_at ? date('M d, Y', strtotime($user->created_at)) : 'N/A';
                        
                        // Get avatar
                        $avatarUrl = $user->profile_photo && $user->profile_photo !== 'default-avatar.png' 
                            ? asset('uploads/profile_pictures/' . $user->profile_photo)
                            : '';
                        $initials = strtoupper(substr($user->display_name, 0, 2));
                    @endphp
                    <tr class="table-user-row" data-user-id="{{ $user->id }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-role="{{ $user->role }}">
                        <td class="user-id">{{ $user->id }}</td>
                        <td>
                            <div class="table-user-info">
                                <div class="table-avatar">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $user->display_name }}" 
                                             class="view-photo"
                                             data-photo="{{ $avatarUrl }}">
                                    @else
                                        <span class="table-avatar-initials">{{ $initials }}</span>
                                    @endif
                                </div>
                                <div class="table-user-details">
                                    <div class="table-user-name">{{ $user->display_name }}</div>
                                    <div class="table-user-email">{{ $user->email ?? 'No email' }}</div>
                                    @if($nicInfo)
                                        <div class="table-user-nic">NIC: {{ $nicInfo }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="user-contact">{{ $contactInfo }}</td>
                        <td>
                            <span class="table-role-badge table-role-{{ $user->role }}">{{ $roleText }}</span>
                        </td>
                        <td>
                            <span class="table-status {{ $statusClass }}">
                                <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="user-registered">{{ $createdDate }}</td>
                        <td>
                            <div class="table-action-buttons">
                                <button class="table-action-btn table-view" data-action="view" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="table-action-btn table-edit" data-action="edit" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->is_active)
                                    <button class="table-action-btn table-suspend" data-action="suspend" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="Suspend">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button class="table-action-btn table-activate" data-action="activate" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="Activate">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                @if($user->role === 'farmer')
                                    <button class="table-action-btn table-promote" data-action="promote" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="Promote">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
                                <button class="table-action-btn table-delete" data-action="delete" data-user-id="{{ $user->id }}" data-user-name="{{ $user->display_name }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <h3>No users found</h3>
        <p>Try adjusting your search criteria</p>
    </div>
@endif

<style>
    .table-view-container {
        overflow-x: auto;
        margin: 0;
        padding: 0;
    }
    
    .user-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        background: white;
    }
    
    .user-data-table th {
        background: #f8fafc;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
        position: sticky;
        top: 0;
    }
    
    .user-data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }
    
    .user-data-table tbody tr:hover {
        background: #f9fafb;
    }
    
    .table-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 200px;
    }
    
    .table-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .table-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .table-avatar-initials {
        font-weight: 600;
    }
    
    .table-user-details {
        min-width: 0;
        flex: 1;
    }
    
    .table-user-name {
        font-weight: 500;
        color: #111827;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    
    .table-user-email {
        font-size: 11px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    
    .table-user-nic {
        font-size: 11px;
        color: #8b5cf6;
        margin-top: 2px;
    }
    
    .table-role-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .table-role-farmer { background: #d1fae5; color: #065f46; }
    .table-role-lead_farmer { background: #fef3c7; color: #92400e; }
    .table-role-buyer { background: #dbeafe; color: #1e40af; }
    .table-role-facilitator { background: #ede9fe; color: #5b21b6; }
    .table-role-admin { background: #fee2e2; color: #991b1b; }
    .table-role-subadmin { background: #fce7f3; color: #9d174d; }
    
    .table-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .table-status-active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .table-status-inactive {
        background: #fef3c7;
        color: #92400e;
    }
    
    .table-action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap;
    }
    
    .table-action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: #f3f4f6;
        color: #4b5563;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .table-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .table-view:hover { background: #10B981; color: white; }
    .table-edit:hover { background: #3b82f6; color: white; }
    .table-suspend:hover { background: #f59e0b; color: white; }
    .table-activate:hover { background: #22c55e; color: white; }
    .table-promote:hover { background: #8b5cf6; color: white; }
    .table-delete:hover { background: #ef4444; color: white; }
    
    .user-id {
        font-weight: 600;
        color: #374151;
    }
    
    .user-contact, .user-registered {
        white-space: nowrap;
    }
    
    @media (max-width: 768px) {
        .user-data-table {
            font-size: 12px;
        }
        
        .user-data-table th,
        .user-data-table td {
            padding: 8px 10px;
        }
        
        .table-action-buttons {
            flex-direction: column;
            gap: 3px;
        }
        
        .table-action-btn {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }
        
        .table-user-name,
        .table-user-email {
            max-width: 120px;
        }
    }
    
    @media (max-width: 480px) {
        .table-view-container {
            margin: 0 -10px;
            width: calc(100% + 20px);
        }
        
        .user-data-table {
            min-width: 600px;
        }
    }
</style>