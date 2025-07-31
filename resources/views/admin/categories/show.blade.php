@extends('layout.admin')

@section('content')
<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Category Details</h1>
                <p class="text-muted">View category information and subcategories</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Categories
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Category Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Category Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category Name</label>
                                    <p class="mb-0">{{ $category->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Slug</label>
                                    <p class="mb-0"><code>{{ $category->slug }}</code></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <p class="mb-0">
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created Date</label>
                                    <p class="mb-0">{{ $category->created_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $category->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category ID</label>
                                    <p class="mb-0">#{{ $category->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subcategories -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Subcategories</h5>
                        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i>Add Subcategory
                        </a>
                    </div>
                    <div class="card-body">
                        @if($category->subcategories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->subcategories as $subcategory)
                                            <tr>
                                                <td>{{ $subcategory->name }}</td>
                                                <td><code class="text-muted">{{ $subcategory->slug }}</code></td>
                                                <td>
                                                    @if($subcategory->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $subcategory->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" 
                                                           class="btn btn-outline-warning" 
                                                           title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.subcategories.toggle-status', $subcategory) }}" 
                                                              method="POST" 
                                                              class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $subcategory->is_active ? 'warning' : 'success' }}" 
                                                                    title="{{ $subcategory->is_active ? 'Deactivate' : 'Activate' }}">
                                                                <i class="bi bi-{{ $subcategory->is_active ? 'pause' : 'play' }}"></i>
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
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-list-nested display-4 mb-3"></i>
                                <h6>No Subcategories</h6>
                                <p>This category doesn't have any subcategories yet.</p>
                                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-2"></i>Add First Subcategory
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-primary mb-1">{{ $category->subcategories_count }}</h4>
                                    <small class="text-muted">Total Subcategories</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-success mb-1">{{ $category->active_subcategories_count }}</h4>
                                    <small class="text-muted">Active Subcategories</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-warning mb-1">{{ $category->subcategories_count - $category->active_subcategories_count }}</h4>
                                    <small class="text-muted">Inactive Subcategories</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-info mb-1">{{ $category->subcategories_count > 0 ? round(($category->active_subcategories_count / $category->subcategories_count) * 100) : 0 }}%</h4>
                                    <small class="text-muted">Active Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-warning">
                                <i class="bi bi-pencil me-2"></i>Edit Category
                            </a>
                            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus me-2"></i>Add Subcategory
                            </a>
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }} w-100"
                                        onclick="return confirm('Are you sure you want to {{ $category->is_active ? 'deactivate' : 'activate' }} this category?')">
                                    <i class="bi bi-{{ $category->is_active ? 'pause' : 'play' }} me-2"></i>
                                    {{ $category->is_active ? 'Deactivate' : 'Activate' }} Category
                                </button>
                            </form>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                    <i class="bi bi-trash me-2"></i>Delete Category
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 