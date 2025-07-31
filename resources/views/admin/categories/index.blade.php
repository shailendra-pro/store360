@extends('layout.admin')

@section('content')
<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Categories</h1>
                <p class="text-muted">Manage your product categories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-2"></i>Add Category
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Categories Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Categories</h5>
            </div>
            <div class="card-body">
                @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Subcategories</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <span class="text-primary fw-bold">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $category->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="text-muted">{{ $category->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->subcategories_count }}</span>
                                            @if($category->active_subcategories_count > 0)
                                                <small class="text-muted ms-1">({{ $category->active_subcategories_count }} active)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.categories.show', $category) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.categories.toggle-status', $category) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}"
                                                            onclick="return confirm('Are you sure you want to {{ $category->is_active ? 'deactivate' : 'activate' }} this category?')">
                                                        <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.categories.destroy', $category) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $categories->links() }}
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-tags display-1 mb-3"></i>
                        <h5>No Categories Found</h5>
                        <p>Get started by creating your first category.</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i>Add Category
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-tags text-primary display-6 mb-2"></i>
                        <h4 class="mb-1">{{ $categories->total() }}</h4>
                        <p class="text-muted mb-0">Total Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success display-6 mb-2"></i>
                        <h4 class="mb-1">{{ $categories->where('is_active', true)->count() }}</h4>
                        <p class="text-muted mb-0">Active Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-pause-circle text-warning display-6 mb-2"></i>
                        <h4 class="mb-1">{{ $categories->where('is_active', false)->count() }}</h4>
                        <p class="text-muted mb-0">Inactive Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-list-nested text-info display-6 mb-2"></i>
                        <h4 class="mb-1">{{ $categories->sum('subcategories_count') }}</h4>
                        <p class="text-muted mb-0">Total Subcategories</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 