@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-box me-2"></i>Manage Products
                </h1>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </a>
            </div>

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

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by title...">
                        </div>
                        <div class="col-md-3">
                            <label for="subcategory_id" class="form-label">Subcategory</label>
                            <select class="form-select" id="subcategory_id" name="subcategory_id">
                                <option value="">All Subcategories</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" 
                                            {{ request('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->category->name }} > {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="company_id" class="form-label">Company</label>
                            <select class="form-select" id="company_id" name="company_id">
                                <option value="">All Companies</option>
                                <option value="global" {{ request('company_id') === 'global' ? 'selected' : '' }}>
                                    Global Products
                                </option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" 
                                            {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->business_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Products
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Products
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('is_active', true)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Global Products
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('is_global', true)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-globe fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Low Stock Items
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Company</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <img src="{{ $product->main_image_url }}" 
                                                 alt="{{ $product->title }}" 
                                                 class="rounded" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $product->title }}</div>
                                            @if($product->sku)
                                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $product->category_name }}</div>
                                            <small class="text-muted">{{ $product->subcategory_name }}</small>
                                        </td>
                                        <td>
                                            @if($product->is_global)
                                                <span class="badge bg-info">Global</span>
                                            @else
                                                {{ $product->company_name }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $product->formatted_price }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $product->stock_status_color }}">
                                                {{ $product->stock_status }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $product->stock_quantity }} units</small>
                                        </td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $product->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.products.toggle-status', $product) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}"
                                                            onclick="return confirm('Are you sure you want to {{ $product->is_active ? 'deactivate' : 'activate' }} this product?')">
                                                        <i class="bi bi-{{ $product->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.products.destroy', $product) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-box fa-3x mb-3"></i>
                                                <h5>No products found</h5>
                                                <p>Start by adding your first product.</p>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle me-2"></i>Add Product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 