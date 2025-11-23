<div class="sidebar text-dark" id="sidebar">
    <div class="sidebar-menu">
        <nav class="nav flex-column">
            <!-- Core Features -->
            <div class="menu-category-label">Core Features</div>

            <!-- Level 1: Dashboard -->
            <div class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}" data-tooltip="Dashboard">
                    <i class="fas fa-tachometer-alt me-3"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Level 1: Content Management -->
            <div class="nav-item">
                <a class="nav-link @if ($activeMenu === 'cash') active @endif text-dark px-3 py-2 d-flex align-items-center"
                    href="#" data-bs-toggle="collapse" data-bs-target="#contentManagement"
                    aria-expanded="@if ($activeMenu === 'cash') true @else false @endif">
                    <i class="fas fa-file-alt me-3"></i>
                    <span>KAS</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <!-- Level 2: Content Management Sub Menu -->
                <div class="collapse @if ($activeMenu === 'cash') show @endif" id="contentManagement">
                    <div class="nav flex-column ms-3">
                        <a class="nav-link @if ($activeSubMenu === 'cash.flows') active @endif text-dark px-3 py-2 d-flex align-items-center"
                            href="{{ route('cash-flows.index', ['tenant_id' => $tenant->tenant_id]) }}">
                            <i class="fas fa-images me-3"></i>
                            <span>Arus Kas</span>
                        </a>
                        <a class="nav-link @if ($activeSubMenu === 'cash.categories') active @endif text-dark px-3 py-2 d-flex align-items-center"
                            href="{{ route('cash-categories.index', ['tenant_id' => $tenant->tenant_id]) }}">
                            <i class="fas fa-images me-3"></i>
                            <span>Kategori</span>
                        </a>
                        <a class="nav-link @if ($activeSubMenu === 'cash.accounts') active @endif text-dark px-3 py-2 d-flex align-items-center"
                            href="#media">
                            <i class="fas fa-images me-3"></i>
                            <span>Akun</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
