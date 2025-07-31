<!-- Sidebar -->
<div class="col-lg-3 col-xl-2 p-0">
    <div class="offcanvas-lg offcanvas-start bg-white shadow-sm" tabindex="-1" id="sidebar">
        <div class="offcanvas-header d-lg-none">
            <h5 class="offcanvas-title text-primary fw-bold">
                <i class="bi bi-shield-check me-2"></i>Store 360 Admin
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>

        <div class="offcanvas-body p-0">
            <nav class="sidebar-nav" style="margin-top: 76px;">
                <ul class="nav flex-column">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- Categories -->
                    {{-- <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#categoriesMenu">
                            <i class="bi bi-grid me-2"></i>
                            Categories
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="categoriesMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="#all-categories">
                                        <i class="bi bi-circle me-2"></i>All Categories
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#add-category">
                                        <i class="bi bi-plus-circle me-2"></i>Add Category
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#category-settings">
                                        <i class="bi bi-gear me-2"></i>Category Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}

                    <!-- Business Management -->
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#businessMenu">
                            <i class="bi bi-building me-2"></i>
                            Business Management
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="businessMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.businesses.index') }}">
                                        <i class="bi bi-circle me-2"></i>All Businesses
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.businesses.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>Add Business
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#active-businesses">
                                        <i class="bi bi-check-circle me-2"></i>Active Businesses
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#expired-businesses">
                                        <i class="bi bi-clock me-2"></i>Expired Businesses
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- User Management -->
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#userMenu">
                            <i class="bi bi-people me-2"></i>
                            User Management
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="userMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-circle me-2"></i>All Users
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>Add User
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#secure-links">
                                        <i class="bi bi-link-45deg me-2"></i>Secure Links
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#user-stats">
                                        <i class="bi bi-graph-up me-2"></i>User Statistics
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Subcategories -->
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#subcategoriesMenu">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Subcategories
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="subcategoriesMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.subcategories.index') }}">
                                        <i class="bi bi-circle me-2"></i>All Subcategories
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.subcategories.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>Add Subcategory
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#manage-hierarchy">
                                        <i class="bi bi-diagram-2 me-2"></i>Manage Hierarchy
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Products -->
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#productsMenu">
                            <i class="bi bi-box me-2"></i>
                            Products
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse" id="productsMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="#all-products">
                                        <i class="bi bi-circle me-2"></i>All Products
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#add-product">
                                        <i class="bi bi-plus-circle me-2"></i>Add Product
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#product-inventory">
                                        <i class="bi bi-boxes me-2"></i>Inventory
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#product-reviews">
                                        <i class="bi bi-star me-2"></i>Reviews
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Orders -->
                    <li class="nav-item">
                        <a class="nav-link" href="#orders">
                            <i class="bi bi-cart me-2"></i>
                            Orders
                            <span class="badge bg-primary rounded-pill ms-auto">12</span>
                        </a>
                    </li>

                    <!-- Users -->
                    <li class="nav-item">
                        <a class="nav-link" href="#users">
                            <i class="bi bi-people me-2"></i>
                            Users
                        </a>
                    </li>

                    <!-- Analytics -->
                    <li class="nav-item">
                        <a class="nav-link" href="#analytics">
                            <i class="bi bi-graph-up me-2"></i>
                            Analytics
                        </a>
                    </li>

                    <!-- Settings -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings') }}">
                            <i class="bi bi-gear me-2"></i>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
