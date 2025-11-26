<div class="sidebar text-dark" id="sidebar">
    <div class="sidebar-menu">
        <nav class="nav flex-column">
            <!-- Core Features -->
            <div class="menu-category-label">Core Features</div>
            
            <!-- Level 1: Dashboard -->
            <div class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt me-3"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Level 1: AJAX Demo -->
            <div class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center {{ request()->routeIs('admin.ajax-demo') ? 'active' : '' }}" 
                   href="{{ route('admin.ajax-demo') }}" data-tooltip="AJAX Demo">
                    <i class="fas fa-code me-3"></i>
                    <span>AJAX Demo</span>
                </a>
            </div>

            <!-- User Interface -->
            <div class="menu-category-label">User Interface</div>
            
            <!-- Level 1: UI Components -->
            <div class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center {{ request()->routeIs('admin.ui-components') ? 'active' : '' }}" 
                   href="{{ route('admin.ui-components') }}" data-tooltip="UI Components">
                    <i class="fas fa-puzzle-piece me-3"></i>
                    <span>UI Components</span>
                </a>
            </div>

            <!-- Level 1: File Manager -->
            <div class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center {{ request()->routeIs('admin.file-manager') ? 'active' : '' }}" 
                   href="{{ route('admin.file-manager') }}" data-tooltip="File Manager">
                    <i class="fas fa-folder-open me-3"></i>
                    <span>File Manager</span>
                </a>
            </div>

            <!-- User Management -->
            <div class="menu-category-label">User Management</div>

            <!-- Level 1: User Management -->
            <div class="nav-item">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" 
                   data-bs-toggle="collapse" data-bs-target="#userManagement" aria-expanded="false" data-tooltip="User Management">
                    <i class="fas fa-users me-3"></i>
                    <span>User Management</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: User Management Sub Menu -->
                <div class="collapse" id="userManagement">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="{{ route('admin.users') }}">
                            <i class="fas fa-user me-3"></i>
                            <span>All Users</span>
                        </a>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#userRoles" aria-expanded="false">
                            <i class="fas fa-user-tag me-3"></i>
                            <span>Roles & Permissions</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: Roles Sub Menu -->
                        <div class="collapse" id="userRoles">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#roles">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Manage Roles</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#permissions">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Manage Permissions</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#assign-roles">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Assign Roles</span>
                                </a>
                            </div>
                        </div>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#user-profile">
                            <i class="fas fa-id-card me-3"></i>
                            <span>User Profiles</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Management -->
            <div class="menu-category-label">Content Management</div>
            
            <!-- Level 1: Content Management -->
            <div class="nav-item">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#contentManagement" aria-expanded="false">
                    <i class="fas fa-file-alt me-3"></i>
                    <span>Content Management</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: Content Management Sub Menu -->
                <div class="collapse" id="contentManagement">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#posts" aria-expanded="false">
                            <i class="fas fa-newspaper me-3"></i>
                            <span>Posts</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: Posts Sub Menu -->
                        <div class="collapse" id="posts">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#all-posts">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>All Posts</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#add-post">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Add New Post</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#categories">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Categories</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#tags">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Tags</span>
                                </a>
                            </div>
                        </div>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#pages" aria-expanded="false">
                            <i class="fas fa-copy me-3"></i>
                            <span>Pages</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: Pages Sub Menu -->
                        <div class="collapse" id="pages">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#all-pages">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>All Pages</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#add-page">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Add New Page</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#page-templates">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Page Templates</span>
                                </a>
                            </div>
                        </div>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#media">
                            <i class="fas fa-images me-3"></i>
                            <span>Media Library</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- E-Commerce -->
            <div class="menu-category-label">E-Commerce</div>
            
            <!-- Level 1: E-Commerce -->
            <div class="nav-item">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#ecommerce" aria-expanded="false">
                    <i class="fas fa-shopping-cart me-3"></i>
                    <span>E-Commerce</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: E-Commerce Sub Menu -->
                <div class="collapse" id="ecommerce">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#products" aria-expanded="false">
                            <i class="fas fa-box me-3"></i>
                            <span>Products</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: Products Sub Menu -->
                        <div class="collapse" id="products">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#all-products">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>All Products</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#add-product">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Add Product</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#product-categories">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Product Categories</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#inventory">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Inventory</span>
                                </a>
                            </div>
                        </div>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="{{ route('admin.orders') }}">
                            <i class="fas fa-receipt me-3"></i>
                            <span>Orders</span>
                        </a>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#customers">
                            <i class="fas fa-user-friends me-3"></i>
                            <span>Customers</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Analytics & Reports -->
            <div class="menu-category-label">Analytics & Reports</div>
            
            <!-- Level 1: Reports & Analytics -->
            <div class="nav-item">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#reports" aria-expanded="false">
                    <i class="fas fa-chart-bar me-3"></i>
                    <span>Reports & Analytics</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: Reports Sub Menu -->
                <div class="collapse" id="reports">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#sales-reports">
                            <i class="fas fa-chart-line me-3"></i>
                            <span>Sales Reports</span>
                        </a>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#user-analytics">
                            <i class="fas fa-user-chart me-3"></i>
                            <span>User Analytics</span>
                        </a>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#customReports" aria-expanded="false">
                            <i class="fas fa-cog me-3"></i>
                            <span>Custom Reports</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: Custom Reports Sub Menu -->
                        <div class="collapse" id="customReports">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#report-builder">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Report Builder</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#saved-reports">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Saved Reports</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#scheduled-reports">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Scheduled Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Configuration -->
            <div class="menu-category-label">System Configuration</div>
            
            <!-- Level 1: Settings -->
            <div class="nav-item">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="false">
                    <i class="fas fa-cogs me-3"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: Settings Sub Menu -->
                <div class="collapse" id="settings">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#general-settings">
                            <i class="fas fa-sliders-h me-3"></i>
                            <span>General Settings</span>
                        </a>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#systemSettings" aria-expanded="false">
                            <i class="fas fa-server me-3"></i>
                            <span>System Settings</span>
                            <i class="fas fa-chevron-down ms-auto"></i>
                        </a>
                        <!-- Level 3: System Settings Sub Menu -->
                        <div class="collapse" id="systemSettings">
                            <div class="nav flex-column ms-3">
                                <a class="nav-link text-muted px-3 py-1" href="#email-config">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Email Configuration</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#cache-settings">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Cache Settings</span>
                                </a>
                                <a class="nav-link text-muted px-3 py-1" href="#backup-settings">
                                    <i class="fas fa-circle me-2" style="font-size: 6px;"></i>
                                    <span>Backup Settings</span>
                                </a>
                            </div>
                        </div>
                        <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#security">
                            <i class="fas fa-shield-alt me-3"></i>
                            <span>Security</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Level 1: Logout -->
            <div class="nav-item mt-auto">
                <a class="nav-link text-dark px-3 py-2 d-flex align-items-center" href="#logout">
                    <i class="fas fa-sign-out-alt me-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
</div>
