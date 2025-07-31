@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-eye me-2"></i>Product Details: {{ $product->title }}
                </h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Product
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
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

            <div class="row">
                <!-- Product Images -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-images me-2"></i>Product Images
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($product->images->count() > 0)
                                <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        @foreach($product->images as $index => $image)
                                            <button type="button" data-bs-target="#productImageCarousel" 
                                                    data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index === 0 ? 'active' : '' }}"
                                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                                    aria-label="Slide {{ $index + 1 }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner">
                                        @foreach($product->images as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $image->image_url }}" class="d-block w-100" 
                                                     alt="{{ $image->alt_text }}" style="height: 400px; object-fit: cover;">
                                                <div class="carousel-caption d-none d-md-block">
                                                    @if($image->is_primary)
                                                        <span class="badge bg-primary">Primary Image</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($product->images->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Thumbnail Navigation -->
                                <div class="row mt-3">
                                    @foreach($product->images as $index => $image)
                                        <div class="col-3">
                                            <img src="{{ $image->image_url }}" class="img-thumbnail" 
                                                 alt="{{ $image->alt_text }}" 
                                                 style="height: 80px; object-fit: cover; cursor: pointer;"
                                                 onclick="$('#productImageCarousel').carousel({{ $index }})">
                                            @if($image->is_primary)
                                                <div class="text-center mt-1">
                                                    <small class="badge bg-primary">Primary</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-image fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Images</h5>
                                    <p class="text-muted">This product doesn't have any images yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Product Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Title:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $product->title }}
                                </div>
                            </div>

                            @if($product->sku)
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>SKU:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <code>{{ $product->sku }}</code>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Category:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $product->category_name }} > {{ $product->subcategory_name }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Company:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($product->is_global)
                                        <span class="badge bg-info">Global (All Companies)</span>
                                    @else
                                        {{ $product->company_name }}
                                    </div>
                                @endif
                            </div>

                            @if($product->price)
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Price:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="h5 text-success">{{ $product->formatted_price }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Stock:</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $product->stock_status_color }}">
                                        {{ $product->stock_status }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $product->stock_quantity }} units available</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $product->created_at->format('M d, Y \a\t g:i A') }}
                                    <br>
                                    <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $product->updated_at->format('M d, Y \a\t g:i A') }}
                                    <br>
                                    <small class="text-muted">{{ $product->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-file-text me-2"></i>Description
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $product->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Specifications -->
            @if($product->specifications && count($product->specifications) > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-list-check me-2"></i>Specifications
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($product->specifications as $key => $value)
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $key }}:</strong>
                                                <span>{{ $value }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Edit Product
                                </a>
                                
                                <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }}"
                                            onclick="return confirm('Are you sure you want to {{ $product->is_active ? 'deactivate' : 'activate' }} this product?')">
                                        <i class="bi bi-{{ $product->is_active ? 'pause' : 'play' }} me-2"></i>
                                        {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                        <i class="bi bi-trash me-2"></i>Delete Product
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 