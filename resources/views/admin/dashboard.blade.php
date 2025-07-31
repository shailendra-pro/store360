@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Dashboard</h1>
                <p class="text-muted">Welcome back, {{ $user->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-2"></i>Add User
                </a>
                <a href="{{ route('admin.businesses.create') }}" class="btn btn-success">
                    <i class="bi bi-building me-2"></i>Add Business
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-people text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Users</h6>
                                <h4 class="mb-0">{{ number_format($stats['total_users']) }}</h4>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> {{ $stats['active_users'] }} active
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-building text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Businesses</h6>
                                <h4 class="mb-0">{{ number_format($stats['total_businesses']) }}</h4>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> {{ $stats['active_businesses'] }} active
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-link-45deg text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Secure Links</h6>
                                <h4 class="mb-0">{{ number_format($stats['valid_secure_links']) }}</h4>
                                <small class="text-info">
                                    <i class="bi bi-check-circle"></i> {{ $stats['expired_secure_links'] }} expired
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-tags text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Categories</h6>
                                <h4 class="mb-0">{{ number_format($stats['total_categories']) }}</h4>
                                <small class="text-warning">
                                    <i class="bi bi-list"></i> {{ $stats['total_subcategories'] }} subcategories
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="row">
            <div class="col-xl-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Monthly Growth</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="showChart('users')">Users</button>
                            <button type="button" class="btn btn-outline-primary" onclick="showChart('businesses')">Businesses</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="growthChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Expiring Secure Links</h5>
                    </div>
                    <div class="card-body">
                        @if($expiringLinks->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($expiringLinks as $user)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                        <span class="badge bg-warning rounded-pill">
                                            {{ $user->secure_link_expires_at->diffForHumans() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-check-circle display-6 mb-3"></i>
                                <p>No expiring secure links</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Users</h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentUsers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Company</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentUsers as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <span class="text-primary fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $user->name }}</div>
                                                            <small class="text-muted">{{ $user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->company ?: 'N/A' }}</td>
                                                <td>
                                                    @if($user->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-people display-6 mb-3"></i>
                                <p>No users found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Businesses</h5>
                        <a href="{{ route('admin.businesses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentBusinesses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Business</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentBusinesses as $business)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <span class="text-success fw-bold">{{ strtoupper(substr($business->business_name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $business->business_name }}</div>
                                                            <small class="text-muted">@{{ $business->username }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $business->contact_email }}</td>
                                                <td>
                                                    @if($business->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $business->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-building display-6 mb-3"></i>
                                <p>No businesses found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Companies -->
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Top Companies by Users</h5>
                    </div>
                    <div class="card-body">
                        @if($topCompanies->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($topCompanies as $company)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <h6 class="mb-1">{{ $company->company }}</h6>
                                            <small class="text-muted">{{ $company->user_count }} users</small>
                                        </div>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                                            @php
                                                $maxUsers = $topCompanies->max('user_count');
                                                $percentage = ($company->user_count / $maxUsers) * 100;
                                            @endphp
                                            <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $company->user_count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-building display-6 mb-3"></i>
                                <p>No company data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                    <i class="bi bi-person-plus display-6 mb-2"></i>
                                    <span>Add User</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.businesses.create') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                    <i class="bi bi-building-add display-6 mb-2"></i>
                                    <span>Add Business</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                    <i class="bi bi-tags display-6 mb-2"></i>
                                    <span>Add Subcategory</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.users.secure-link-stats') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                    <i class="bi bi-link-45deg display-6 mb-2"></i>
                                    <span>Secure Links</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart data
const chartData = {
    users: {
        labels: @json(collect($monthlyGrowth)->pluck('month')),
        datasets: [{
            label: 'Users',
            data: @json(collect($monthlyGrowth)->pluck('users')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    businesses: {
        labels: @json(collect($monthlyGrowth)->pluck('month')),
        datasets: [{
            label: 'Businesses',
            data: @json(collect($monthlyGrowth)->pluck('businesses')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }]
    }
};

// Initialize chart
const ctx = document.getElementById('growthChart').getContext('2d');
let currentChart = new Chart(ctx, {
    type: 'line',
    data: chartData.users,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Function to switch between charts
function showChart(type) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Update chart data
    currentChart.data = chartData[type];
    currentChart.update();
}
</script>

@endsection
