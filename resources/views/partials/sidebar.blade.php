<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="index" class="fs-18 fw-semibold">
            <img width="75%" class="pe-app-sidebar-logo-default d-none" alt="Logo"
                src="{{ asset('assets/images/logo-dark.png') }}">
            <img width="75%" class="pe-app-sidebar-logo-light d-none" alt="Logo"
                src="{{ asset('assets/images/logo-light.png') }}">
            <img width="100%" class="pe-app-sidebar-logo-minimize d-none" alt="Logo"
                src="{{ asset('assets/images/logo-md.png') }}">
            <img width="100%" class="pe-app-sidebar-logo-minimize-light d-none" alt="Logo"
                src="{{ asset('assets/images/logo-md-light.png') }}">
            <!-- FabKin -->
        </a>
        <!--end::Brand Image-->
    </div>
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <ul class="pe-main-menu list-unstyled">
            @if (hasPermissionGroup(\App\Constants\TenantPermissions::GROUP_DASHBOARD))
                <li class="pe-menu-title">
                    Main
                </li>
                <li class="pe-slide pe-has-sub">
                    <a href="{{ route('dashboard', ['tenant_id' => $tenant->tenant_id]) }}"
                        class="pe-nav-link  @if (\App\Constants\TenantPermissions::MODULE_DASHBOARD == $moduleName) active @endif">
                        <i class="ri-dashboard-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Dashboard</span>
                    </a>
                </li>
            @endif

            @if (hasPermissionGroup(\App\Constants\TenantPermissions::GROUP_CASH))
                <li class="pe-menu-title">
                    Kas
                </li>
                <li class="pe-slide pe-has-sub">
                    <a href="{{ route('cash-flows.index', ['tenant_id' => $tenant->tenant_id]) }}"
                        class="pe-nav-link  @if (\App\Constants\TenantPermissions::MODULE_CASH_FLOW == $moduleName) active @endif">
                        <i class="ri-refresh-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Arus Kas</span>
                    </a>
                </li>
                <li class="pe-slide pe-has-sub">
                    <a href="{{ route('cash-categories.index', ['tenant_id' => $tenant->tenant_id]) }}"
                        class="pe-nav-link  @if (\App\Constants\TenantPermissions::MODULE_CASH_CATEGORY == $moduleName) active @endif">
                        <i class="ri-apps-2-fill pe-nav-icon"></i>
                        <span class="pe-nav-content">Kategori</span>
                    </a>
                </li>
                <li class="pe-slide pe-has-sub">
                    <a href="{{ route('cash-accounts.index', ['tenant_id' => $tenant->tenant_id]) }}"
                        class="pe-nav-link  @if (\App\Constants\TenantPermissions::MODULE_CASH_ACCOUNT == $moduleName) active @endif">
                        <i class="ri-wallet-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Akun</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</aside>
