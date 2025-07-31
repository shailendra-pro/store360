@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">User Details</h1>
                <p class="text-muted">View and manage user account information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- User Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person me-2"></i>{{ $user->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <p class="mb-0">{{ $user->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <p class="mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Company</label>
                                    <p class="mb-0">{{ $user->company ?: 'Not assigned' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Role</label>
                                    <div>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-check me-1"></i>Admin
                                            </span>
                                        @elseif($user->role === 'business')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-building me-1"></i>Business
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-person me-1"></i>User
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account Status</label>
                                    <div>
                                        @if($user->is_active)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account ID</label>
                                    <p class="mb-0">{{ $user->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created</label>
                                    <p class="mb-0">{{ $user->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secure Link Management Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-link-45deg me-2"></i>Secure Link Management
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($user->secure_link)
                            <!-- Existing Secure Link -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert {{ $user->isSecureLinkValid() ? 'alert-success' : 'alert-warning' }}">
                                        <h6 class="alert-heading">
                                            @if($user->isSecureLinkValid())
                                                <i class="bi bi-check-circle me-2"></i>Secure Link Active
                                            @else
                                                <i class="bi bi-clock me-2"></i>Secure Link Expired
                                            @endif
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <p class="mb-2"><strong>Secure Link URL:</strong></p>
                                                <div class="input-group">
                                                    <input type="text"
                                                           class="form-control"
                                                           value="{{ $user->secure_link_url }}"
                                                           readonly>
                                                    <button class="btn btn-outline-secondary"
                                                            type="button"
                                                            onclick="copyToClipboard('{{ $user->secure_link_url }}')">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Expires:</strong></p>
                                                <p class="mb-0">{{ $user->secure_link_expires_at->format('M d, Y H:i') }}</p>
                                                @if($user->isSecureLinkValid())
                                                    <small class="text-success">{{ $user->remaining_hours }} hours remaining</small>
                                                @else
                                                    <small class="text-danger">Expired {{ $user->secure_link_expires_at->diffForHumans() }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Extend Link Form -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">Extend Secure Link</h6>
                                    <form method="POST" action="{{ route('admin.users.extend-secure-link', $user) }}" class="row g-3">
                                        @csrf
                                        <div class="col-md-6">
                                            <label for="additional_hours" class="form-label">Additional Hours</label>
                                            <input type="number"
                                                   class="form-control @error('additional_hours') is-invalid @enderror"
                                                   id="additional_hours"
                                                   name="additional_hours"
                                                   min="1"
                                                   max="8760"
                                                   value="24"
                                                   required>
                                            @error('additional_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="bi bi-clock-plus me-2"></i>Extend Link
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Revoke Link -->
                            <div class="row">
                                <div class="col-12">
                                    <form method="POST" action="{{ route('admin.users.revoke-secure-link', $user) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to revoke this secure link? This action cannot be undone.')">
                                            <i class="bi bi-link-break me-2"></i>Revoke Secure Link
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Generate New Secure Link -->
                            <div class="text-center py-4">
                                <div class="text-muted mb-3">
                                    <i class="bi bi-link-45deg display-4"></i>
                                    <h5>No Secure Link</h5>
                                    <p>This user doesn't have a secure link yet.</p>
                                </div>
                                <form method="POST" action="{{ route('admin.users.generate-secure-link', $user) }}" class="row g-3 justify-content-center">
                                    @csrf
                                    <div class="col-md-4">
                                        <label for="hours" class="form-label">Hours</label>
                                        <input type="number"
                                               class="form-control @error('hours') is-invalid @enderror"
                                               id="hours"
                                               name="hours"
                                               min="1"
                                               max="8760"
                                               value="24"
                                               required>
                                        @error('hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-link-45deg me-2"></i>Generate Secure Link
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Edit User
                            </a>

                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="bi bi-trash me-2"></i>Delete User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Secure Link Statistics -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Link Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0">{{ $user->custom_hours ?: 0 }}</h4>
                                    <small class="text-muted">Total Hours</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div>
                                    <h4 class="text-success mb-0">{{ $user->remaining_hours }}</h4>
                                    <small class="text-muted">Remaining Hours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i>';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>

@endsection
