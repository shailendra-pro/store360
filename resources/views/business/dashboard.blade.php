<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard - {{ $business->business_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
        }
        .business-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-building me-2"></i>Store 360 Business
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="{{ $business->logo_url }}" alt="{{ $business->business_name }}" class="business-logo me-2">
                        <span>{{ $business->business_name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('business.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
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
                                <p class="text-muted mb-0">Manage your business operations and view important information.</p>
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
                                <h4 class="mb-0">150</h4>
                                <p class="mb-0 opacity-75">Total Products</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-box-seam fs-1 opacity-75"></i>
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
                                <h4 class="mb-0">25</h4>
                                <p class="mb-0 opacity-75">Active Orders</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-cart-check fs-1 opacity-75"></i>
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
                                <h4 class="mb-0">$12,450</h4>
                                <p class="mb-0 opacity-75">Monthly Revenue</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-currency-dollar fs-1 opacity-75"></i>
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
                                <h4 class="mb-0">89%</h4>
                                <p class="mb-0 opacity-75">Customer Satisfaction</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-heart fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Business Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Business Name:</strong></p>
                                <p class="text-muted">{{ $business->business_name }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Contact Email:</strong></p>
                                <p class="text-muted">{{ $business->contact_email }}</p>
                            </div>
                        </div>
                        @if($business->phone)
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Phone:</strong></p>
                                <p class="text-muted">{{ $business->phone }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Address:</strong></p>
                                <p class="text-muted">{{ $business->address ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($business->business_description)
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1"><strong>Description:</strong></p>
                                <p class="text-muted">{{ $business->business_description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Account Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Account Status:</strong></p>
                                @if($business->isBusinessActive())
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Username:</strong></p>
                                <p class="text-muted">{{ $business->username }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Expiration Date:</strong></p>
                                @if($business->expires_at)
                                    <p class="text-muted">{{ $business->expires_at->format('M d, Y') }}</p>
                                    @if($business->expires_at->isPast())
                                        <small class="text-danger">Expired</small>
                                    @elseif($business->expires_at->diffInDays(now()) <= 30)
                                        <small class="text-warning">Expires soon</small>
                                    @endif
                                @else
                                    <p class="text-muted">No expiration</p>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Member Since:</strong></p>
                                <p class="text-muted">{{ $business->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
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
                                <a href="#" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Add Product
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-success w-100">
                                    <i class="bi bi-cart-plus me-2"></i>View Orders
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-info w-100">
                                    <i class="bi bi-graph-up me-2"></i>Analytics
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
