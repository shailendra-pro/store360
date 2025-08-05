@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-pencil me-2"></i>Edit Product: {{ $product->title }}
                </h1>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Products
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="title" class="form-label">Product Title *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                               id="title" name="title" value="{{ old('title', $product->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                               id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Optional">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4"
                                              placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="subcategory_id" class="form-label">Subcategory *</label>
                                        <select class="form-select @error('subcategory_id') is-invalid @enderror"
                                                id="subcategory_id" name="subcategory_id" required>
                                            <option value="">Select Subcategory</option>
                                            @foreach($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                        {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                                    {{ $subcategory->category->name }} > {{ $subcategory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subcategory_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="price" class="form-label">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                   id="price" name="price" value="{{ old('price', $product->price) }}"
                                                   step="0.01" min="0" placeholder="0.00">
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                               min="0" placeholder="0">
                                        @error('stock_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Product Status</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active"
                                                   name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active Product
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Assignment -->
                            <div class="col-md-4">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-building me-2"></i>Company Assignment
                                </h5>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_global"
                                               name="is_global" {{ old('is_global', $product->is_global) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_global">
                                            <strong>Global Product</strong>
                                            <br>
                                            <small class="text-muted">Visible to all companies</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3" id="company-selection">
                                    <label for="company_id" class="form-label">Assign to Company</label>
                                    <select class="form-select @error('company_id') is-invalid @enderror"
                                            id="company_id" name="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}"
                                                    {{ old('company_id', $product->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->business_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Product Statistics -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Product Statistics</h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="h4 text-primary">{{ $product->images->count() }}</div>
                                                <small class="text-muted">Images</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="h4 text-success">{{ $product->stock_quantity }}</div>
                                                <small class="text-muted">In Stock</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Current Images -->
                        @if($product->images->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-images me-2"></i>Current Images
                                    </h5>

                                    <div class="row g-3">
                                        @foreach($product->images as $image)
                                            <div class="col-md-3 col-sm-6">
                                                <div class="card">
                                                    <img src="{{ $image->image_url }}" class="card-img-top" alt="{{ $image->alt_text }}"
                                                         style="height: 150px; object-fit: cover;">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            @if($image->is_primary)
                                                                <span class="badge bg-primary">Primary</span>
                                                            @else
                                                                <form action="{{ route('admin.products.set-primary-image', $image) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                        Set Primary
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if($product->images->count() > 1)
                                                                <form action="{{ route('admin.products.delete-image', $image) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                            onclick="return confirm('Are you sure you want to delete this image?')">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Add New Images -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-plus-circle me-2"></i>Add New Images (Optional)
                                </h5>

                                <div class="mb-3">
                                    <label for="images" class="form-label">Upload Additional Images</label>
                                    <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                           id="images" name="images[]" multiple accept="image/*">
                                    <div class="form-text">
                                        You can select multiple images. They will be added to existing images.
                                        Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB each)
                                    </div>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="image-preview" class="row g-3"></div>
                            </div>
                        </div>

                        <hr>

                        <!-- Specifications -->
                        @if(isset($product->specifications['key'], $product->specifications['value']))
                            @foreach($product->specifications['key'] as $i => $key)
                                @php $value = $product->specifications['value'][$i] ?? '' @endphp
                                <div class="row mb-2 specification-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="specifications[key][]" value="{{ $key }}" placeholder="Specification name">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="specifications[value][]" value="{{ $value }}" placeholder="Specification value">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-specification">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @elseif(is_array($product->specifications))
                            @foreach($product->specifications as $key => $value)
                                <div class="row mb-2 specification-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="specifications[key][]" value="{{ $key }}" placeholder="Specification name">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="specifications[value][]" value="{{ $value }}" placeholder="Specification value">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-specification">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif


                        <hr>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global product toggle
    const isGlobalCheckbox = document.getElementById('is_global');
    const companySelection = document.getElementById('company-selection');
    const companySelect = document.getElementById('company_id');

    function toggleCompanySelection() {
        if (isGlobalCheckbox.checked) {
            companySelection.style.display = 'none';
            companySelect.value = '';
        } else {
            companySelection.style.display = 'block';
        }
    }

    isGlobalCheckbox.addEventListener('change', toggleCompanySelection);
    toggleCompanySelection(); // Initial state

    // Image preview
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';

        if (this.files) {
            Array.from(this.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-6';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" alt="Preview"
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted">${file.name}</small>
                                <br><span class="badge bg-info">New</span>
                            </div>
                        </div>
                    `;
                    imagePreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Specifications
    const addSpecBtn = document.getElementById('add-specification');
    const specsContainer = document.getElementById('specifications-container');

    addSpecBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 specification-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="specifications[key][]"
                       placeholder="Specification name">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="specifications[value][]"
                       placeholder="Specification value">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-specification">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        specsContainer.appendChild(newRow);
    });

    // Remove specification
    specsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-specification')) {
            e.target.closest('.specification-row').remove();
        }
    });
});
</script>
@endpush
@endsection
