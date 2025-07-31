@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Business Accounts</h1>
                <p class="text-muted">Manage business user accounts</p>
            </div>
            <a href="{{ route('admin.businesses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-2"></i>Add Business
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Business Accounts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Business Accounts</h5>
            </div>
            <div class="card-body p-0">
                @if($businesses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Business</th>
                                    <th>Contact Info</th>
                                    <th>Login Details</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businesses as $business)
                                    <tr>
                                        <td>{{ $business->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $business->logo_url }}"
                                                     alt="{{ $business->business_name }}"
                                                     class="rounded-circle me-3"
                                                     width="40"
                                                     height="40"
                                                     style="object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ $business->business_name }}</h6>
                                                    <small class="text-muted">{{ $business->username }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="small text-muted">{{ $business->contact_email }}</div>
                                                @if($business->phone)
                                                    <div class="small text-muted">{{ $business->phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="small"><strong>Username:</strong> {{ $business->username }}</div>
                                                <div class="small text-muted">Password: ••••••••</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($business->isBusinessActive())
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Active
                                                </span>
                                            @elseif(!$business->is_active)
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle me-1"></i>Disabled
                                                </span>
                                            @elseif($business->expires_at && $business->expires_at->isPast())
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>Expired
                                                </span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="bi bi-info-circle me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($business->expires_at)
                                                <div class="small">
                                                    {{ $business->expires_at->format('M d, Y') }}
                                                </div>
                                                @if($business->expires_at->isPast())
                                                    <div class="small text-danger">Expired</div>
                                                @elseif($business->expires_at->diffInDays(now()) <= 30)
                                                    <div class="small text-warning">Expires soon</div>
                                                @endif
                                            @else
                                                <span class="text-muted">No expiration</span>
                                            @endif
                                        </td>
                                        <td>{{ $business->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.businesses.show', $business) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.businesses.edit', $business) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.businesses.toggle-status', $business) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle Status">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this business account?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-building display-4 mb-3"></i>
                            <h5>No business accounts found</h5>
                            <p>Get started by creating your first business account.</p>
                            <a href="{{ route('admin.businesses.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus me-2"></i>Create Business Account
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($businesses->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $businesses->firstItem() }} to {{ $businesses->lastItem() }} of {{ $businesses->total() }} results
                        </div>
                        <div>
                            {{ $businesses->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

@endsection
