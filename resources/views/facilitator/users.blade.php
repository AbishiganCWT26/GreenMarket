@extends('facilitator.layouts.facilitator_master')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Facilitator/users.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="user-dashboard">
	<div class="user-container">
		<div class="stats-row">
			<div class="stat-item" onclick="filterByRole('farmer')">
				<div class="stat-icon green">
					<i class="fa-solid fa-tractor"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $userTypes['farmers'] ?? 0 }}</span>
					<span class="stat-label">Farmers</span>
				</div>
			</div>
			<div class="stat-item" onclick="filterByRole('lead_farmer')">
				<div class="stat-icon blue">
					<i class="fa-solid fa-user-tie"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $userTypes['lead_farmers'] ?? 0 }}</span>
					<span class="stat-label">Lead Farmers</span>
				</div>
			</div>
			<div class="stat-item" onclick="filterByRole('buyer')">
				<div class="stat-icon amber">
					<i class="fa-solid fa-cart-shopping"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $userTypes['buyers'] ?? 0 }}</span>
					<span class="stat-label">Buyers</span>
				</div>
			</div>
			<div class="stat-item" onclick="filterByRole('facilitator')">
				<div class="stat-icon purple">
					<i class="fa-solid fa-handshake-angle"></i>
				</div>
				<div class="stat-info">
					<span class="stat-value">{{ $userTypes['facilitators'] ?? 0 }}</span>
					<span class="stat-label">Facilitators</span>
				</div>
			</div>
		</div>

		<div class="action-bar">
			<div class="search-container">
				<input type="text" id="smartSearch" placeholder="Search by name, email, NIC, mobile..." autocomplete="off">
				<button type="button" id="searchBtn">
					<i class="fa-solid fa-magnifying-glass"></i>
				</button>
			</div>
			<div class="view-toggle">
				<button class="view-btn active" id="cardViewBtn" onclick="setViewMode('card')">
					<i class="fa-solid fa-grip"></i>
				</button>
				<button class="view-btn" id="tableViewBtn" onclick="setViewMode('table')">
					<i class="fa-solid fa-table"></i>
				</button>
			</div>
			<div class="filter-group">
				<select id="roleFilter" class="filter-select" onchange="applyFilters()">
					<option value="">All Roles</option>
					<option value="farmer">Farmer</option>
					<option value="lead_farmer">Lead Farmer</option>
					<option value="buyer">Buyer</option>
					<option value="facilitator">Facilitator</option>
				</select>
				<select id="statusFilter" class="filter-select" onchange="applyFilters()">
					<option value="">All Status</option>
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
			</div>
			<button class="btn-refresh" onclick="refreshUsers()">
				<i class="fa-solid fa-rotate"></i>
			</button>
		</div>

		<div id="usersContainer">
			<div class="users-grid" id="usersGrid"></div>
			<div class="table-view" id="tableView" style="display: none;"></div>
		</div>

		<div class="pagination-wrapper" id="paginationWrapper"></div>
	</div>
</div>

<div id="userProfileModal"></div>
<div id="editUserModal"></div>
<div id="otpVerificationModal"></div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/gn-data.js') }}"></script>
<script>
let currentView = localStorage.getItem('userViewMode') || 'card';
let usersData = @json($users->items());
let totalUsers = {{ $users->total() }};
let currentPage = 1;
let itemsPerPage = 12;
let filteredUsers = [];
let searchTerm = '';

document.addEventListener('DOMContentLoaded', function() {
	setViewMode(currentView);
	setupSearch();
	updateStats();
});

function setupSearch() {
	const searchInput = document.getElementById('smartSearch');
	const searchBtn = document.getElementById('searchBtn');

	const performSearch = () => {
		searchTerm = searchInput.value.toLowerCase().trim();
		filterUsers();
	};

	searchInput.addEventListener('keyup', function(e) {
		if (e.key === 'Enter') {
			performSearch();
		}
	});

	searchBtn.addEventListener('click', performSearch);
}

function filterUsers() {
	let users = usersData;
	const roleFilter = document.getElementById('roleFilter').value;
	const statusFilter = document.getElementById('statusFilter').value;

	if (roleFilter) {
		users = users.filter(u => u.role === roleFilter);
	}

	if (statusFilter) {
		const isActive = statusFilter === 'active';
		users = users.filter(u => u.is_active == isActive);
	}

	if (searchTerm) {
		users = users.filter(user => {
			const fullName = getUserFullName(user).toLowerCase();
			const searchableFields = [
				fullName,
				user.username.toLowerCase(),
				user.email?.toLowerCase() || '',
				user.farmer?.nic_no?.toLowerCase() || '',
				user.lead_farmer?.nic_no?.toLowerCase() || '',
				user.buyer?.nic_no?.toLowerCase() || '',
				user.facilitator?.nic_no?.toLowerCase() || '',
				user.farmer?.primary_mobile || '',
				user.lead_farmer?.primary_mobile || '',
				user.buyer?.primary_mobile || '',
				user.facilitator?.primary_mobile || ''
			];

			return searchableFields.some(field => field.includes(searchTerm));
		});
	}

	filteredUsers = users;
	totalUsers = users.length;
	currentPage = 1;
	renderCurrentView();
}

function filterByRole(role) {
	document.getElementById('roleFilter').value = role;
	applyFilters();
}

function applyFilters() {
	filterUsers();
}

function setViewMode(mode) {
	currentView = mode;
	localStorage.setItem('userViewMode', mode);

	document.getElementById('cardViewBtn').classList.toggle('active', mode === 'card');
	document.getElementById('tableViewBtn').classList.toggle('active', mode === 'table');

	calculateItemsPerPage();
	filterUsers();
}

function calculateItemsPerPage() {
	const width = window.innerWidth;

	if (currentView === 'card') {
		if (width >= 2560) itemsPerPage = 18;
		else if (width >= 1500) itemsPerPage = 12;
		else if (width >= 1200) itemsPerPage = 8;
		else if (width >= 992) itemsPerPage = 6;
		else if (width >= 768) itemsPerPage = 4;
		else itemsPerPage = 3;
	} else {
		if (width >= 2560) itemsPerPage = 15;
		else if (width >= 1500) itemsPerPage = 15;
		else if (width >= 1200) itemsPerPage = 10;
		else if (width >= 992) itemsPerPage = 10;
		else if (width >= 768) itemsPerPage = 10;
		else itemsPerPage = 5;
	}
}

function renderCurrentView() {
	if (currentView === 'card') {
		renderCardView();
	} else {
		renderTableView();
	}
}

function renderCardView() {
	const start = (currentPage - 1) * itemsPerPage;
	const end = start + itemsPerPage;
	const pageUsers = filteredUsers.slice(start, end);

	let html = '';

	if (pageUsers.length === 0) {
		html = `
			<div class="empty-state">
				<i class="fa-solid fa-users-slash"></i>
				<h3>No Users Found</h3>
				<p>Try adjusting your search or filters</p>
			</div>
		`;
	} else {
		pageUsers.forEach(user => {
			const roleInfo = getRoleInfo(user);
			const profilePhoto = getProfilePhoto(user);
			const fullName = getUserFullName(user);
			const contactNumber = getUserContactNumber(user);

			html += `
				<div class="user-card" data-user-id="${user.id}">
					<div class="card-header">
						<span class="user-id">#${user.id}</span>
						<span class="status-badge ${user.is_active ? 'active' : 'inactive'}">
							<i class="fa-solid fa-circle"></i>
							${user.is_active ? 'Active' : 'Inactive'}
						</span>
					</div>
					<div class="card-body">
						<div class="user-avatar">
							<img src="${profilePhoto}" alt="${fullName}" onerror="this.src='${getFallbackImage(user.role)}'">
						</div>
						<h3 class="user-name">${fullName}</h3>
						<div class="user-username">@${user.username}</div>
						<div class="role-badge ${roleInfo.color}">
							<i class="fa-solid ${roleInfo.icon}"></i>
							${roleInfo.label}
						</div>
						<div class="user-contact">
							${contactNumber ? `<a href="tel:${contactNumber}" class="contact-link"><i class="fa-solid fa-phone"></i> ${contactNumber}</a>` : '<span class="text-muted">No contact</span>'}
						</div>
						<div class="user-meta">
							<div class="meta-item">
								<i class="fa-regular fa-envelope"></i>
								<span>${user.email || 'N/A'}</span>
							</div>
							<div class="meta-item">
								<i class="fa-regular fa-calendar"></i>
								<span>${formatDate(user.created_at)}</span>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button class="btn-icon view" onclick="viewUserProfile(${user.id})" title="View Profile">
							<i class="fa-regular fa-eye"></i>
						</button>
						<button class="btn-icon edit" onclick="editUserWithOTP(${user.id})" title="Edit User">
							<i class="fa-regular fa-pen-to-square"></i>
						</button>
						${user.id !== {{ Auth::id() }} ? `
							<button class="btn-icon toggle" onclick="toggleUserStatus(${user.id}, '${user.is_active ? 'deactivate' : 'activate'}')" title="${user.is_active ? 'Deactivate' : 'Activate'}">
								<i class="fa-solid ${user.is_active ? 'fa-user-slash' : 'fa-user-check'}"></i>
							</button>
						` : ''}
					</div>
				</div>
			`;
		});
	}

	document.getElementById('usersGrid').innerHTML = html;
	document.getElementById('usersGrid').style.display = 'grid';
	document.getElementById('tableView').style.display = 'none';
	renderPagination();
}

function renderTableView() {
	const start = (currentPage - 1) * itemsPerPage;
	const end = start + itemsPerPage;
	const pageUsers = filteredUsers.slice(start, end);

	let html = '';

	if (pageUsers.length === 0) {
		html = `
			<div class="empty-state">
				<i class="fa-solid fa-users-slash"></i>
				<h3>No Users Found</h3>
				<p>Try adjusting your search or filters</p>
			</div>
		`;
	} else {
		html = '<table class="users-table"><thead><tr><th>User</th><th>Role</th><th>Contact</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';

		pageUsers.forEach(user => {
			const roleInfo = getRoleInfo(user);
			const profilePhoto = getProfilePhoto(user);
			const fullName = getUserFullName(user);
			const contactNumber = getUserContactNumber(user);

			html += `
				<tr class="user-row" data-user-id="${user.id}">
					<td>
						<div class="table-user">
							<img src="${profilePhoto}" alt="${fullName}" class="table-avatar" onerror="this.src='${getFallbackImage(user.role)}'">
							<div>
								<div class="table-username">${fullName}</div>
								<small class="text-muted">@${user.username}</small>
							</div>
						</div>
					</td>
					<td>
						<span class="role-tag ${roleInfo.color}">
							<i class="fa-solid ${roleInfo.icon}"></i>
							${roleInfo.label}
						</span>
					</td>
					<td>
						${contactNumber ? `<a href="tel:${contactNumber}" class="contact-link">${contactNumber}</a>` : '<span class="text-muted">N/A</span>'}
						${user.email ? `<div><small class="text-muted">${user.email}</small></div>` : ''}
					</td>
					<td>
						<span class="status-tag ${user.is_active ? 'active' : 'inactive'}">
							<i class="fa-solid fa-circle"></i>
							${user.is_active ? 'Active' : 'Inactive'}
						</span>
					</td>
					<td>
						<div class="table-date">
							<div>${formatDate(user.created_at)}</div>
							<small>${timeAgo(user.created_at)}</small>
						</div>
					</td>
					<td>
						<div class="table-actions">
							<button class="table-btn view" onclick="viewUserProfile(${user.id})" title="View Profile">
								<i class="fa-regular fa-eye"></i>
							</button>
							<button class="table-btn edit" onclick="editUserWithOTP(${user.id})" title="Edit User">
								<i class="fa-regular fa-pen-to-square"></i>
							</button>
							${user.id !== {{ Auth::id() }} ? `
								<button class="table-btn toggle" onclick="toggleUserStatus(${user.id}, '${user.is_active ? 'deactivate' : 'activate'}')" title="${user.is_active ? 'Deactivate' : 'Activate'}">
									<i class="fa-solid ${user.is_active ? 'fa-user-slash' : 'fa-user-check'}"></i>
								</button>
							` : ''}
						</div>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table>';
	}

	document.getElementById('tableView').innerHTML = html;
	document.getElementById('tableView').style.display = 'block';
	document.getElementById('usersGrid').style.display = 'none';
	renderPagination();
}

function getProfilePhoto(user) {
	const defaultImages = [
		'default-avatar.png',
		'default.png',
		'default-buyer.png',
		'farmer.png',
		'lead-farmer.png',
		'facilitator.png'
	];

	if (user.profile_photo && !defaultImages.includes(user.profile_photo)) {
		return `{{ asset('uploads/profile_pictures') }}/${user.profile_photo}`;
	}
	return getFallbackImage(user.role);
}

function getFallbackImage(role) {
	const fallbacks = {
		buyer: `{{ asset('uploads/profile_pictures/default-buyer.png') }}`,
		farmer: `{{ asset('assets/images/farmer.png') }}`,
		lead_farmer: `{{ asset('assets/images/Profiles/lead-farmer.png') }}`,
		facilitator: `{{ asset('assets/images/Profiles/facilitator.png') }}`,
		admin: `{{ asset('assets/images/Profiles/default-avatar.png') }}`
	};
	return fallbacks[role] || `{{ asset('assets/icons/user-icon.svg') }}`;
}

function getUserFullName(user) {
	if (user.farmer && user.farmer.name) return user.farmer.name;
	if (user.lead_farmer && user.lead_farmer.name) return user.lead_farmer.name;
	if (user.buyer && user.buyer.name) return user.buyer.name;
	if (user.facilitator && user.facilitator.name) return user.facilitator.name;
	return user.username;
}

function getUserContactNumber(user) {
	if (user.farmer && user.farmer.primary_mobile) return user.farmer.primary_mobile;
	if (user.lead_farmer && user.lead_farmer.primary_mobile) return user.lead_farmer.primary_mobile;
	if (user.buyer && user.buyer.primary_mobile) return user.buyer.primary_mobile;
	if (user.facilitator && user.facilitator.primary_mobile) return user.facilitator.primary_mobile;
	return null;
}

function getRoleInfo(user) {
	const roles = {
		farmer: { icon: 'fa-tractor', color: 'green', label: 'Farmer' },
		lead_farmer: { icon: 'fa-user-tie', color: 'blue', label: 'Lead Farmer' },
		buyer: { icon: 'fa-cart-shopping', color: 'amber', label: 'Buyer' },
		facilitator: { icon: 'fa-handshake-angle', color: 'purple', label: 'Facilitator' }
	};
	return roles[user.role] || { icon: 'fa-user', color: 'gray', label: user.role };
}

function formatDate(date) {
	return new Date(date).toLocaleDateString('en-US', { 
		day: 'numeric', 
		month: 'short', 
		year: 'numeric' 
	});
}

function formatDateTime(date) {
	return new Date(date).toLocaleDateString('en-US', { 
		day: 'numeric', 
		month: 'short', 
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	});
}

function timeAgo(date) {
	const seconds = Math.floor((new Date() - new Date(date)) / 1000);
	const intervals = {
		year: 31536000,
		month: 2592000,
		week: 604800,
		day: 86400,
		hour: 3600,
		minute: 60
	};

	for (const [unit, secondsInUnit] of Object.entries(intervals)) {
		const interval = Math.floor(seconds / secondsInUnit);
		if (interval >= 1) {
			return interval + ' ' + unit + (interval === 1 ? '' : 's') + ' ago';
		}
	}
	return 'just now';
}

function renderPagination() {
	const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
	let html = '';

	if (totalPages > 1) {
		html = '<div class="pagination"><ul>';

		html += `<li class="${currentPage === 1 ? 'disabled' : ''}">
			<a href="#" onclick="changePage(${currentPage - 1}); return false;">
				<i class="fa-solid fa-chevron-left"></i>
			</a>
		</li>`;

		let startPage = Math.max(1, currentPage - 2);
		let endPage = Math.min(totalPages, startPage + 4);

		if (endPage - startPage < 4) {
			startPage = Math.max(1, endPage - 4);
		}

		for (let i = startPage; i <= endPage; i++) {
			html += `<li class="${currentPage === i ? 'active' : ''}">
				<a href="#" onclick="changePage(${i}); return false;">${i}</a>
			</li>`;
		}

		html += `<li class="${currentPage === totalPages ? 'disabled' : ''}">
			<a href="#" onclick="changePage(${currentPage + 1}); return false;">
				<i class="fa-solid fa-chevron-right"></i>
			</a>
		</li>`;

		html += '</ul></div>';
	}

	document.getElementById('paginationWrapper').innerHTML = html;
}

function changePage(page) {
	if (page < 1 || page > Math.ceil(filteredUsers.length / itemsPerPage)) return;
	currentPage = page;
	renderCurrentView();
}

window.addEventListener('resize', function() {
	calculateItemsPerPage();
	if (filteredUsers.length > 0) {
		renderCurrentView();
	}
});

function updateStats() {
	const stats = {
		farmer: {{ $userTypes['farmers'] ?? 0 }},
		lead_farmer: {{ $userTypes['lead_farmers'] ?? 0 }},
		buyer: {{ $userTypes['buyers'] ?? 0 }},
		facilitator: {{ $userTypes['facilitators'] ?? 0 }}
	};

	document.querySelectorAll('.stat-item').forEach((item, index) => {
		const values = Object.values(stats);
		if (index < values.length) {
			item.querySelector('.stat-value').textContent = values[index];
		}
	});
}

function refreshUsers() {
	Swal.fire({
		title: 'Refreshing...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	setTimeout(() => {
		window.location.reload();
	}, 800);
}

function validatePassword(password) {
	const hasUpperCase = /[A-Z]/.test(password);
	const hasLowerCase = /[a-z]/.test(password);
	const hasNumbers = /\d/.test(password);
	const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

	return password.length >= 8 && hasUpperCase && hasLowerCase && hasNumbers && hasSpecial;
}

function validateNIC(nic) {
	if (!nic) return false;
	nic = nic.trim().toUpperCase();
	const oldNicPattern = /^[0-9]{9}[VX]$/;
	const newNicPattern = /^[0-9]{12}$/;
	if (oldNicPattern.test(nic)) {
		const year = parseInt(nic.substr(0, 2));
		const days = parseInt(nic.substr(2, 3));
		if (days > 500) {
			return days <= 866;
		}
		return days > 0 && days <= 366;
	}
	if (newNicPattern.test(nic)) {
		const year = parseInt(nic.substr(0, 4));
		const days = parseInt(nic.substr(4, 3));
		if (days > 500) {
			return days <= 866;
		}
		return year >= 1900 && year <= 2100 && days > 0 && days <= 366;
	}
	return false;
}

function formatNIC(nic) {
	if (!nic) return '';
	nic = nic.trim().toUpperCase();
	if (nic.length === 10 && /^[0-9]{9}[VX]$/.test(nic)) {
		return nic;
	}
	if (nic.length === 12 && /^[0-9]{12}$/.test(nic)) {
		return nic;
	}
	return nic;
}

function viewUserProfile(userId) {
	Swal.fire({
		title: 'Loading Profile...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch(`/facilitator/users/${userId}/profile`)
	.then(response => response.json())
	.then(data => {
		Swal.close();
		if (data.success) {
			const user = data.user;
			const profilePhoto = getProfilePhoto(user);
			const fullName = getUserFullName(user);
			const contactNumber = getUserContactNumber(user);

			let basicInfoHTML = `
				<div class="swal-section">
					<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Basic Information</h4>
					<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
						<div><strong>Username:</strong> ${user.username}</div>
						<div><strong>Email:</strong> ${user.email || 'N/A'}</div>
						<div><strong>Primary Contact:</strong> ${contactNumber || 'N/A'}</div>
						<div><strong>Role:</strong> <span style="text-transform: capitalize;">${user.role.replace('_', ' ')}</span></div>
						<div><strong>Status:</strong> ${user.is_active ? 'Active' : 'Inactive'}</div>
						<div><strong>Registered Date:</strong> ${formatDateTime(user.created_at)}</div>
						<div><strong>Last Login:</strong> ${user.last_login ? formatDateTime(user.last_login) : 'Never'}</div>
					</div>
				</div>
			`;

			let profileInfoHTML = '';
			if (user.role === 'farmer' && user.farmer) {
				profileInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Full Name:</strong> ${user.farmer.name || 'N/A'}</div>
							<div><strong>NIC Number:</strong> ${user.farmer.nic_no || 'N/A'}</div>
							<div><strong>WhatsApp Number:</strong> ${user.farmer.whatsapp_number || 'N/A'}</div>
							<div><strong>Address:</strong> ${user.farmer.residential_address || 'N/A'}</div>
							<div><strong>District:</strong> ${user.farmer.district || 'N/A'}</div>
							<div><strong>Divisional Secretariat:</strong> ${user.farmer.divisional_secretariat || 'N/A'}</div>
							<div><strong>Grama Niladhari Division:</strong> ${user.farmer.grama_niladhari_division || 'N/A'}</div>
							<div><strong>GN Division Code:</strong> ${user.farmer.gn_division_code || 'N/A'}</div>
						</div>
					</div>
				`;

				let leadFarmerInfoHTML = '';
				if (user.farmer.lead_farmer) {
					const lf = user.farmer.lead_farmer;
					leadFarmerInfoHTML = `
						<div class="swal-section" style="margin-top: 15px;">
							<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Lead Farmer Information</h4>
							<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
								<div><strong>Name:</strong> ${lf.name || 'N/A'}</div>
								<div><strong>District:</strong> ${lf.district || 'N/A'}</div>
								<div><strong>Contact:</strong> ${lf.primary_mobile || 'N/A'}</div>
								<div><strong>Group Name:</strong> ${lf.group_name || 'N/A'}</div>
								<div><strong>Group Number:</strong> ${lf.group_number || 'N/A'}</div>
							</div>
						</div>
					`;
				}

				let paymentInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Payment Details</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Preferred Payment:</strong> ${user.farmer.preferred_payment || 'N/A'}</div>
							<div><strong>Bank Name:</strong> ${user.farmer.bank_name || 'N/A'}</div>
							<div><strong>Bank Branch:</strong> ${user.farmer.bank_branch || 'N/A'}</div>
							<div><strong>Account Number:</strong> ${user.farmer.account_number || 'N/A'}</div>
							<div><strong>Account Holder:</strong> ${user.farmer.account_holder_name || 'N/A'}</div>
							<div><strong>Ez Cash Number:</strong> ${user.farmer.ezcash_mobile || 'N/A'}</div>
							<div><strong>M-Cash Number:</strong> ${user.farmer.mcash_mobile || 'N/A'}</div>
						</div>
					</div>
				`;

				Swal.fire({
					title: `${fullName}'s Profile`,
					width: '800px',
					html: `
						<div class="profile-view" style="text-align: center;">
							<img src="${profilePhoto}" class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-green); margin-bottom: 15px;" onerror="this.src='${getFallbackImage(user.role)}'">
						</div>
						<div style="max-height: 60vh; overflow-y: auto; padding-right: 10px;">
							${basicInfoHTML}
							${profileInfoHTML}
							${leadFarmerInfoHTML}
							${paymentInfoHTML}
						</div>
					`,
					confirmButtonText: 'Close',
					confirmButtonColor: '#10B981'
				});
			} 
			else if (user.role === 'lead_farmer' && user.lead_farmer) {
				profileInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Full Name:</strong> ${user.lead_farmer.name || 'N/A'}</div>
							<div><strong>NIC Number:</strong> ${user.lead_farmer.nic_no || 'N/A'}</div>
							<div><strong>WhatsApp Number:</strong> ${user.lead_farmer.whatsapp_number || 'N/A'}</div>
							<div><strong>Address:</strong> ${user.lead_farmer.residential_address || 'N/A'}</div>
							<div><strong>District:</strong> ${user.lead_farmer.district || 'N/A'}</div>
							<div><strong>Divisional Secretariat:</strong> ${user.lead_farmer.divisional_secretariat || 'N/A'}</div>
							<div><strong>Grama Niladhari Division:</strong> ${user.lead_farmer.grama_niladhari_division || 'N/A'}</div>
							<div><strong>GN Division Code:</strong> ${user.lead_farmer.gn_division_code || 'N/A'}</div>
							<div><strong>Group Name:</strong> ${user.lead_farmer.group_name || 'N/A'}</div>
							<div><strong>Group Number:</strong> ${user.lead_farmer.group_number || 'N/A'}</div>
						</div>
					</div>
				`;

				let paymentInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Payment Details</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Preferred Payment:</strong> ${user.lead_farmer.preferred_payment || 'N/A'}</div>
							<div><strong>Bank Name:</strong> ${user.lead_farmer.bank_name || 'N/A'}</div>
							<div><strong>Bank Branch:</strong> ${user.lead_farmer.bank_branch || 'N/A'}</div>
							<div><strong>Account Number:</strong> ${user.lead_farmer.account_number || 'N/A'}</div>
							<div><strong>Account Holder:</strong> ${user.lead_farmer.account_holder_name || 'N/A'}</div>
						</div>
					</div>
				`;

				Swal.fire({
					title: `${fullName}'s Profile`,
					width: '800px',
					html: `
						<div class="profile-view" style="text-align: center;">
							<img src="${profilePhoto}" class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-green); margin-bottom: 15px;" onerror="this.src='${getFallbackImage(user.role)}'">
						</div>
						<div style="max-height: 60vh; overflow-y: auto; padding-right: 10px;">
							${basicInfoHTML}
							${profileInfoHTML}
							${paymentInfoHTML}
						</div>
					`,
					confirmButtonText: 'Close',
					confirmButtonColor: '#10B981'
				});
			}
			else if (user.role === 'buyer' && user.buyer) {
				profileInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Full Name:</strong> ${user.buyer.name || 'N/A'}</div>
							<div><strong>NIC Number:</strong> ${user.buyer.nic_no || 'N/A'}</div>
							<div><strong>WhatsApp Number:</strong> ${user.buyer.whatsapp_number || 'N/A'}</div>
							<div><strong>Address:</strong> ${user.buyer.residential_address || 'N/A'}</div>
							<div><strong>District:</strong> ${user.buyer.district || 'N/A'}</div>
						</div>
					</div>
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Business Information</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Business Name:</strong> ${user.buyer.business_name || 'N/A'}</div>
							<div><strong>Business Type:</strong> ${user.buyer.business_type || 'N/A'}</div>
						</div>
					</div>
				`;

				Swal.fire({
					title: `${fullName}'s Profile`,
					width: '800px',
					html: `
						<div class="profile-view" style="text-align: center;">
							<img src="${profilePhoto}" class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-green); margin-bottom: 15px;" onerror="this.src='${getFallbackImage(user.role)}'">
						</div>
						<div style="max-height: 60vh; overflow-y: auto; padding-right: 10px;">
							${basicInfoHTML}
							${profileInfoHTML}
						</div>
					`,
					confirmButtonText: 'Close',
					confirmButtonColor: '#10B981'
				});
			}
			else if (user.role === 'facilitator' && user.facilitator) {
				profileInfoHTML = `
					<div class="swal-section" style="margin-top: 15px;">
						<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
						<div class="profile-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; text-align: left; gap: 8px; font-size: 13px;">
							<div><strong>Full Name:</strong> ${user.facilitator.name || 'N/A'}</div>
							<div><strong>NIC Number:</strong> ${user.facilitator.nic_no || 'N/A'}</div>
							<div><strong>WhatsApp Number:</strong> ${user.facilitator.whatsapp_number || 'N/A'}</div>
							<div><strong>Email:</strong> ${user.facilitator.email || 'N/A'}</div>
							<div><strong>Assigned Division:</strong> ${user.facilitator.assigned_division || 'N/A'}</div>
						</div>
					</div>
				`;

				Swal.fire({
					title: `${fullName}'s Profile`,
					width: '800px',
					html: `
						<div class="profile-view" style="text-align: center;">
							<img src="${profilePhoto}" class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-green); margin-bottom: 15px;" onerror="this.src='${getFallbackImage(user.role)}'">
						</div>
						<div style="max-height: 60vh; overflow-y: auto; padding-right: 10px;">
							${basicInfoHTML}
							${profileInfoHTML}
						</div>
					`,
					confirmButtonText: 'Close',
					confirmButtonColor: '#10B981'
				});
			}
		} else {
			Swal.fire('Error', data.message || 'Failed to load profile', 'error');
		}
	})
	.catch(() => {
		Swal.fire('Error', 'Failed to load profile', 'error');
	});
}

function editUserWithOTP(userId) {
	Swal.fire({
		title: 'Edit User',
		text: 'OTP verification required. Send OTP to user?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Send OTP',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel'
	}).then(result => {
		if (result.isConfirmed) {
			sendOTPForEdit(userId);
		}
	});
}

function sendOTPForEdit(userId) {
	Swal.fire({
		title: 'Sending OTP...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch(`/facilitator/users/${userId}/send-otp`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		}
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			showOTPVerificationModal(userId, data.contact);
		} else {
			Swal.fire('Error', data.message || 'Failed to send OTP', 'error');
		}
	})
	.catch(() => {
		Swal.fire('Error', 'Failed to send OTP', 'error');
	});
}

function showOTPVerificationModal(userId, contact) {
	Swal.fire({
		title: 'Verify OTP',
		html: `
			<div class="otp-container">
				<p>Enter 6-digit OTP sent to xxxxxx${contact || 'user'}</p>
				<input type="text" id="otpInput" class="otp-input" maxlength="6" pattern="[0-9]*" inputmode="numeric">
				<p class="otp-timer">OTP expires in 5 minutes</p>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Verify',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		preConfirm: () => {
			const otp = document.getElementById('otpInput').value;
			if (!otp || otp.length !== 6) {
				Swal.showValidationMessage('Please enter valid 6-digit OTP');
				return false;
			}
			return { otp };
		}
	}).then(result => {
		if (result.isConfirmed) {
			verifyOTP(userId, result.value.otp);
		}
	});
}

function verifyOTP(userId, otp) {
	Swal.fire({
		title: 'Verifying...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch('/facilitator/users/verify-otp', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		},
		body: JSON.stringify({ otp, user_id: userId })
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			Swal.fire({
				title: 'Success!',
				text: 'OTP verified',
				icon: 'success',
				confirmButtonColor: '#10B981'
			}).then(() => {
				loadEditUserForm(userId);
			});
		} else {
			Swal.fire('Error', data.message || 'Invalid OTP', 'error');
		}
	})
	.catch(() => {
		Swal.fire('Error', 'Verification failed', 'error');
	});
}

function loadEditUserForm(userId) {
	Swal.fire({
		title: 'Loading...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch(`/facilitator/users/${userId}/edit-data`)
	.then(response => response.json())
	.then(data => {
		Swal.close();
		if (data.success) {
			showEditUserForm(data.user);
		} else {
			Swal.fire('Error', data.message || 'Failed to load user data', 'error');
		}
	})
	.catch(() => {
		Swal.fire('Error', 'Failed to load user data', 'error');
	});
}

function showEditUserForm(user) {
	let roleDisplay = user.role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
	let contactNumber = getUserContactNumber(user);

	let basicInfoHtml = `
		<div class="swal-section">
			<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Basic Information</h4>
			<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
				<div class="form-group">
					<label>Username</label>
					<input type="text" class="swal2-input" value="${user.username}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
				</div>
				<div class="form-group">
					<label>Email *</label>
					<input type="email" id="edit_email" class="swal2-input" value="${user.email || ''}" required style="margin: 0; width: 100%; font-size: 14px;">
				</div>
				<div class="form-group">
					<label>Primary Contact Number *</label>
					<input type="text" id="edit_primary_mobile" class="swal2-input" value="${contactNumber || ''}" required placeholder="07xxxxxxxx" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" style="margin: 0; width: 100%; font-size: 14px;">
				</div>
				<div class="form-group">
					<label>Role</label>
					<input type="text" class="swal2-input" value="${roleDisplay}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
				</div>
				<div class="form-group">
					<label>Status</label>
					<select id="edit_is_active" class="swal2-select" style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
						<option value="1" ${user.is_active ? 'selected' : ''}>Active</option>
						<option value="0" ${!user.is_active ? 'selected' : ''}>Inactive</option>
					</select>
				</div>
				<div class="form-group">
					<label>Registered Date</label>
					<input type="text" class="swal2-input" value="${formatDateTime(user.created_at)}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
				</div>
				<div class="form-group">
					<label>Last Login</label>
					<input type="text" class="swal2-input" value="${user.last_login ? formatDateTime(user.last_login) : 'Never'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
				</div>
			</div>
		</div>
	`;

	let profileInfoHtml = '';
	let paymentInfoHtml = '';
	let businessInfoHtml = '';

	let profileData = {};
	if (user.role === 'farmer' && user.farmer) profileData = user.farmer;
	else if (user.role === 'lead_farmer' && user.lead_farmer) profileData = user.lead_farmer;
	else if (user.role === 'buyer' && user.buyer) profileData = user.buyer;
	else if (user.role === 'facilitator' && user.facilitator) profileData = user.facilitator;

	if (['farmer', 'lead_farmer', 'buyer'].includes(user.role)) {
		profileInfoHtml = `
			<div class="swal-section" style="margin-top: 20px;">
				<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
				<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
					<div class="form-group">
						<label>Full Name *</label>
						<input type="text" id="edit_name" class="swal2-input" value="${profileData.name || ''}" required style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group">
						<label>NIC Number ${user.role==='lead_farmer'?'*':''}</label>
						<input type="text" id="edit_nic_no" class="swal2-input" value="${profileData.nic_no || ''}" ${user.role==='lead_farmer'?'required':''} style="margin: 0; width: 100%; font-size: 14px;">
						<div id="edit_nic_status" style="font-size: 12px; margin-top: 5px;"></div>
					</div>
					<div class="form-group">
						<label>WhatsApp Number</label>
						<input type="text" id="edit_whatsapp_number" class="swal2-input" value="${profileData.whatsapp_number || ''}" placeholder="07xxxxxxxx" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group" style="grid-column: span 2;">
						<label>Residential Address ${user.role!=='buyer'?'*':''}</label>
						<textarea id="edit_residential_address" class="swal2-textarea" ${user.role!=='buyer'?'required':''} style="margin: 0; width: 100%; font-size: 14px; min-height: 80px;">${profileData.residential_address || ''}</textarea>
					</div>
					<div class="form-group">
						<label>District ${user.role!=='buyer'?'*':''}</label>
						<select id="edit_district" class="swal2-select" ${user.role!=='buyer'?'required':''} style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
							<option value="">Select District</option>
							${['Ampara','Anuradhapura','Badulla','Batticaloa','Colombo','Galle','Gampaha','Hambantota','Jaffna','Kalutara','Kandy','Kegalle','Kilinochchi','Kurunegala','Mannar','Matale','Matara','Monaragala','Mullaitivu','Nuwara Eliya','Polonnaruwa','Puttalam','Ratnapura','Trincomalee','Vavuniya'].map(d => `<option value="${d}" ${profileData.district===d?'selected':''}>${d}</option>`).join('')}
						</select>
					</div>
		`;

		if (['farmer', 'lead_farmer'].includes(user.role)) {
			profileInfoHtml += `
					<div class="form-group">
						<label>Divisional Secretariat *</label>
						<select id="edit_divisional_secretariat" class="swal2-select" required style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
							<option value="">Select Divisional Secretariat</option>
						</select>
					</div>
					<div class="form-group">
						<label>GN Division *</label>
						<select id="edit_grama_niladhari_division" class="swal2-select" required style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
							<option value="">Select GN Division</option>
						</select>
					</div>
					<div class="form-group">
						<label>GN Code</label>
						<input type="text" id="edit_gn_division_code" class="swal2-input" value="${profileData.gn_division_code || ''}" readonly style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
			`;
			if (user.role === 'farmer') {
				profileInfoHtml += `
					<div class="form-group" style="grid-column: span 2;">
						<label>Map Link</label>
						<input type="url" id="edit_address_map_link" class="swal2-input" value="${profileData.address_map_link || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
				`;
			}
		}
		
		profileInfoHtml += `
				</div>
			</div>
		`;
	}

	if (user.role === 'farmer' && profileData.lead_farmer) {
		const lf = profileData.lead_farmer;
		profileInfoHtml += `
			<div class="swal-section" style="margin-top: 20px;">
				<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Lead Farmer Information</h4>
				<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
					<div class="form-group">
						<label>Lead Farmer Name</label>
						<input type="text" class="swal2-input" value="${lf.name || 'N/A'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
					<div class="form-group">
						<label>District</label>
						<input type="text" class="swal2-input" value="${lf.district || 'N/A'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
					<div class="form-group">
						<label>Contact</label>
						<input type="text" class="swal2-input" value="${lf.primary_mobile || 'N/A'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
					<div class="form-group">
						<label>Group Name</label>
						<input type="text" class="swal2-input" value="${lf.group_name || 'N/A'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
					<div class="form-group">
						<label>Group Number</label>
						<input type="text" class="swal2-input" value="${lf.group_number || 'N/A'}" disabled style="margin: 0; width: 100%; font-size: 14px; background-color: #f3f4f6;">
					</div>
				</div>
			</div>
		`;
	}
	
	if (['farmer', 'lead_farmer'].includes(user.role)) {
		let isLead = user.role === 'lead_farmer';
		paymentInfoHtml = `
			<div class="swal-section" style="margin-top: 20px;">
				<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Payment Details</h4>
				<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
					<div class="form-group" style="grid-column: span 2;">
						<label>Preferred Payment Method</label>
						<select id="edit_preferred_payment" class="swal2-select" style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
							${isLead ? `
								<option value="bank" selected>Bank Transfer Only</option>
							` : `
								<option value="bank" ${profileData.preferred_payment==='bank'?'selected':''}>Bank Transfer</option>
								<option value="ezcash" ${profileData.preferred_payment==='ezcash'?'selected':''}>eZ Cash</option>
								<option value="mcash" ${profileData.preferred_payment==='mcash'?'selected':''}>mCash</option>
								<option value="all" ${profileData.preferred_payment==='all'?'selected':''}>All Methods</option>
							`}
						</select>
					</div>
					
					<div class="form-group payment-bank">
						<label>Bank Name *</label>
						<input type="text" id="edit_bank_name" class="swal2-input" value="${profileData.bank_name || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group payment-bank">
						<label>Bank Branch *</label>
						<input type="text" id="edit_bank_branch" class="swal2-input" value="${profileData.bank_branch || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group payment-bank">
						<label>Account Number *</label>
						<input type="text" id="edit_account_number" class="swal2-input" value="${profileData.account_number || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group payment-bank">
						<label>Account Holder Name *</label>
						<input type="text" id="edit_account_holder_name" class="swal2-input" value="${profileData.account_holder_name || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					
					${!isLead ? `
					<div class="form-group payment-ezcash">
						<label>eZ Cash Mobile Number</label>
						<input type="text" id="edit_ezcash_mobile" class="swal2-input" value="${profileData.ezcash_mobile || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group payment-mcash">
						<label>mCash Mobile Number</label>
						<input type="text" id="edit_mcash_mobile" class="swal2-input" value="${profileData.mcash_mobile || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					` : ''}
				</div>
			</div>
		`;
	}

	if (user.role === 'buyer') {
		businessInfoHtml = `
			<div class="swal-section" style="margin-top: 20px;">
				<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Business Information</h4>
				<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
					<div class="form-group">
						<label>Business Name</label>
						<input type="text" id="edit_business_name" class="swal2-input" value="${profileData.business_name || ''}" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group">
						<label>Business Type</label>
						<select id="edit_business_type" class="swal2-select" style="margin: 0; width: 100%; font-size: 14px; padding: 0 10px; height: 40px;">
							<option value="">Select Type</option>
							<option value="individual" ${profileData.business_type==='individual'?'selected':''}>Individual</option>
							<option value="restaurant" ${profileData.business_type==='restaurant'?'selected':''}>Restaurant</option>
							<option value="hotel" ${profileData.business_type==='hotel'?'selected':''}>Hotel</option>
							<option value="retailer" ${profileData.business_type==='retailer'?'selected':''}>Retailer</option>
							<option value="wholesaler" ${profileData.business_type==='wholesaler'?'selected':''}>Wholesaler</option>
						</select>
					</div>
				</div>
			</div>
		`;
	}

	if (user.role === 'facilitator') {
		profileInfoHtml = `
			<div class="swal-section" style="margin-top: 20px;">
				<h4 style="text-align: left; margin-bottom: 10px; color: var(--primary-green); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Information</h4>
				<div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
					<div class="form-group">
						<label>Full Name *</label>
						<input type="text" id="edit_name" class="swal2-input" value="${profileData.name || ''}" required style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group">
						<label>NIC Number</label>
						<input type="text" id="edit_nic_no" class="swal2-input" value="${profileData.nic_no || ''}" style="margin: 0; width: 100%; font-size: 14px;">
						<div id="edit_nic_status" style="font-size: 12px; margin-top: 5px;"></div>
					</div>
					<div class="form-group">
						<label>WhatsApp Number</label>
						<input type="text" id="edit_whatsapp_number" class="swal2-input" value="${profileData.whatsapp_number || ''}" placeholder="07xxxxxxxx" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'');" style="margin: 0; width: 100%; font-size: 14px;">
					</div>
					<div class="form-group">
						<label>Assigned Division *</label>
						<input type="text" id="edit_assigned_division" class="swal2-input" value="${profileData.assigned_division || ''}" required style="margin: 0; width: 100%; font-size: 14px;">
					</div>
				</div>
			</div>
		`;
	}

	Swal.fire({
		title: `Edit ${roleDisplay} Details`,
		width: '850px',
		html: `
			<form id="editForm" class="edit-form" style="max-height: 65vh; overflow-y: auto; overflow-x: hidden; padding: 10px; font-size: 14px;">
				${basicInfoHtml}
				${profileInfoHtml}
				${paymentInfoHtml}
				${businessInfoHtml}
			</form>
		`,
		showCancelButton: true,
		confirmButtonText: 'Update',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		allowOutsideClick: false,
		didOpen: () => {
			const nicInput = document.getElementById('edit_nic_no');
			const nicStatus = document.getElementById('edit_nic_status');
			if (nicInput && nicStatus) {
				const checkNIC = () => {
					let nicValue = nicInput.value;
					if (nicValue) {
						nicValue = nicValue.trim().toUpperCase();
					}
					if (nicValue === '') {
						nicStatus.textContent = '';
					} else if (validateNIC(nicValue)) {
						nicStatus.style.color = '#10B981';
						nicStatus.innerHTML = '<i class="fa-solid fa-circle-check"></i> Valid NIC format';
					} else {
						nicStatus.style.color = '#dc2626';
						nicStatus.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Invalid NIC format';
					}
				};
				nicInput.addEventListener('input', function() {
					this.value = this.value.trim().toUpperCase();
					checkNIC();
				});
				nicInput.addEventListener('blur', function() {
					const nicValue = this.value.trim().toUpperCase();
					if (nicValue && validateNIC(nicValue)) {
						this.value = formatNIC(nicValue);
					}
					checkNIC();
				});
				checkNIC(); // Trigger on load
			}

			const selectElement = document.getElementById('edit_preferred_payment');
			if (selectElement) {
				const toggleFields = () => {
					const val = selectElement.value;
					document.querySelectorAll('.payment-bank').forEach(el => el.style.display = (val === 'bank' || val === 'all') ? 'block' : 'none');
					document.querySelectorAll('.payment-ezcash').forEach(el => el.style.display = (val === 'ezcash' || val === 'all') ? 'block' : 'none');
					document.querySelectorAll('.payment-mcash').forEach(el => el.style.display = (val === 'mcash' || val === 'all') ? 'block' : 'none');
				};
				selectElement.addEventListener('change', toggleFields);
				toggleFields();
			}
			
			document.querySelectorAll('#editForm label').forEach(lbl => {
				lbl.style.display = 'block';
				lbl.style.fontWeight = '600';
				lbl.style.marginBottom = '5px';
				lbl.style.color = '#374151';
			});

			// Cascading logic for GN Data
			const districtSelect = document.getElementById('edit_district');
			const dsSelect = document.getElementById('edit_divisional_secretariat');
			const gnSelect = document.getElementById('edit_grama_niladhari_division');
			const gnCodeInput = document.getElementById('edit_gn_division_code');

			if (districtSelect && dsSelect && gnSelect) {
				const populateDS = (district, selectedDS = null) => {
					dsSelect.innerHTML = '<option value="">Select Divisional Secretariat</option>';
					gnSelect.innerHTML = '<option value="">Select GN Division</option>';
					if (gnCodeInput) gnCodeInput.value = '';

					if (district && typeof gnData !== 'undefined' && gnData[district]) {
						Object.keys(gnData[district]).forEach(ds => {
							const option = document.createElement('option');
							option.value = ds;
							option.textContent = ds;
							if (selectedDS && ds === selectedDS) option.selected = true;
							dsSelect.appendChild(option);
						});
					}
				};

				const populateGN = (district, ds, selectedGN = null) => {
					gnSelect.innerHTML = '<option value="">Select GN Division</option>';
					if (gnCodeInput) gnCodeInput.value = '';

					if (district && ds && typeof gnData !== 'undefined' && gnData[district] && gnData[district][ds]) {
						gnData[district][ds].forEach(gn => {
							const option = document.createElement('option');
							option.value = gn.name;
							option.textContent = gn.name;
							option.dataset.code = gn.code;
							if (selectedGN && gn.name === selectedGN) {
								option.selected = true;
								if (gnCodeInput) gnCodeInput.value = gn.code;
							}
							gnSelect.appendChild(option);
						});
					}
				};

				districtSelect.addEventListener('change', (e) => {
					populateDS(e.target.value);
				});

				dsSelect.addEventListener('change', (e) => {
					populateGN(districtSelect.value, e.target.value);
				});

				gnSelect.addEventListener('change', (e) => {
					const selectedOption = e.target.options[e.target.selectedIndex];
					if (gnCodeInput) gnCodeInput.value = selectedOption ? (selectedOption.dataset.code || '') : '';
				});

				// Initial population if data exists
				if (profileData.district) {
					populateDS(profileData.district, profileData.divisional_secretariat);
					if (profileData.divisional_secretariat) {
						populateGN(profileData.district, profileData.divisional_secretariat, profileData.grama_niladhari_division);
					}
				}
			}
		},
		preConfirm: () => {
			const getVal = id => {
				const el = document.getElementById(id);
				return el ? el.value : undefined;
			};
			
			const email = getVal('edit_email');
			const primary_mobile = getVal('edit_primary_mobile');
			const is_active = getVal('edit_is_active');
			
			if (!email || !primary_mobile) {
				Swal.showValidationMessage('Please fill all required basic info fields (*)!');
				return false;
			}
			
			if (primary_mobile && primary_mobile.length !== 10) {
				Swal.showValidationMessage('Primary Contact Number must be exactly 10 digits!');
				return false;
			}

			let resultData = {
				username: user.username, /* pass to fulfill backend logic if required */
				email,
				primary_mobile,
				is_active
			};

			if (['farmer', 'lead_farmer', 'buyer', 'facilitator'].includes(user.role)) {
				resultData.name = getVal('edit_name');
				if (!resultData.name) {
					Swal.showValidationMessage('Full Name is required!');
					return false;
				}
				
				resultData.nic_no = getVal('edit_nic_no');
				if (user.role === 'lead_farmer' && !resultData.nic_no) {
					Swal.showValidationMessage('NIC Number is required for Lead Farmers!');
					return false;
				}
				
				if (resultData.nic_no && !validateNIC(resultData.nic_no)) {
					Swal.showValidationMessage('Please enter a valid NIC number!');
					return false;
				}
				
				resultData.whatsapp_number = getVal('edit_whatsapp_number');
				if (resultData.whatsapp_number && resultData.whatsapp_number.length !== 10) {
					Swal.showValidationMessage('WhatsApp Number must be exactly 10 digits!');
					return false;
				}
				
				if (['farmer', 'lead_farmer'].includes(user.role)) {
					resultData.residential_address = getVal('edit_residential_address');
					resultData.district = getVal('edit_district');
					resultData.divisional_secretariat = getVal('edit_divisional_secretariat');
					resultData.grama_niladhari_division = getVal('edit_grama_niladhari_division');
					resultData.gn_division_code = getVal('edit_gn_division_code');
					
					if (!resultData.residential_address || !resultData.district || !resultData.divisional_secretariat || !resultData.grama_niladhari_division) {
						Swal.showValidationMessage('Please fill all required profile fields (*)!');
						return false;
					}

					if (user.role === 'farmer') {
						resultData.address_map_link = getVal('edit_address_map_link');
					}
					
					resultData.preferred_payment = getVal('edit_preferred_payment');
					let payMethod = resultData.preferred_payment;
					if (payMethod === 'bank' || payMethod === 'all') {
						resultData.bank_name = getVal('edit_bank_name');
						resultData.bank_branch = getVal('edit_bank_branch');
						resultData.account_number = getVal('edit_account_number');
						resultData.account_holder_name = getVal('edit_account_holder_name');
						if (!resultData.bank_name || !resultData.bank_branch || !resultData.account_number || !resultData.account_holder_name) {
							Swal.showValidationMessage('All Bank details are required!');
							return false;
						}
					}
					if (payMethod === 'ezcash' || payMethod === 'all') {
						resultData.ezcash_mobile = getVal('edit_ezcash_mobile');
					}
					if (payMethod === 'mcash' || payMethod === 'all') {
						resultData.mcash_mobile = getVal('edit_mcash_mobile');
					}
					
					if (user.role === 'lead_farmer') {
						resultData.group_name = user.lead_farmer.group_name;
						resultData.group_number = user.lead_farmer.group_number;
					}
				} else if (user.role === 'buyer') {
					resultData.residential_address = getVal('edit_residential_address');
					resultData.district = getVal('edit_district');
					resultData.business_name = getVal('edit_business_name');
					resultData.business_type = getVal('edit_business_type');
				} else if (user.role === 'facilitator') {
					resultData.assigned_division = getVal('edit_assigned_division');
					if (!resultData.assigned_division) {
						Swal.showValidationMessage('Assigned Division is required!');
						return false;
					}
				}
			}

			return resultData;
		}
	}).then(result => {
		if (result.isConfirmed) {
			updateUser(user.id, result.value);
		}
	});
}

function updateUser(userId, data) {
	Swal.fire({
		title: 'Updating...',
		html: '<div class="spinner"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});

	fetch(`/facilitator/users/${userId}/update`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		},
		body: JSON.stringify(data)
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			Swal.fire({
				title: 'Success!',
				text: 'User updated successfully',
				icon: 'success',
				confirmButtonColor: '#10B981'
			}).then(() => {
				window.location.reload();
			});
		} else {
			Swal.fire('Error', data.message || 'Update failed', 'error');
		}
	})
	.catch(() => {
		Swal.fire('Error', 'Update failed', 'error');
	});
}

function toggleUserStatus(userId, action) {
	const actionText = action === 'deactivate' ? 'Deactivate' : 'Activate';
	const confirmText = action === 'deactivate' 
		? 'This user will lose access to the system.' 
		: 'This user will regain access to the system.';

	Swal.fire({
		title: `${actionText} User?`,
		text: confirmText,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: `Yes, ${actionText}`,
		confirmButtonColor: action === 'deactivate' ? '#dc2626' : '#10B981',
		cancelButtonText: 'Cancel'
	}).then(result => {
		if (result.isConfirmed) {
			Swal.fire({
				title: 'Processing...',
				html: '<div class="spinner"></div>',
				showConfirmButton: false,
				allowOutsideClick: false
			});

			fetch(`/facilitator/users/${userId}/${action}`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					Swal.fire({
						title: 'Success!',
						text: `User ${actionText}d successfully`,
						icon: 'success',
						confirmButtonColor: '#10B981'
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire('Error', data.message || 'Operation failed', 'error');
				}
			})
			.catch(() => {
				Swal.fire('Error', 'Operation failed', 'error');
			});
		}
	});
}
</script>
@endsection