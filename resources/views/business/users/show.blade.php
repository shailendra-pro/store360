@extends('layout.business')

@section('title', 'User Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">User Details</h2>
        <p class="text-muted mb-0">Manage user information and access</p>
    </div>
    <a href="{{ route('business.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Users
    </a>
</div>

<div class="row">
    <!-- User Information -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>User Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Full Name</label>
                        <p class="fw-medium mb-0">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Email Address</label>
                        <p class="fw-medium mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Company</label>
                        <p class="fw-medium mb-0">{{ $user->company }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Account Status</label>
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Created Date</label>
                        <p class="fw-medium mb-0">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <p class="fw-medium mb-0">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secure Link Management -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-link-45deg me-2"></i>Secure Link Management
                </h5>
            </div>
            <div class="card-body">
                @if($user->secure_link)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Secure Link Status</label>
                            <div>
                                @if($user->isSecureLinkValid())
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Valid
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Expired
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Expires At</label>
                            <p class="fw-medium mb-0">
                                {{ $user->secure_link_expires_at ? $user->secure_link_expires_at->format('M d, Y \a\t g:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($user->isSecureLinkValid())
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Time Remaining</label>
                                <p class="fw-medium mb-0">{{ $user->remaining_hours }} hours</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Secure Link URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ route('secure.access', $user->secure_link) }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('business.users.generate-secure-link', $user->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Generate New Link
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#extendLinkModal">
                                <i class="bi bi-clock me-2"></i>Extend Link
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-link-45deg display-6 text-muted mb-3"></i>
                        <h6 class="text-muted">No secure link generated</h6>
                        <p class="text-muted mb-3">Generate a secure link to allow this user to access the system.</p>
                        <form method="POST" action="{{ route('business.users.generate-secure-link', $user->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-link-45deg me-2"></i>Generate Secure Link
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-gear me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('business.users.toggle-status', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $user->is_active ? 'Disable' : 'Enable' }} User
                        </button>
                    </form>

                    @if($user->secure_link)
                        <button type="button" class="btn btn-outline-info w-100" onclick="sendSecureLinkEmail()">
                            <i class="bi bi-envelope me-2"></i>Resend Email
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Activity -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-activity me-2"></i>Activity Summary
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Account Created</span>
                    <span class="fw-medium">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Last Updated</span>
                    <span class="fw-medium">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                @if($user->secure_link_expires_at)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Link Expires</span>
                        <span class="fw-medium">{{ $user->secure_link_expires_at->diffForHumans() }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Extend Link Modal -->
<div class="modal fade" id="extendLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Secure Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('business.users.extend-secure-link', $user->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="hours" class="form-label">Extend by (hours)</label>
                        <select class="form-select" id="hours" name="hours" required>
                            <option value="1">1 hour</option>
                            <option value="6">6 hours</option>
                            <option value="12">12 hours</option>
                            <option value="24" selected>24 hours</option>
                            <option value="48">48 hours</option>
                            <option value="72">72 hours</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        The secure link will be extended by the selected number of hours from its current expiration time.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Extend Link</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard(button) {
    const input = button.parentElement.querySelector('input');
    input.select();
    document.execCommand('copy');

    // Change button text temporarily
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i>';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');

    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

function sendSecureLinkEmail() {
    // This would typically make an AJAX call to resend the email
    alert('Email resend functionality would be implemented here.');
}
</script>
@endsection
