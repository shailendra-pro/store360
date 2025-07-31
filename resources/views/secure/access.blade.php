<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access - Store 360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .access-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .secure-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="access-card p-4 p-md-5">
                    <!-- Secure Icon -->
                    <div class="secure-icon">
                        <i class="bi bi-shield-check text-white fs-1"></i>
                    </div>

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark mb-2">Secure Access Granted</h2>
                        <p class="text-muted">Welcome to your secure area</p>
                    </div>

                    <!-- User Information -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">User Name</label>
                                        <p class="mb-0">{{ $user->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                </div>
                                @if($user->company)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Company</label>
                                        <p class="mb-0">{{ $user->company }}</p>
                                    </div>
                                </div>
                                @endif
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
                            </div>
                        </div>
                    </div>

                    <!-- Link Information -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>Secure Link Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Expires:</strong></p>
                                <p class="mb-0">{{ $user->secure_link_expires_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Remaining Time:</strong></p>
                                <p class="mb-0 text-success">{{ $user->remaining_hours }} hours</p>
                            </div>
                        </div>
                    </div>

                    <!-- Access Options -->
                    <div class="text-center">
                        <h6 class="text-primary mb-3">What would you like to do?</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Access Dashboard
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-outline-info w-100">
                                    <i class="bi bi-file-earmark-text me-2"></i>View Documents
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-outline-success w-100">
                                    <i class="bi bi-download me-2"></i>Download Files
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            This secure link will expire automatically. Contact your administrator for assistance.
                        </p>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>Last accessed: {{ now()->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
