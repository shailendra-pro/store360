@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Subcategory Details</h1>
                <p class="text-muted">View subcategory information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Subcategories
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>{{ $subcategory->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Subcategory Name</label>
                                    <p class="mb-0">{{ $subcategory->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Slug</label>
                                    <p class="mb-0"><code>{{ $subcategory->slug }}</code></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Parent Category</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info bg-opacity-10 text-info me-2">
                                    <i class="bi bi-grid me-1"></i>{{ $subcategory->category->name }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            @if($subcategory->description)
                                <p class="mb-0">{{ $subcategory->description }}</p>
                            @else
                                <p class="mb-0 text-muted">No description provided</p>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div>
                                        @if($subcategory->is_active)
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sort Order</label>
                                    <p class="mb-0">{{ $subcategory->sort_order }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created</label>
                                    <p class="mb-0">{{ $subcategory->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $subcategory->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID</label>
                                    <p class="mb-0">{{ $subcategory->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Edit Subcategory
                            </a>

                            <form method="POST" action="{{ route('admin.subcategories.toggle-status', $subcategory) }}" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    {{ $subcategory->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this subcategory? This action cannot be undone.')">
                                    <i class="bi bi-trash me-2"></i>Delete Subcategory
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading">About Subcategories</h6>
                            <p class="mb-0 small">
                                Subcategories help organize your products into more specific groups within main categories.
                                They improve navigation and make it easier for customers to find what they're looking for.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
