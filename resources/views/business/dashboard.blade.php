@extends('layout.business')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <img src="{{ $business->logo_url }}"
                         alt="{{ $business->business_name }}"
                         class="rounded me-3"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                        <h2 class="mb-1">Welcome back, {{ $business->business_name }}!</h2>
                        <p class="text-muted mb-0">Manage your company's end users and business operations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                        <p class="mb-0 opacity-75">Total Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
                        <p class="mb-0 opacity-75">Active Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person-check fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['users_with_links'] }}</h4>
                        <p class="mb-0 opacity-75">Users with Links</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-link-45deg fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['valid_links'] }}</h4>
                        <p class="mb-0 opacity-75">Valid Links</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Recent Users
                </h5>
                <a href="{{ route('business.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add New User
                </a>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Secure Link</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users->take(5) as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->secure_link)
                                                @if($user->isSecureLinkValid())
                                                    <span class="badge bg-success">Valid</span>
                                                    <br><small class="text-muted">{{ $user->remaining_hours }}h left</small>
                                                @else
                                                    <span class="badge bg-danger">Expired</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">No Link</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('business.users.show', $user->id) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($user->secure_link)
                                                    <form method="POST" action="{{ route('business.users.generate-secure-link', $user->id) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning" title="Generate New Link">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('business.users.index') }}" class="btn btn-outline-primary">
                                View All Users
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-people display-6 mb-3"></i>
                        <p>No users found</p>
                        <a href="{{ route('business.users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add First User
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('business.users.create') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person-plus me-2"></i>Add User
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('business.users.index') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('business.logo.index') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-image me-2"></i>Logo Management
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-warning w-100">
                            <i class="bi bi-graph-up me-2"></i>Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
