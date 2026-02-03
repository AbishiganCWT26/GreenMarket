@extends('admin.layouts.admin_master')

@section('title', 'User Management')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/index-user-management.css') }}">
    <style>
        :root{
            --primary-green:#10B981;
            --dark-green:#059669;
            --card-bg:#ffffff;
            --body-bg:#f6f8fa;
            --text-dark:#0f1724;
            --muted:#6b7280;
            --sidebar-width:240px;
            --shadow-xs:0 1px 3px rgba(15,23,36,0.04);
            --shadow-sm:0 4px 10px rgba(15,23,36,0.06);
            --shadow-md:0 7px 15px rgba(15,23,36,0.08);
            --accent-amber:#f59e0b;
            --blue:#3b82f6;
            --purple:#8b5cf6;
            --yellow:#f59e0b;
            --border-color:#e5e7eb;
            --focus-shadow:rgba(16, 185, 129, 0.1);
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:"Poppins", sans-serif;
        }

        body{
            background:var(--body-bg);
            color:var(--text-dark);
            font-size:13px;
            min-height:100vh;
        }

        .container{
            max-width:100%;
            padding:10px;
        }

        .header{
            background:white;
            border-radius:10px;
            padding:15px;
            margin-bottom:15px;
            box-shadow:var(--shadow-sm);
            border:1px solid var(--border-color);
        }

        .header-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:15px;
            flex-wrap:wrap;
            gap:10px;
        }

        .title-section h1{
            font-size:18px;
            font-weight:600;
            color:var(--text-dark);
            display:flex;
            align-items:center;
            gap:8px;
        }

        .title-section h1 i{
            color:var(--primary-green);
        }

        .stats-cards{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
            gap:10px;
            margin:15px 0;
        }

        .stat-card{
            background:white;
            border-radius:8px;
            padding:12px;
            text-align:center;
            box-shadow:var(--shadow-xs);
            border:1px solid var(--border-color);
            transition:transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover{
            transform:translateY(-2px);
            box-shadow:var(--shadow-sm);
        }

        .stat-card i{
            font-size:20px;
            margin-bottom:5px;
        }

        .stat-card.total i{ color:var(--primary-green); }
        .stat-card.active i{ color:#22c55e; }
        .stat-card.inactive i{ color:#ef4444; }
        .stat-card.admins i{ color:var(--blue); }

        .stat-card .number{
            font-size:20px;
            font-weight:600;
            display:block;
            margin:5px 0;
        }

        .stat-card .label{
            font-size:11px;
            color:var(--muted);
            text-transform:uppercase;
            letter-spacing:0.5px;
        }

        .controls{
            display:flex;
            gap:8px;
            align-items:center;
            flex-wrap:wrap;
        }



        .search-box{
            position:relative;
            min-width:200px;
        }

        .search-box i{
            position:absolute;
            left:10px;
            top:50%;
            transform:translateY(-50%);
            color:var(--muted);
            font-size:14px;
        }

        .search-box input{
            width:100%;
            padding:8px 8px 8px 32px;
            border:1px solid var(--border-color);
            border-radius:6px;
            font-size:13px;
            transition:border-color 0.2s;
        }

        .search-box input:focus{
            outline:none;
            border-color:var(--primary-green);
            box-shadow:0 0 0 3px var(--focus-shadow);
        }

        .btn{
            padding:8px 14px;
            border:none;
            border-radius:6px;
            font-size:13px;
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
            transition:all 0.2s;
            font-weight:500;
        }

        .btn-primary{
            background:var(--primary-green);
            color:white;
        }

        .btn-primary:hover{
            background:var(--dark-green);
            transform:translateY(-1px);
            box-shadow:0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .content-area{
            background:white;
            border-radius:10px;
            padding:15px;
            box-shadow:var(--shadow-sm);
            border:1px solid var(--border-color);
            min-height:400px;
        }

        .loading{
            text-align:center;
            padding:40px;
            color:var(--muted);
        }

        .loading i{
            font-size:24px;
            margin-bottom:10px;
            color:var(--primary-green);
        }

        .pagination-container{
            margin-top:15px;
            display:flex;
            justify-content:center;
        }

        .pagination{
            display:flex;
            gap:5px;
            list-style:none;
        }

        .pagination li{
            min-width:32px;
            height:32px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:6px;
            font-size:13px;
            cursor:pointer;
            transition:all 0.2s;
        }

        .pagination li a{
            color:var(--muted);
            text-decoration:none;
            width:100%;
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .pagination li.active{
            background:var(--primary-green);
        }

        .pagination li.active a{
            color:white;
        }

        .pagination li:not(.active):hover{
            background:var(--body-bg);
        }

        .pagination li.disabled{
            opacity:0.5;
            cursor:not-allowed;
        }

        .empty-state{
            text-align:center;
            padding:50px 20px;
            color:var(--muted);
        }

        .empty-state i{
            font-size:40px;
            margin-bottom:15px;
            opacity:0.5;
        }

        .swal2-popup{
            font-size:13px !important;
            border-radius:10px !important;
            border:1px solid var(--border-color) !important;
            box-shadow:var(--shadow-md) !important;
        }

        .swal2-title{
            font-size:18px !important;
            font-weight:600 !important;
            color:var(--text-dark) !important;
        }

        .swal2-content{
            font-size:13px !important;
            color:var(--muted) !important;
        }

        .swal2-confirm{
            background-color:var(--primary-green) !important;
            border:none !important;
            font-size:13px !important;
            padding:8px 20px !important;
            border-radius:6px !important;
        }

        .swal2-cancel{
            background-color:#6b7280 !important;
            border:none !important;
            font-size:13px !important;
            padding:8px 20px !important;
            border-radius:6px !important;
        }

        .swal2-image{
            border-radius:8px !important;
            max-width:250px !important;
            margin:10px auto !important;
        }

        .modal-overlay{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.5);
            z-index:1000;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
        }

        .modal-dialog{
            background:white;
            border-radius:12px;
            max-width:500px;
            width:100%;
            box-shadow:var(--shadow-md);
            animation:modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp{
            from{ transform:translateY(20px); opacity:0; }
            to{ transform:translateY(0); opacity:1; }
        }

        .modal-header{
            padding:15px 20px;
            border-bottom:1px solid var(--border-color);
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .modal-header h3{
            font-size:16px;
            font-weight:600;
            color:var(--text-dark);
            display:flex;
            align-items:center;
            gap:8px;
        }

        .modal-close{
            background:none;
            border:none;
            font-size:18px;
            color:var(--muted);
            cursor:pointer;
            padding:5px;
            border-radius:4px;
            transition:all 0.2s;
        }

        .modal-close:hover{
            background:var(--body-bg);
            color:var(--text-dark);
        }

        .modal-body{
            padding:20px;
        }

        .deletion-options{
            display:grid;
            gap:12px;
            margin:20px 0;
        }

        .option-card{
            background:var(--body-bg);
            border:2px solid var(--border-color);
            border-radius:8px;
            padding:15px;
            cursor:pointer;
            transition:all 0.2s;
            display:flex;
            gap:12px;
            align-items:flex-start;
        }

        .option-card:hover{
            border-color:var(--primary-green);
            background:white;
            transform:translateY(-2px);
            box-shadow:var(--shadow-sm);
        }

        .option-card.selected{
            border-color:var(--primary-green);
            background:rgba(16, 185, 129, 0.05);
        }

        .option-icon{
            width:40px;
            height:40px;
            border-radius:8px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .option-card[data-action="delete_all"] .option-icon{
            background:var(--danger-bg);
            color:var(--danger-color);
        }

        .option-card[data-action="transfer"] .option-icon{
            background:var(--info-bg);
            color:var(--info-color);
        }

        .option-content{
            flex:1;
        }

        .option-content h4{
            font-size:14px;
            font-weight:600;
            margin-bottom:4px;
            color:var(--text-dark);
        }

        .option-content p{
            font-size:12px;
            color:var(--muted);
            line-height:1.4;
        }

        .transfer-select{
            margin-top:10px;
        }

        .form-select{
            width:100%;
            padding:8px 12px;
            border:1px solid var(--border-color);
            border-radius:6px;
            font-size:13px;
            background:white;
            transition:border-color 0.2s;
        }

        .form-select:focus{
            outline:none;
            border-color:var(--primary-green);
            box-shadow:0 0 0 3px var(--focus-shadow);
        }

        .modal-actions{
            display:flex;
            gap:10px;
            justify-content:flex-end;
            margin-top:20px;
            padding-top:20px;
            border-top:1px solid var(--border-color);
        }

        .btn-secondary{
            padding:8px 16px;
            background:#6b7280;
            color:white;
            border:none;
            border-radius:6px;
            font-size:13px;
            cursor:pointer;
            transition:all 0.2s;
            font-weight:500;
        }

        .btn-secondary:hover{
            background:#4b5563;
        }

        @media (max-width:768px){
            .header-top{
                flex-direction:column;
                align-items:stretch;
            }
            
            .controls{
                width:100%;
            }
            
            .search-box{
                width:100%;
            }
            
            .stats-cards{
                grid-template-columns:repeat(2, 1fr);
            }
            
            .modal-dialog{
                max-width:90%;
                margin:auto;
            }
        }

        @media (max-width:480px){
            .stats-cards{
                grid-template-columns:1fr;
            }
            
            .container{
                padding:5px;
            }
            
            .header, .content-area{
                padding:10px;
            }
            
            .modal-body{
                padding:15px;
            }
            
            .modal-actions{
                flex-direction:column;
            }
            
            .modal-actions button{
                width:100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="header">
            <div class="header-top">
                <div class="title-section">
                    <h1><i class="fas fa-users-cog"></i> User Management</h1>
                </div>
                <div class="controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search users..." autocomplete="off">
                    </div>

                    <button class="btn btn-primary" id="add-user-btn">
                        <i class="fas fa-user-plus"></i> Add User
                    </button>
                </div>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card total">
                    <i class="fas fa-users"></i>
                    <span class="number">{{ $totalUsers }}</span>
                    <span class="label">Total Users</span>
                </div>
                <div class="stat-card active">
                    <i class="fas fa-user-check"></i>
                    <span class="number">{{ $activeUsers }}</span>
                    <span class="label">Active</span>
                </div>
                <div class="stat-card inactive">
                    <i class="fas fa-user-slash"></i>
                    <span class="number">{{ $inactiveUsers }}</span>
                    <span class="label">Inactive</span>
                </div>
                <div class="stat-card admins">
                    <i class="fas fa-user-shield"></i>
                    <span class="number">{{ $adminUsers }}</span>
                    <span class="label">Admins</span>
                </div>
            </div>
        </div>

        <div class="content-area">
            <div id="users-content">
                @include('admin.users.partials.user_cards', ['users' => $users])
            </div>
            
            <div id="loading" class="loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading users...</p>
            </div>
            
            <div class="pagination-container" id="pagination-container">
                {!! $paginator->links('vendor.pagination.simple-unique1') !!}
            </div>
        </div>
    </div>

    <div id="leadFarmerDeletionModal" class="modal-overlay" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Lead Farmer Deletion</h3>
                    <button class="modal-close" id="closeLeadFarmerModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="leadFarmerModalContent">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentView = 'card';
            let currentPage = 1;
            let searchTerm = '';
            let loading = false;

            function updateStatsCards(stats) {
                if (stats) {
                    $('.stat-card.total .number').text(stats.total || 0);
                    $('.stat-card.active .number').text(stats.active || 0);
                    $('.stat-card.inactive .number').text(stats.inactive || 0);
                    $('.stat-card.admins .number').text(stats.admins || 0);
                }
            }

            updateStatsCards({
                total: {{ $totalUsers }},
                active: {{ $activeUsers }},
                inactive: {{ $inactiveUsers }},
                admins: {{ $adminUsers }}
            });

            function showLoading(show) {
                loading = show;
                if (show) {
                    $('#loading').show();
                    $('#users-content').hide();
                } else {
                    $('#loading').hide();
                    $('#users-content').show();
                }
            }

            function loadUsers(page = 1, search = '') {
                if (loading) return;
                
                showLoading(true);
                currentPage = page;
                searchTerm = search;

                $.ajax({
                    url: "{{ route('admin.users.index') }}",
                    method: 'GET',
                    data: {
                        view: currentView,
                        q: search,
                        page: page
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success && response.html) {
                            $('#users-content').html(response.html);
                            if (response.pagination) {
                                $('#pagination-container').html(response.pagination);
                            }
                            // Update stats cards with response data
                            if (response.stats) {
                                updateStatsCards(response.stats);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid response format',
                                confirmButtonColor: '#10B981'
                            });
                        }
                        showLoading(false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load users',
                            confirmButtonColor: '#10B981'
                        });
                        showLoading(false);
                    }
                });
            }

            function showAddUserModal() {
                Swal.fire({
                    title: 'Add New User',
                    html: `
                        <div class="user-form">
                            <div class="form-group">
                                <label>User Type <span class="required">*</span></label>
                                <select id="user-type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="farmer">Farmer</option>
                                    <option value="lead_farmer">Lead Farmer</option>
                                    <option value="buyer">Buyer</option>
                                    <option value="facilitator">Facilitator</option>
                                    <option value="admin">Administrator</option>
                                    <option value="subadmin">Sub Administrator</option>
                                </select>
                            </div>
                            <div id="role-specific-fields" style="display:none;"></div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Create User',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6b7280',
                    width: '500px',
                    preConfirm: function() {
                        const userType = $('#user-type').val();
                        const name = $('#name').val();
                        const username = $('#username').val();
                        const email = $('#email').val();
                        const password = $('#password').val();
                        const passwordConfirmation = $('#password_confirmation').val();

                        if (!userType || !name || !username || !password) {
                            Swal.showValidationMessage('Please fill all required fields');
                            return false;
                        }

                        if (password !== passwordConfirmation) {
                            Swal.showValidationMessage('Passwords do not match');
                            return false;
                        }

                        if (password.length < 8) {
                            Swal.showValidationMessage('Password must be at least 8 characters');
                            return false;
                        }

                        if (!/(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(password)) {
                            Swal.showValidationMessage('Password must include uppercase, number and special character');
                            return false;
                        }

                        const formData = {
                            user_type: userType,
                            name: name,
                            username: username,
                            email: email,
                            password: password,
                            password_confirmation: passwordConfirmation
                        };

                        if (userType === 'farmer') {
                            formData.nic_no = $('#farmer_nic').val() || '';
                            formData.primary_mobile = $('#farmer_mobile').val() || '';
                            formData.whatsapp_number = $('#farmer_whatsapp').val() || '';
                            formData.residential_address = $('#farmer_address').val() || '';
                            formData.grama_niladhari_division = $('#farmer_gnd').val() || '';
                            formData.district = $('#farmer_district').val() || 'Colombo';
                            formData.preferred_payment = $('#farmer_payment').val() || 'bank';
                            
                            // Get payment method
                            const paymentMethod = $('#farmer_payment').val();
                            
                            // Validate based on payment method
                            if (paymentMethod === 'bank' || paymentMethod === 'all') {
                                const account = $('#farmer_account').val() || '';
                                const accountName = $('#farmer_account_name').val() || '';
                                const bankName = $('#farmer_bank').val() || '';
                                const bankBranch = $('#farmer_branch').val() || '';
                                
                                if (!account) {
                                    Swal.showValidationMessage('Account Number is required for Bank Transfer');
                                    return false;
                                }
                                if (!accountName) {
                                    Swal.showValidationMessage('Account Holder Name is required for Bank Transfer');
                                    return false;
                                }
                                if (!bankName) {
                                    Swal.showValidationMessage('Bank Name is required for Bank Transfer');
                                    return false;
                                }
                                if (!bankBranch) {
                                    Swal.showValidationMessage('Bank Branch is required for Bank Transfer');
                                    return false;
                                }
                                
                                formData.account_number = account;
                                formData.account_holder_name = accountName;
                                formData.bank_name = bankName;
                                formData.bank_branch = bankBranch;
                            }
                            
                            if (paymentMethod === 'ezcash' || paymentMethod === 'all') {
                                const ezcashMobile = $('#farmer_ezcash').val() || '';
                                if (!ezcashMobile) {
                                    Swal.showValidationMessage('EzCash Mobile Number is required for EzCash payment');
                                    return false;
                                }
                                formData.ezcash_mobile = ezcashMobile;
                            }
                            
                            if (paymentMethod === 'mcash' || paymentMethod === 'all') {
                                const mcashMobile = $('#farmer_mcash').val() || '';
                                if (!mcashMobile) {
                                    Swal.showValidationMessage('mCash Mobile Number is required for mCash payment');
                                    return false;
                                }
                                formData.mcash_mobile = mcashMobile;
                            }
                            
                            // Set optional fields if they exist
                            if ($('#farmer_ezcash').val()) formData.ezcash_mobile = $('#farmer_ezcash').val();
                            if ($('#farmer_mcash').val()) formData.mcash_mobile = $('#farmer_mcash').val();                   
                        } else if (userType === 'lead_farmer') {
                            formData.nic_no = $('#lead_nic').val() || '';
                            formData.primary_mobile = $('#lead_mobile').val() || '';
                            formData.whatsapp_number = $('#lead_whatsapp').val() || '';
                            formData.residential_address = $('#lead_address').val() || '';
                            formData.residential_address = $('#lead_address').val() || '';
                            formData.grama_niladhari_division = $('#lead_gnd').val() || '';
                            formData.district = $('#lead_district').val() || 'Colombo';
                            formData.group_name = $('#lead_group_name').val() || '';
                            formData.group_number = $('#lead_group_number').val() || '';
                            formData.account_number = $('#lead_account').val() || '';
                            formData.account_holder_name = $('#lead_account_name').val() || '';
                            formData.bank_name = $('#lead_bank').val() || '';
                            formData.bank_branch = $('#lead_branch').val() || '';
                        } else if (userType === 'buyer') {
                            formData.nic_no = $('#buyer_nic').val() || '';
                            formData.primary_mobile = $('#buyer_mobile').val() || '';
                            formData.whatsapp_number = $('#buyer_whatsapp').val() || '';
                            formData.residential_address = $('#buyer_address').val() || '';
                            formData.business_name = $('#buyer_business').val() || '';
                            formData.business_type = $('#buyer_type').val() || 'individual';
                        } else if (userType === 'facilitator') {
                            formData.nic_no = $('#facilitator_nic').val() || '';
                            formData.primary_mobile = $('#facilitator_mobile').val() || '';
                            formData.whatsapp_number = $('#facilitator_whatsapp').val() || '';
                            formData.assigned_division = $('#facilitator_division').val() || '';
                        } else if (userType === 'admin' || userType === 'subadmin') {
                            formData.nic_no = $('#admin_nic').val() || '';
                            formData.phone_number = $('#admin_phone').val() || '';
                        }

                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = result.value;
                        
                        $.ajax({
                            url: "{{ route('admin.users.store') }}",
                            method: 'POST',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        confirmButtonColor: '#10B981'
                                    }).then(() => {
                                        loadUsers();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#10B981'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let error = 'Failed to create user';
                                
                                if (xhr.responseJSON?.message) {
                                    error = xhr.responseJSON.message;

                                    // Parse database error messages
                                    if (error.includes('farmers_nic_no_key') && error.includes('already exists')) {
                                        error = error.replace(/.*ERROR:.*DETAIL: Key \(nic_no\)=\(([^)]+)\).*/s, 'NIC No. "$1" already exists')
                                                    .replace(/.*failed to create user because nic no\. "([^"]+)" already exists.*/i, 'NIC No. "$1" already exists');
                                    }
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '<span style="color: #1f2937; font-weight: 700;">Submission Failed</span>',
                                    html: `
                                        <div style="text-align: center; background-color: #fef2f2; border: 4px solid #ef4444; padding: 16px; border-radius: 20px;">
                                            <p style="margin: 0; color: #b91c1c; font-size: 14px; font-weight: 600; line-height: 1.5;">
                                                ${error}
                                            </p>
                                            <p style="margin: 8px 0 0 0; color: #7f1d1d; font-size: 12px; opacity: 0.8;">
                                                Please check the details and try again.
                                            </p>
                                        </div>
                                    `,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#10B981',
                                    background: '#ffffff',
                                    width: '450px',
                                    padding: '1.5rem',
                                    customClass: {
                                        popup: 'rounded-lg'
                                    }
                                });
                            }
                        });
                    }
                });

                $('#user-type').on('change', function() {
                    const userType = $(this).val();
                    let html = '';
                    
                    switch(userType) {
                        case 'farmer':
                            html = `
                                <div class="form-section">
                                    <h4>Farmer Details</h4>
                                    <div id="common-fields">
                                        <div class="form-group">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" id="name" class="form-input" placeholder="Enter full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="required">*</span></label>
                                            <input type="text" id="username" class="form-input" placeholder="Enter username" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="email" class="form-input" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password" class="form-input" placeholder="Enter password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                                            </div>
                                            <small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="form-group">
                                        <label>NIC Number <span class="required">*</span></label>
                                        <input type="text" id="farmer_nic" class="form-input" placeholder="Enter NIC" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Primary Mobile <span class="required">*</span></label>
                                        <input type="tel" id="farmer_mobile" class="form-input" placeholder="Enter mobile number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>WhatsApp Number</label>
                                        <input type="tel" id="farmer_whatsapp" class="form-input" placeholder="Enter WhatsApp">
                                    </div>
                                    <div class="form-group">
                                        <label>Residential Address <span class="required">*</span></label>
                                        <textarea id="farmer_address" class="form-input" placeholder="Enter address" rows="2" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Grama Niladhari Division <span class="required">*</span></label>
                                        <input type="text" id="farmer_gnd" class="form-input" placeholder="Enter GND" required>
                                    </div>
                                    <div class="form-group">
                                        <label>District <span class="required">*</span></label>
                                        <select id="farmer_district" class="form-select" required>
                                            <option value="" disabled selected>Select your district</option>
                                            <option value="Ampara">Ampara</option>
                                            <option value="Anuradhapura">Anuradhapura</option>
                                            <option value="Badulla">Badulla</option>
                                            <option value="Batticaloa">Batticaloa</option>
                                            <option value="Colombo">Colombo</option>
                                            <option value="Galle">Galle</option>
                                            <option value="Gampaha">Gampaha</option>
                                            <option value="Hambantota">Hambantota</option>
                                            <option value="Jaffna">Jaffna</option>
                                            <option value="Kalutara">Kalutara</option>
                                            <option value="Kandy">Kandy</option>
                                            <option value="Kegalle">Kegalle</option>
                                            <option value="Kilinochchi">Kilinochchi</option>
                                            <option value="Kurunegala">Kurunegala</option>
                                            <option value="Mannar">Mannar</option>
                                            <option value="Matale">Matale</option>
                                            <option value="Matara">Matara</option>
                                            <option value="Moneragala">Moneragala</option>
                                            <option value="Mullaitivu">Mullaitivu</option>
                                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                                            <option value="Polonnaruwa">Polonnaruwa</option>
                                            <option value="Puttalam">Puttalam</option>
                                            <option value="Ratnapura">Ratnapura</option>
                                            <option value="Trincomalee">Trincomalee</option>
                                            <option value="Vavuniya">Vavuniya</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Preferred Payment <span class="required">*</span></label>
                                        <select id="farmer_payment" class="form-select" required>
                                            <option value="bank">Bank Transfer</option>
                                            <option value="ezcash">EzCash</option>
                                            <option value="mcash">mCash</option>
                                            <option value="all">All Methods</option>
                                        </select>
                                    </div>
                                    <div id="farmer-bank-fields">
                                        <div class="form-group">
                                            <label>Account Number <span id="account-required" class="required" style="display:none;">*</span></label>
                                            <input type="text" id="farmer_account" class="form-input" placeholder="Enter account number">
                                        </div>
                                        <div class="form-group">
                                            <label>Account Holder Name <span id="account-name-required" class="required" style="display:none;">*</span></label>
                                            <input type="text" id="farmer_account_name" class="form-input" placeholder="Enter holder name">
                                        </div>
                                        <div class="form-group">
                                            <label>Bank Name <span id="bank-required" class="required" style="display:none;">*</span></label>
                                            <input type="text" id="farmer_bank" class="form-input" placeholder="Enter bank name">
                                        </div>
                                        <div class="form-group">
                                            <label>Bank Branch <span id="branch-required" class="required" style="display:none;">*</span></label>
                                            <input type="text" id="farmer_branch" class="form-input" placeholder="Enter branch">
                                        </div>
                                    </div>
                                    <div id="farmer-ezcash-fields" style="display:none;">
                                        <div class="form-group">
                                            <label>EzCash Mobile Number <span id="ezcash-required" class="required" style="display:none;">*</span></label>
                                            <input type="tel" id="farmer_ezcash" class="form-input" placeholder="Enter EzCash mobile">
                                        </div>
                                    </div>
                                    <div id="farmer-mcash-fields" style="display:none;">
                                        <div class="form-group">
                                            <label>mCash Mobile Number <span id="mcash-required" class="required" style="display:none;">*</span></label>
                                            <input type="tel" id="farmer_mcash" class="form-input" placeholder="Enter mCash mobile">
                                        </div>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'lead_farmer':
                            html = `
                                <div class="form-section">
                                    <h4>Lead Farmer Details</h4>

                                    <div id="common-fields">
                                        <div class="form-group">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" id="name" class="form-input" placeholder="Enter full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="required">*</span></label>
                                            <input type="text" id="username" class="form-input" placeholder="Enter username" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="email" class="form-input" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password" class="form-input" placeholder="Enter password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                                            </div>
                                            <small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Full Name <span class="required">*</span></label>
                                        <input type="text" id="lead_name" class="form-input" placeholder="Enter full name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>NIC Number <span class="required">*</span></label>
                                        <input type="text" id="lead_nic" class="form-input" placeholder="Enter NIC" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Primary Mobile <span class="required">*</span></label>
                                        <input type="tel" id="lead_mobile" class="form-input" placeholder="Enter mobile number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>WhatsApp Number</label>
                                        <input type="tel" id="lead_whatsapp" class="form-input" placeholder="Enter WhatsApp">
                                    </div>
                                    <div class="form-group">
                                        <label>Residential Address <span class="required">*</span></label>
                                        <textarea id="lead_address" class="form-input" placeholder="Enter address" rows="2" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Grama Niladhari Division</label>
                                        <input type="text" id="lead_gnd" class="form-input" placeholder="Enter GND">
                                    </div>
                                    <div class="form-group">
                                        <label>District <span class="required">*</span></label>
                                        <select id="lead_district" class="form-select" required>
                                            <option value="" disabled selected>Select your district</option>
                                            <option value="Ampara">Ampara</option>
                                            <option value="Anuradhapura">Anuradhapura</option>
                                            <option value="Badulla">Badulla</option>
                                            <option value="Batticaloa">Batticaloa</option>
                                            <option value="Colombo">Colombo</option>
                                            <option value="Galle">Galle</option>
                                            <option value="Gampaha">Gampaha</option>
                                            <option value="Hambantota">Hambantota</option>
                                            <option value="Jaffna">Jaffna</option>
                                            <option value="Kalutara">Kalutara</option>
                                            <option value="Kandy">Kandy</option>
                                            <option value="Kegalle">Kegalle</option>
                                            <option value="Kilinochchi">Kilinochchi</option>
                                            <option value="Kurunegala">Kurunegala</option>
                                            <option value="Mannar">Mannar</option>
                                            <option value="Matale">Matale</option>
                                            <option value="Matara">Matara</option>
                                            <option value="Moneragala">Moneragala</option>
                                            <option value="Mullaitivu">Mullaitivu</option>
                                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                                            <option value="Polonnaruwa">Polonnaruwa</option>
                                            <option value="Puttalam">Puttalam</option>
                                            <option value="Ratnapura">Ratnapura</option>
                                            <option value="Trincomalee">Trincomalee</option>
                                            <option value="Vavuniya">Vavuniya</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Group Name <span class="required">*</span></label>
                                        <input type="text" id="lead_group_name" class="form-input" placeholder="Enter group name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Group Number <span class="required">*</span></label>
                                        <input type="text" id="lead_group_number" class="form-input" placeholder="Enter group number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Account Number <span class="required">*</span></label>
                                        <input type="text" id="lead_account" class="form-input" placeholder="Enter account number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Account Holder Name <span class="required">*</span></label>
                                        <input type="text" id="lead_account_name" class="form-input" placeholder="Enter holder name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank Name <span class="required">*</span></label>
                                        <input type="text" id="lead_bank" class="form-input" placeholder="Enter bank name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank Branch <span class="required">*</span></label>
                                        <input type="text" id="lead_branch" class="form-input" placeholder="Enter branch" required>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'buyer':
                            html = `
                                <div class="form-section">
                                    <h4>Buyer Details</h4>

                                    <div id="common-fields">
                                        <div class="form-group">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" id="name" class="form-input" placeholder="Enter full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="required">*</span></label>
                                            <input type="text" id="username" class="form-input" placeholder="Enter username" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="email" class="form-input" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password" class="form-input" placeholder="Enter password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                                            </div>
                                            <small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>NIC Number</label>
                                        <input type="text" id="buyer_nic" class="form-input" placeholder="Enter NIC">
                                    </div>
                                    <div class="form-group">
                                        <label>Primary Mobile <span class="required">*</span></label>
                                        <input type="tel" id="buyer_mobile" class="form-input" placeholder="Enter mobile number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>WhatsApp Number</label>
                                        <input type="tel" id="buyer_whatsapp" class="form-input" placeholder="Enter WhatsApp">
                                    </div>
                                    <div class="form-group">
                                        <label>Residential Address</label>
                                        <textarea id="buyer_address" class="form-input" placeholder="Enter address" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Business Name</label>
                                        <input type="text" id="buyer_business" class="form-input" placeholder="Enter business name">
                                    </div>
                                    <div class="form-group">
                                        <label>Business Type</label>
                                        <select id="buyer_type" class="form-select">
                                            <option value="individual">Individual</option>
                                            <option value="restaurant">Restaurant</option>
                                            <option value="hotel">Hotel</option>
                                            <option value="retailer">Retailer</option>
                                            <option value="wholesaler">Wholesaler</option>
                                        </select>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'facilitator':
                            html = `
                                <div class="form-section">
                                    <h4>Facilitator Details</h4>

                                    <div id="common-fields">
                                        <div class="form-group">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" id="name" class="form-input" placeholder="Enter full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="required">*</span></label>
                                            <input type="text" id="username" class="form-input" placeholder="Enter username" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="email" class="form-input" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password" class="form-input" placeholder="Enter password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                                            </div>
                                            <small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>NIC Number <span class="required">*</span></label>
                                        <input type="text" id="facilitator_nic" class="form-input" placeholder="Enter NIC" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Primary Mobile <span class="required">*</span></label>
                                        <input type="tel" id="facilitator_mobile" class="form-input" placeholder="Enter mobile number" required>
                                    </div>
                                    <div class="form-group">
                                        <label>WhatsApp Number</label>
                                        <input type="tel" id="facilitator_whatsapp" class="form-input" placeholder="Enter WhatsApp">
                                    </div>
                                    <div class="form-group">
                                        <label>Assigned Division <span class="required">*</span></label>
                                        <input type="text" id="facilitator_division" class="form-input" placeholder="Enter assigned division" required>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'admin':
                        case 'subadmin':
                            html = `
                                <div class="form-section">
                                    <h4>${userType === 'admin' ? 'Administrator' : 'Sub Administrator'} Details</h4>

                                    <div id="common-fields">
                                        <div class="form-group">
                                            <label>Full Name <span class="required">*</span></label>
                                            <input type="text" id="name" class="form-input" placeholder="Enter full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="required">*</span></label>
                                            <input type="text" id="username" class="form-input" placeholder="Enter username" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="email" class="form-input" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password" class="form-input" placeholder="Enter password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                                            </div>
                                            <small class="password-hint">Minimum 8 characters with uppercase, number & special character</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <div class="password-container">
                                                <input type="password" id="password_confirmation" class="form-input" placeholder="Confirm password" required>
                                                <i class="fa-regular fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>NIC Number</label>
                                        <input type="text" id="admin_nic" class="form-input" placeholder="Enter NIC">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number <span class="required">*</span></label>
                                        <input type="tel" id="admin_phone" class="form-input" placeholder="Enter phone number" required>
                                    </div>
                                </div>
                            `;
                            break;
                    }
                    
                    $('#role-specific-fields').html(html).show();
                    
                    if (userType === 'farmer') {
                        $('#farmer_payment').on('change', function() {
                            const payment = $(this).val();
                            
                            // Reset all required indicators
                            $('#account-required, #account-name-required, #bank-required, #branch-required, #ezcash-required, #mcash-required').hide();
                            
                            // Hide all fields first
                            $('#farmer-bank-fields, #farmer-ezcash-fields, #farmer-mcash-fields').hide();
                            
                            // Show fields and set required indicators based on payment method
                            if (payment === 'bank' || payment === 'all') {
                                $('#farmer-bank-fields').show();
                                $('#account-required, #account-name-required, #bank-required, #branch-required').show();
                            }
                            if (payment === 'ezcash' || payment === 'all') {
                                $('#farmer-ezcash-fields').show();
                                $('#ezcash-required').show();
                            }
                            if (payment === 'mcash' || payment === 'all') {
                                $('#farmer-mcash-fields').show();
                                $('#mcash-required').show();
                            }
                            
                            // For "All Methods", show all fields
                            if (payment === 'all') {
                                $('#farmer-bank-fields, #farmer-ezcash-fields, #farmer-mcash-fields').show();
                                $('#account-required, #account-name-required, #bank-required, #branch-required, #ezcash-required, #mcash-required').show();
                            }
                        }).trigger('change');
                    }
                });
            }

            window.togglePasswordVisibility = function(fieldId) {
                const field = $('#' + fieldId);
                const toggleIcon = field.next('.password-toggle');
                
                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    field.attr('type', 'password');
                    toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            $('#search-input').on('input', function() {
                const search = $(this).val();
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(() => {
                    loadUsers(1, search);
                }, 500));
            });

            $('#add-user-btn').click(showAddUserModal);

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                loadUsers(page, searchTerm);
            });

            $(document).on('click', '.action-btn', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name') || 'User';

                switch(action) {
                    case 'view':
                        window.location.href = `/admin/users/${userId}`;
                        break;
                    case 'edit':
                        window.location.href = `/admin/users/${userId}/edit`;
                        break;
                    case 'suspend':
                        Swal.fire({
                            title: 'Suspend User',
                            text: `Are you sure you want to suspend ${userName}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#10B981',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, suspend',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `/admin/users/${userId}/suspend`,
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Suspended!',
                                                text: response.message,
                                                confirmButtonColor: '#10B981'
                                            }).then(() => {
                                                loadUsers(currentPage, searchTerm);
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: response.message,
                                                confirmButtonColor: '#10B981'
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to suspend user',
                                            confirmButtonColor: '#10B981'
                                        });
                                    }
                                });
                            }
                        });
                        break;
                    case 'activate':
                        $.ajax({
                            url: `/admin/users/${userId}/activate`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Activated!',
                                        text: response.message,
                                        confirmButtonColor: '#10B981'
                                    }).then(() => {
                                        loadUsers(currentPage, searchTerm);
                                    });
                                }
                            }
                        });
                        break;
                    case 'delete':
                        handleDeleteUser(userId, userName);
                        break;
                    case 'promote':
                        Swal.fire({
                            title: 'Promote to Lead Farmer',
                            text: `Promote ${userName} to Lead Farmer role?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#10B981',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, promote',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `/admin/users/${userId}/promote`,
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Promoted!',
                                                text: response.message,
                                                confirmButtonColor: '#10B981'
                                            }).then(() => {
                                                loadUsers(currentPage, searchTerm);
                                            });
                                        }
                                    }
                                });
                            }
                        });
                        break;
                }
            });

            function handleDeleteUser(userId, userName) {
                Swal.fire({
                    title: 'Delete User',
                    html: `Are you sure you want to delete <strong>${userName}</strong>?<br><br>
                        <small class="text-muted">This action cannot be undone and all related data will be permanently deleted.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/users/${userId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        confirmButtonColor: '#10B981'
                                    }).then(() => {
                                        loadUsers(currentPage, searchTerm);
                                    });
                                } else if (response.requires_action) {
                                    showLeadFarmerDeletionModal(userId, response);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#10B981'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to delete user',
                                    confirmButtonColor: '#10B981'
                                });
                            }
                        });
                    }
                });
            }

            function showLeadFarmerDeletionModal(userId, response) {
                $.ajax({
                    url: `/admin/get-lead-farmers-for-transfer`,
                    method: 'GET',
                    success: function(leadFarmersResponse) {
                        let leadFarmersHtml = '';
                        if (leadFarmersResponse.leadFarmers && leadFarmersResponse.leadFarmers.length > 0) {
                            leadFarmersHtml = '<option value="">Select Lead Farmer</option>';
                            leadFarmersResponse.leadFarmers.forEach(function(leadFarmer) {
                                if (leadFarmer.id != response.lead_farmer_id) {
                                    leadFarmersHtml += `<option value="${leadFarmer.id}">${leadFarmer.name} - ${leadFarmer.group_name}</option>`;
                                }
                            });
                        }
                        
                        const modalHtml = `
                            <p>${response.message}</p>
                            <div class="deletion-options">
                                <div class="option-card" data-action="delete_all">
                                    <div class="option-icon">
                                        <i class="fas fa-trash"></i>
                                    </div>
                                    <div class="option-content">
                                        <h4>Delete All Farmers</h4>
                                        <p>Permanently delete all ${response.farmers_count || 0} farmers under this lead farmer</p>
                                    </div>
                                </div>
                                <div class="option-card" data-action="transfer">
                                    <div class="option-icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="option-content">
                                        <h4>Transfer Farmers</h4>
                                        <p>Transfer all farmers to another lead farmer</p>
                                        <div class="transfer-select" style="display:none; margin-top:10px;">
                                            <select id="newLeadFarmerSelect" class="form-select">
                                                ${leadFarmersHtml || '<option value="">No other lead farmers available</option>'}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-actions" style="margin-top:20px; display:none;" id="modalActions">
                                <button class="btn-secondary" id="cancelAction">Cancel</button>
                                <button class="btn-primary" id="confirmAction" disabled>Confirm</button>
                            </div>
                        `;
                        
                        $('#leadFarmerModalContent').html(modalHtml);
                        $('#leadFarmerDeletionModal').fadeIn();
                        
                        let selectedAction = '';
                        let newLeadFarmerId = '';
                        
                        $('.option-card').click(function() {
                            $('.option-card').removeClass('selected');
                            $(this).addClass('selected');
                            selectedAction = $(this).data('action');
                            $('#modalActions').show();
                            $('#confirmAction').prop('disabled', true);
                            
                            if (selectedAction === 'transfer') {
                                $(this).find('.transfer-select').show();
                                $('#newLeadFarmerSelect').on('change', function() {
                                    newLeadFarmerId = $(this).val();
                                    $('#confirmAction').prop('disabled', !newLeadFarmerId);
                                });
                            } else {
                                $('#confirmAction').prop('disabled', false);
                            }
                        });
                        
                        $('#confirmAction').click(function() {
                            if (!selectedAction) return;
                            
                            const data = {
                                action: selectedAction,
                                _token: '{{ csrf_token() }}'
                            };
                            
                            if (selectedAction === 'transfer' && newLeadFarmerId) {
                                data.new_lead_farmer_id = newLeadFarmerId;
                            }
                            
                            $.ajax({
                                url: `/admin/users/${userId}/process-deletion`,
                                method: 'POST',
                                data: data,
                                success: function(response) {
                                    if (response.success) {
                                        $('#leadFarmerDeletionModal').fadeOut();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: response.message,
                                            confirmButtonColor: '#10B981'
                                        }).then(() => {
                                            loadUsers(currentPage, searchTerm);
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message,
                                            confirmButtonColor: '#10B981'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON?.message || 'Failed to process deletion',
                                        confirmButtonColor: '#10B981'
                                    });
                                }
                            });
                        });
                        
                        $('#cancelAction, #closeLeadFarmerModal').click(function() {
                            $('#leadFarmerDeletionModal').fadeOut();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load lead farmers',
                            confirmButtonColor: '#10B981'
                        });
                    }
                });
            }

            $(document).on('click', '.view-photo', function(e) {
                e.preventDefault();
                const photoUrl = $(this).data('photo');
                Swal.fire({
                    imageUrl: photoUrl,
                    imageAlt: 'Profile Photo',
                    showConfirmButton: false,
                    showCloseButton: true,
                    width: '300px',
                    padding: '10px'
                });
            });

            updateActiveStats();
        });
    </script>
@endsection