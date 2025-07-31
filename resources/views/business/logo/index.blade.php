@extends('layout.business')

@section('title', 'Logo Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Logo Management</h2>
        <p class="text-muted mb-0">Manage your company's branding and logo</p>
    </div>
</div>

<div class="row">
    <!-- Current Logo -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-image me-2"></i>Current Logo
                </h5>
            </div>
            <div class="card-body text-center">
                @if($business->logo_path)
                    <div class="mb-3">
                        <img src="{{ $business->logo_url }}"
                             alt="{{ $business->business_name }} Logo"
                             class="img-fluid rounded border"
                             style="max-width: 300px; max-height: 100px; object-fit: contain;">
                    </div>
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">File Size:</small>
                            <p class="mb-1">{{ number_format(Storage::disk('public')->size($business->logo_path) / 1024, 1) }} KB</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Uploaded:</small>
                            <p class="mb-1">{{ $business->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLogoModal">
                            <i class="bi bi-pencil me-2"></i>Edit Logo
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteLogoModal">
                            <i class="bi bi-trash me-2"></i>Delete Logo
                        </button>
                    </div>
                @else
                    <div class="py-5">
                        <i class="bi bi-image display-6 text-muted mb-3"></i>
                        <h6 class="text-muted">No logo uploaded</h6>
                        <p class="text-muted mb-3">Upload your company logo to enhance your branding.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadLogoModal">
                            <i class="bi bi-upload me-2"></i>Upload Logo
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Logo Requirements -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Logo Requirements
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h6 class="text-primary">File Format</h6>
                        <p class="text-muted mb-0">PNG format with transparent background</p>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-primary">Dimensions</h6>
                        <p class="text-muted mb-0">Minimum: 300x100 pixels<br>Recommended: 600x200 pixels</p>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-primary">File Size</h6>
                        <p class="text-muted mb-0">Maximum: 2 MB</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-primary">Background</h6>
                        <p class="text-muted mb-0">Transparent background recommended for best display on light and dark themes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Preview -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>Logo Preview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <h6 class="text-muted mb-2">Light Background</h6>
                        <div class="bg-light p-3 rounded mb-2" style="min-height: 80px; display: flex; align-items: center; justify-content: center;">
                            @if($business->logo_path)
                                <img src="{{ $business->logo_url }}"
                                     alt="Logo on light background"
                                     style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            @else
                                <span class="text-muted">No logo</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <h6 class="text-muted mb-2">Dark Background</h6>
                        <div class="bg-dark p-3 rounded mb-2" style="min-height: 80px; display: flex; align-items: center; justify-content: center;">
                            @if($business->logo_path)
                                <img src="{{ $business->logo_url }}"
                                     alt="Logo on dark background"
                                     style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            @else
                                <span class="text-muted text-white-50">No logo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Logo Modal -->
<div class="modal fade" id="uploadLogoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Company Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('business.logo.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="logo" class="form-label">Select Logo File</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                               id="logo" name="logo" accept=".png" required>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">PNG format only, max 2MB, min 300x100px</div>
                    </div>

                    <div id="logoPreview" class="text-center" style="display: none;">
                        <h6 class="text-muted mb-2">Preview</h6>
                        <img id="previewImage" src="" alt="Logo preview"
                             class="img-fluid rounded border"
                             style="max-width: 300px; max-height: 100px; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Logo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Logo Modal -->
<div class="modal fade" id="editLogoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Replace Company Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('business.logo.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img src="{{ $business->logo_url }}"
                             alt="Current logo"
                             class="img-fluid rounded border mb-3"
                             style="max-width: 200px; max-height: 80px; object-fit: contain;">
                        <p class="text-muted">Current logo will be replaced</p>
                    </div>

                    <div class="mb-3">
                        <label for="editLogo" class="form-label">Select New Logo File</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                               id="editLogo" name="logo" accept=".png" required>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">PNG format only, max 2MB, min 300x100px</div>
                    </div>

                    <div id="editLogoPreview" class="text-center" style="display: none;">
                        <h6 class="text-muted mb-2">New Logo Preview</h6>
                        <img id="editPreviewImage" src="" alt="New logo preview"
                             class="img-fluid rounded border"
                             style="max-width: 200px; max-height: 80px; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-2"></i>Replace Logo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Logo Modal -->
<div class="modal fade" id="deleteLogoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Company Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle display-6 text-warning mb-3"></i>
                    <h6>Are you sure you want to delete your logo?</h6>
                    <p class="text-muted">This action cannot be undone. Your company will revert to using a default placeholder logo.</p>
                </div>
                <div class="text-center">
                    <img src="{{ $business->logo_url }}"
                         alt="Logo to be deleted"
                         class="img-fluid rounded border"
                         style="max-width: 200px; max-height: 80px; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('business.logo.delete') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Logo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Logo preview functionality
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('logoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editLogo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editPreviewImage').src = e.target.result;
            document.getElementById('editLogoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
