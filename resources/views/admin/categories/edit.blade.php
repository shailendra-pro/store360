@extends('layout.admin')

@section('content')
<main class="main-content" style="margin-top: 76px;">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Edit Category</h1>
                <p class="text-muted">Update category information</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Categories
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Category Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $category->name) }}" 
                                       placeholder="Enter category name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The name will be automatically converted to a URL-friendly slug.</div>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="slug" 
                                       value="{{ $category->slug }}" 
                                       readonly>
                                <div class="form-text">This is automatically generated from the category name.</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Category
                                    </label>
                                </div>
                                <div class="form-text">Active categories will be visible to users and can have subcategories.</div>
                            </div>

                            <!-- Category Statistics -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Subcategories</h6>
                                            <h4 class="text-primary mb-0">{{ $category->subcategories_count }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Active Subcategories</h6>
                                            <h4 class="text-success mb-0">{{ $category->active_subcategories_count }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i>Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 