@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Business Account Details</h1>
                <p class="text-muted">View and manage business account information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.businesses.edit', $business) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.businesses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Businesses
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-building me-2"></i>{{ $business->business_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Business Logo and Basic Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <img src="{{ $business->logo_url }}"
                                     alt="{{ $business->business_name }}"
                                     class="img-fluid rounded"
                                     style="max-height: 150px;">
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Business Name</label>
                                            <p class="mb-0">{{ $business->business_name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Contact Email</label>
                                            <p class="mb-0">{{ $business->contact_email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Business Description</label>
                                    @if($business->business_description)
                                        <p class="mb-0">{{ $business->business_description }}</p>
                                    @else
                                        <p class="mb-0 text-muted">No description provided</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Login Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-key me-2"></i>Login Information
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Username</label>
                                    <p class="mb-0"><code>{{ $business->username }}</code></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Password</label>
                                    <p class="mb-0 text-muted">•••••••• (hidden for security)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-telephone me-2"></i>Contact Information
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Phone Number</label>
                                    <p class="mb-0">{{ $business->phone ?: 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <p class="mb-0">{{ $business->address ?: 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">City</label>
                                    <p class="mb-0">{{ $business->city ?: 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">State</label>
                                    <p class="mb-0">{{ $business->state ?: 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Postal Code</label>
                                    <p class="mb-0">{{ $business->postal_code ?: 'Not provided' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Country</label>
                                    <p class="mb-0">{{ $business->country ?: 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-gear me-2"></i>Account Status
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account Status</label>
                                    <div>
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
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Expiration Date</label>
                                    @if($business->expires_at)
                                        <p class="mb-0">{{ $business->expires_at->format('M d, Y') }}</p>
                                        @if($business->expires_at->isPast())
                                            <small class="text-danger">Expired</small>
                                        @elseif($business->expires_at->diffInDays(now()) <= 30)
                                            <small class="text-warning">Expires soon</small>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">No expiration</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account ID</label>
                                    <p class="mb-0">{{ $business->id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created</label>
                                    <p class="mb-0">{{ $business->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $business->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.businesses.edit', $business) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Edit Business
                            </a>

                            <form method="POST" action="{{ route('admin.businesses.toggle-status', $business) }}" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    {{ $business->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this business account? This action cannot be undone.')">
                                    <i class="bi bi-trash me-2"></i>Delete Business
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Account Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0">{{ $business->created_at->diffInDays(now()) }}</h4>
                                    <small class="text-muted">Days Active</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div>
                                    <h4 class="text-success mb-0">
                                        @if($business->isBusinessActive())
                                            <i class="bi bi-check-circle"></i>
                                        @else
                                            <i class="bi bi-x-circle text-danger"></i>
                                        @endif
                                    </h4>
                                    <small class="text-muted">Status</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading">About Business Accounts</h6>
                            <p class="mb-0 small">
                                Business accounts allow companies to access the platform with their own credentials.
                                Admins can manage their status, expiration dates, and login details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
