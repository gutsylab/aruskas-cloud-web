<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Sidebar Toggle Button (positioned aligned with sidebar) -->
        <button class="btn btn-outline-secondary sidebar-toggle-btn" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Laravel Admin Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <h5 class="mb-0 fw-semibold text-dark">Laravel Admin</h5>
        </a>

        <!-- Desktop Right Side Menu -->
        <div class="d-none d-lg-flex ms-auto align-items-center">
            <!-- Right side menu -->
            <div class="navbar-nav d-flex flex-row">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">New user registered</a></li>
                        <li><a class="dropdown-item" href="#">Order #123 completed</a></li>
                        <li><a class="dropdown-item" href="#">System update available</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        {{-- <img src="https://via.placeholder.com/32x32" alt="User" class="rounded-circle me-2" width="32" height="32"> --}}
                        <span class="d-none d-md-inline">Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mobile Right Side Menu -->
        <div class="d-flex d-lg-none ms-auto align-items-center">
            <!-- Notifications (Mobile) -->
            <div class="nav-item dropdown me-2">
                <a class="nav-link" href="#" id="notificationDropdownMobile" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">3</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdownMobile">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#">New user registered</a></li>
                    <li><a class="dropdown-item" href="#">Order #123 completed</a></li>
                    <li><a class="dropdown-item" href="#">System update available</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                </ul>
            </div>

            <!-- User Menu (Mobile) -->
            <div class="nav-item dropdown">
                <a class="nav-link" href="#" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
                    <img src="https://via.placeholder.com/32x32" alt="User" class="rounded-circle" width="32" height="32">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMobile">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
