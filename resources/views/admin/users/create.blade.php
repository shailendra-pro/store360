@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Create New User</h1>
                <p class="text-muted">Add a new user account with secure link</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Users
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-plus-circle me-2"></i>New User Account
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-person me-2"></i>Basic Information
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="Enter full name"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="Enter email address"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company" class="form-label">Company</label>
                                        <select class="form-select @error('company') is-invalid @enderror" id="company" name="company">
                                            <option value="">Select Company</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company }}" {{ old('company') == $company ? 'selected' : '' }}>
                                                    {{ $company }}
                                                </option>
                                            @endforeach
                                            <option value="new" {{ old('company') == 'new' ? 'selected' : '' }}>+ Add New Company</option>
                                        </select>
                                        @error('company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Business</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-key me-2"></i>Password
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Enter password"
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirm password"
                                               required>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Secure Link Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-link-45deg me-2"></i>Secure Link Settings
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="custom_hours" class="form-label">Custom Hours for Secure Link</label>
                                        <input type="number"
                                               class="form-control @error('custom_hours') is-invalid @enderror"
                                               id="custom_hours"
                                               name="custom_hours"
                                               value="{{ old('custom_hours', 24) }}"
                                               min="1"
                                               max="8760"
                                               placeholder="Enter hours (1-8760)">
                                        <div class="form-text">Leave empty to skip secure link generation. Max: 1 year (8760 hours)</div>
                                        @error('custom_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Auto-generate Link</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="auto_generate" checked>
                                            <label class="form-check-label" for="auto_generate">
                                                Automatically generate secure link upon user creation
                                            </label>
                                        </div>
                                        <div class="form-text">Secure link will be generated with the specified hours</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-gear me-2"></i>Account Settings
                                    </h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="is_active"
                                                   name="is_active"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active Account
                                            </label>
                                        </div>
                                        <div class="form-text">Inactive accounts cannot access the system</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create User Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company');
    const customHoursInput = document.getElementById('custom_hours');
    const autoGenerateCheckbox = document.getElementById('auto_generate');

    // Handle new company option
    companySelect.addEventListener('change', function() {
        if (this.value === 'new') {
            const newCompany = prompt('Enter new company name:');
            if (newCompany) {
                // Create new option
                const option = new Option(newCompany, newCompany);
                companySelect.add(option, companySelect.options.length - 1);
                companySelect.value = newCompany;
            } else {
                companySelect.value = '';
            }
        }
    });

    // Handle auto-generate checkbox
    autoGenerateCheckbox.addEventListener('change', function() {
        customHoursInput.disabled = !this.checked;
        if (!this.checked) {
            customHoursInput.value = '';
        } else {
            customHoursInput.value = '24';
        }
    });
});
</script>

@endsection
