@extends('layout.business')

@section('title', 'Add New User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Add New User</h2>
        <p class="text-muted mb-0">Create a new end user for your company</p>
    </div>
    <a href="{{ route('business.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-plus me-2"></i>User Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('business.users.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="bi bi-info-circle me-2 mt-1"></i>
                            <div>
                                <strong>Secure Link Access:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>A 48-hour secure link will be automatically generated</li>
                                    <li>The link will be sent to the user's email address</li>
                                    <li>Users can access the system without a password using this link</li>
                                    <li>You can regenerate or extend the link anytime from the user management page</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('business.users.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>What happens next?
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">1. User Creation</h6>
                        <p class="text-muted small">A new user account will be created with the provided information.</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">2. Secure Link Generation</h6>
                        <p class="text-muted small">A unique 48-hour secure link will be generated automatically.</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">3. Email Notification</h6>
                        <p class="text-muted small">The secure link will be sent to the user's email address.</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">4. User Access</h6>
                        <p class="text-muted small">The user can immediately access the system using the secure link.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
