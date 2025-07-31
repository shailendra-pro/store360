@extends('layout.admin')
@section('content')

<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Subcategories</h1>
                <p class="text-muted">Manage your subcategories</p>
            </div>
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-2"></i>Add Subcategory
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Subcategories Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">All Subcategories</h5>
            </div>
            <div class="card-body p-0">
                @if($subcategories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subcategories as $subcategory)
                                    <tr>
                                        <td>{{ $subcategory->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                                    <i class="bi bi-diagram-3 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $subcategory->name }}</h6>
                                                    <small class="text-muted">{{ $subcategory->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $subcategory->category->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($subcategory->description)
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $subcategory->description }}">
                                                    {{ Str::limit($subcategory->description, 50) }}
                                                </span>
                                            @else
                                                <span class="text-muted">No description</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($subcategory->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $subcategory->sort_order }}</td>
                                        <td>{{ $subcategory->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.subcategories.show', $subcategory) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.subcategories.edit', $subcategory) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.subcategories.toggle-status', $subcategory) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle Status">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this subcategory?')">
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
                @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-diagram-3 display-4 mb-3"></i>
                            <h5>No subcategories found</h5>
                            <p>Get started by creating your first subcategory.</p>
                            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus me-2"></i>Create Subcategory
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            @if($subcategories->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $subcategories->firstItem() }} to {{ $subcategories->lastItem() }} of {{ $subcategories->total() }} results
                        </div>
                        <div>
                            {{ $subcategories->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

@endsection 