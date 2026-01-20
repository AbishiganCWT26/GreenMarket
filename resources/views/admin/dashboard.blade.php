@extends('admin.layouts.admin_master')
@section('title', 'Admin Panel')
@section('page-title', 'Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endsection

@section('content')
    <main class="main-content">
        <section class="hero-banner">
            <h2 class="hero-title">System Administration</h2>
            <p class="hero-subtitle">Manage users, products, taxonomy and system configuration for GreenMarket.</p>
        </section>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon-box icon-blue"><i class="fa-solid fa-people-group"></i></div>
                <div class="stat-info">
                    <div class="stat-number" id="totalUsers">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Active Users</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box icon-green">
                    <span class="custom-icon">
                        {!! file_get_contents(public_path('assets/icons/farmer-icon-white.svg')) !!}
                    </span>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="farmersValue">{{ $farmers }}</div>
                    <div class="stat-label">Farmers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box icon-purple"><i class="fa-solid fa-box"></i></div>
                <div class="stat-info">
                    <div class="stat-number" id="productsValue">{{ $products }}</div>
                    <div class="stat-label">Products</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-box icon-yellow"><i class="fa-solid fa-chart-simple"></i></div>
                <div class="stat-info">
                    <div class="stat-number" id="salesValue">Rs. {{ number_format($sales, 2) }}</div>
                    <div class="stat-label">Total Sales</div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="content-card">
                <h4 class="card-header">User Management</h4>
                <div class="user-list">
                    <div class="user-list-item"><span>Admins:</span><span>{{ $admins }}</span></div>
                    <div class="user-list-item"><span>Lead Farmers:</span><span>{{ $leadFarmers }}</span></div>
                    <div class="user-list-item"><span>Farmers:</span><span>{{ $farmers }}</span></div>
                    <div class="user-list-item"><span>Buyers:</span><span>{{ $buyers }}</span></div>
                    <div class="user-list-item"><span>Facilitators:</span><span>{{ $facilitators }}</span></div>
                </div>
            </div>

            <div class="quick-actions">
                <h3 class="widget-title"><i class="fa-solid fa-bolt accent-amber"></i> Quick Actions</h3>
                <ul class="action-list">
                    <li><a href="{{ url('/admin/users') }}"><i class="fa-solid fa-users-gear action-icon"></i> Manage users</a></li>
                    <li><a href="{{ url('/admin/users/create') }}"><i class="fa-solid fa-user-plus action-icon"></i> Register Admin / Facilitator</a></li>
                    <li><a href="{{ url('/admin/taxonomy/create') }}"><i class="fa-solid fa-plus action-icon"></i> Add Product Category</a></li>
                    <li><a href="{{ url('/admin/reports/generate') }}"><i class="fa-solid fa-file-invoice action-icon"></i> Generate Report</a></li>
                    <li><a href="{{ url('/admin/announcements/create') }}"><i class="fa-solid fa-bullhorn action-icon"></i> Publish Announcement</a></li>
                </ul>
            </div>
        </div>

        <div class="table-container">
            <h4 class="widget-title"><i class="fa-solid fa-table-list accent"></i> Recent Lead Farmer Groups</h4>
            <div class="overflow-x">
                <table class="data-table mobile-table" id="groups-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Group Name</th>
                            <th>Total Sales (LKR)</th>
                            <th>Active Farmers</th>
                            <th>Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($groups) == 0)
                            <tr><td colspan="5" class="text-center">No data available</td></tr>
                        @else
                            @foreach($groups as $g)
                            <tr>
                                <td data-label="Rank">{{ $g->rank }}</td>
                                <td data-label="Group Name">{{ $g->group_name }}</td>
                                <td data-label="Total Sales">Rs. {{ number_format($g->total_sales, 2) }}</td>
                                <td data-label="Active Farmers">{{ $g->active_farmers }}</td>
                                <td data-label="Success Rate">{{ $g->success_rate }}%</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-container complaints-section">
            <h4 class="widget-title"><i class="fa-solid fa-comments-question accent"></i> Recent Complaints</h4>
            <div class="overflow-x">
                <table class="data-table mobile-table" id="complaints-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Complainant</th>
                            <th>Against</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($complaints) == 0)
                            <tr><td colspan="8" class="text-center">No complaints</td></tr>
                        @else
                            @foreach($complaints as $c)
                            <tr>
                                <td data-label="ID">{{ $c->id }}</td>
                                <td data-label="Complainant">{{ $c->complainant_name ?? '—' }}</td>
                                <td data-label="Against">
                                    @php
                                        $against = $c->against_user_id ? \DB::table('users')->where('id', $c->against_user_id)->value('username') : null;
                                    @endphp
                                    {{ $against ?? '—' }}
                                </td>
                                <td data-label="Type">{{ ucfirst(str_replace('_',' ', $c->complaint_type)) }}</td>
                                <td data-label="Description">{{ Str::limit($c->description, 80) }}</td>
                                <td data-label="Status">
                                    <span class="status {{ $c->status }}">{{ ucfirst(str_replace('_',' ', $c->status)) }}</span>
                                </td>
                                <td data-label="Created At">{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Actions">
                                    <a href="{{ url('/admin/complaints/'.$c->id) }}" class="btn btn-sm">View</a>
                                    @if($c->status != 'resolved')
                                        <button class="btn btn-sm alert-facilitator" data-id="{{ $c->id }}">Alert Facilitator</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebarOpen = document.getElementById('sidebar-open');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('drawerOverlay');

        mobileMenuBtn && mobileMenuBtn.addEventListener('click', function(){
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.classList.add('no-scroll');
        });

        sidebarClose && sidebarClose.addEventListener('click', function(){
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        overlay && overlay.addEventListener('click', function(){
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        // Notification functionality
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        notifBtn.addEventListener('click', function(e){
            const shown = notifDropdown.getAttribute('aria-hidden') === 'false';
            notifDropdown.setAttribute('aria-hidden', shown ? 'true' : 'false');
        });
        document.addEventListener('click', function(e){
            if(!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)){
                notifDropdown.setAttribute('aria-hidden', 'true');
            }
        });

        const markAllBtn = document.getElementById('markAllRead');
        if(markAllBtn){
            markAllBtn.addEventListener('click', function(e){
                e.preventDefault();
                fetch("{{ url('/admin/notifications/mark-all-read') }}", {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
                    body: JSON.stringify({})
                }).then(r=>r.json()).then(res=>{
                    location.reload();
                }).catch(()=>{ Swal.fire('Error','Unable to mark all as read','error') });
            });
        }

        document.querySelectorAll('.mark-single').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const id = this.dataset.id;
                fetch("{{ url('/admin/notifications/mark-read') }}", {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
                    body: JSON.stringify({id:id})
                }).then(r=>r.json()).then(res=>{
                    location.reload();
                }).catch(()=>{ Swal.fire('Error','Unable to mark notification','error') });
            });
        });

        // Logout functionality
        const logoutBtn = document.getElementById('logout-button');
        if(logoutBtn){
            logoutBtn.addEventListener('click', function(e){
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });
        }
        const logoutTop = document.getElementById('logoutTop');
        if(logoutTop){
            logoutTop.addEventListener('click', function(){
                document.getElementById('logout-form-top').submit();
            });
        }

        // Alert facilitator functionality
        document.querySelectorAll('.alert-facilitator').forEach(function(btn){
            btn.addEventListener('click', function(){
                const id = this.dataset.id;
                fetch("{{ url('/admin/complaints/alert') }}", {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
                    body: JSON.stringify({id:id})
                }).then(r=>r.json()).then(res=>{
                    Swal.fire('Alert sent','Facilitator alerted successfully','success');
                }).catch(()=>{ Swal.fire('Error','Unable to alert facilitator','error') });
            });
        });
    });
</script>
@endsection

