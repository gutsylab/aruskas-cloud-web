@extends('layouts.admin')

@section('title', 'Dashboard - Laravel Admin')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">1,234</h4>
                        <p class="mb-0 opacity-90">Total Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">567</h4>
                        <p class="mb-0 opacity-90">Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-white stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">89</h4>
                        <p class="mb-0 opacity-90">Products</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">$12,345</h4>
                        <p class="mb-0 opacity-90">Revenue</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Area -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sales Overview</h5>
            </div>
            <div class="card-body">
                <div class="chart-placeholder bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chart Area - Integrate with Chart.js or similar library</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#add-user" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </a>
                    <a href="#add-product" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </a>
                    <a href="#view-orders" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>View Orders
                    </a>
                    <a href="#reports" class="btn btn-warning">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user text-primary me-2"></i>
                            New user registration: john.doe@example.com
                        </div>
                        <small class="text-muted">2 minutes ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-shopping-cart text-success me-2"></i>
                            Order #12345 has been completed
                        </div>
                        <small class="text-muted">15 minutes ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-box text-warning me-2"></i>
                            Product "Laptop Pro" stock is running low
                        </div>
                        <small class="text-muted">1 hour ago</small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-cog text-info me-2"></i>
                            System backup completed successfully
                        </div>
                        <small class="text-muted">3 hours ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Products -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Products</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">Laptop Pro</h6>
                            <small class="text-muted">Electronics</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">125 sold</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">Smartphone X</h6>
                            <small class="text-muted">Electronics</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">89 sold</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">Wireless Headphones</h6>
                            <small class="text-muted">Accessories</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">67 sold</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">Gaming Mouse</h6>
                            <small class="text-muted">Accessories</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">45 sold</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
</script>
@endpush
