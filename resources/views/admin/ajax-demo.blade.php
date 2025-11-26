@extends('layouts.admin')

@section('title', 'AJAX Demo - Laravel Admin')
@section('page-title', 'AJAX Functions Demo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">AJAX Demo</li>
@endsection

@section('content')
<div class="row">
    <!-- AJAX GET Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-download text-primary me-2"></i>
                    AJAX GET Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX GET request to fetch data from server.</p>
                <button class="btn btn-primary" onclick="testAjaxGet()">
                    <i class="fas fa-play me-2"></i>Test GET Request
                </button>
                <div id="get-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- AJAX POST Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-paper-plane text-success me-2"></i>
                    AJAX POST Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX POST request to create new data.</p>
                <div class="mb-3">
                    <input type="text" class="form-control" id="post-name" placeholder="Enter name">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="post-email" placeholder="Enter email">
                </div>
                <button class="btn btn-success" onclick="testAjaxPost()">
                    <i class="fas fa-plus me-2"></i>Test POST Request
                </button>
                <div id="post-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- AJAX PUT Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit text-warning me-2"></i>
                    AJAX PUT Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX PUT request to update existing data.</p>
                <div class="mb-3">
                    <input type="number" class="form-control" id="put-id" placeholder="Enter ID to update" value="1">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="put-name" placeholder="Enter updated name">
                </div>
                <button class="btn btn-warning" onclick="testAjaxPut()">
                    <i class="fas fa-save me-2"></i>Test PUT Request
                </button>
                <div id="put-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- AJAX PATCH Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-wrench text-info me-2"></i>
                    AJAX PATCH Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX PATCH request to partially update data.</p>
                <div class="mb-3">
                    <input type="number" class="form-control" id="patch-id" placeholder="Enter ID to patch" value="1">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="patch-status" placeholder="Enter new status" value="active">
                </div>
                <button class="btn btn-info" onclick="testAjaxPatch()">
                    <i class="fas fa-magic me-2"></i>Test PATCH Request
                </button>
                <div id="patch-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- AJAX DELETE Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash text-danger me-2"></i>
                    AJAX DELETE Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX DELETE request to remove data.</p>
                <div class="mb-3">
                    <input type="number" class="form-control" id="delete-id" placeholder="Enter ID to delete" value="1">
                </div>
                <button class="btn btn-danger" onclick="testAjaxDelete()">
                    <i class="fas fa-trash me-2"></i>Test DELETE Request
                </button>
                <div id="delete-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- AJAX Form Upload Demo -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload text-purple me-2"></i>
                    AJAX Form Upload Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Test AJAX form submission with file upload.</p>
                <form id="upload-form">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="title" placeholder="Enter title">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" name="file" accept="image/*">
                    </div>
                    <button type="button" class="btn btn-purple" onclick="testAjaxForm()">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Test Form Upload
                    </button>
                </form>
                <div id="form-result" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Response Display Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AJAX Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="response-content" class="bg-light p-3 rounded"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Styles are now included in admin.css via ajax-demo.css import --}}

{{-- AJAX functionality is now provided by admin/ajax-helpers.js and admin/ajax-demo.js --}}
