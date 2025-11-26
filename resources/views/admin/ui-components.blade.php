@extends('layouts.admin')

@section('title', 'UI Components Demo - Laravel Admin')
@section('page-title', 'UI Components Demo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">UI Components</li>
@endsection

@section('content')
<!-- SweetAlert2 Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell text-warning me-2"></i>
                    SweetAlert2 Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Beautiful, responsive, highly customizable alert dialogs.</p>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-success w-100" onclick="testSweetSuccess()">
                            <i class="fas fa-check me-2"></i>Success Alert
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-danger w-100" onclick="testSweetError()">
                            <i class="fas fa-times me-2"></i>Error Alert
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-warning w-100" onclick="testSweetWarning()">
                            <i class="fas fa-exclamation me-2"></i>Warning Alert
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-info w-100" onclick="testSweetInfo()">
                            <i class="fas fa-info me-2"></i>Info Alert
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-primary w-100" onclick="testSweetConfirm()">
                            <i class="fas fa-question me-2"></i>Confirmation
                        </button>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-danger w-100" onclick="testSweetDelete()">
                            <i class="fas fa-trash me-2"></i>Delete Confirmation
                        </button>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-secondary w-100" onclick="testSweetLoading()">
                            <i class="fas fa-spinner me-2"></i>Loading Dialog
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Button Themes Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-palette text-primary me-2"></i>
                    Button Themes Demo
                </h5>
            </div>
            <div class="card-body">
                <!-- Default Buttons -->
                <div class="mb-4">
                    <h6 class="mb-3">Default</h6>
                    <p class="text-muted small mb-3">Apply <code>.btn-*</code> class followed by <code>.btn</code> class to create default buttons.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary">Primary</button>
                        <button class="btn btn-info">Info</button>
                        <button class="btn btn-success">Success</button>
                        <button class="btn btn-warning">Warning</button>
                        <button class="btn btn-danger">Danger</button>
                        <button class="btn btn-secondary">Secondary</button>
                        <button class="btn btn-dark">Dark</button>
                    </div>
                </div>
                
                <!-- Light Buttons -->
                <div class="mb-4">
                    <h6 class="mb-3">Light</h6>
                    <p class="text-muted small mb-3">Apply <code>.btn-light</code> class followed by <code>.btn</code> class to create light buttons.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-light">Light</button>
                        <button class="btn btn-light text-primary">Primary Light</button>
                        <button class="btn btn-light text-info">Info Light</button>
                        <button class="btn btn-light text-success">Success Light</button>
                        <button class="btn btn-light text-warning">Warning Light</button>
                        <button class="btn btn-light text-danger">Danger Light</button>
                        <button class="btn btn-light text-secondary">Secondary Light</button>
                    </div>
                </div>
                
                <!-- Outline Buttons -->
                <div class="mb-3">
                    <h6 class="mb-3">Outline</h6>
                    <p class="text-muted small mb-3">Apply <code>.btn-outline-*</code> class followed by <code>.btn</code> class to create outline buttons.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary">Primary</button>
                        <button class="btn btn-outline-info">Info</button>
                        <button class="btn btn-outline-success">Success</button>
                        <button class="btn btn-outline-warning">Warning</button>
                        <button class="btn btn-outline-danger">Danger</button>
                        <button class="btn btn-outline-secondary">Secondary</button>
                        <button class="btn btn-outline-dark">Dark</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Select2 Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Enhanced select boxes with search, tagging, and more.</p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Single Select</label>
                        <select class="form-select" data-select2 id="single-select">
                            <option value="">Choose an option</option>
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                            <option value="option4">Option 4</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Multiple Select</label>
                        <select class="form-select" data-select2 multiple id="multiple-select">
                            <option value="tag1">Tag 1</option>
                            <option value="tag2">Tag 2</option>
                            <option value="tag3">Tag 3</option>
                            <option value="tag4">Tag 4</option>
                            <option value="tag5">Tag 5</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">AJAX Select (Users)</label>
                        <select class="form-select" id="ajax-select" data-select2 data-ajax-url="/api/demo/users-select">
                            <option value="">Search users...</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary me-2" onclick="getSelectValues()">
                            <i class="fas fa-eye me-2"></i>Get Values
                        </button>
                        <button class="btn btn-secondary me-2" onclick="clearSelects()">
                            <i class="fas fa-eraser me-2"></i>Clear All
                        </button>
                        <button class="btn btn-info" onclick="setSelectValues()">
                            <i class="fas fa-edit me-2"></i>Set Values
                        </button>
                    </div>
                </div>
                <div id="select-values" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- DateRangePicker Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar text-info me-2"></i>
                    DateRangePicker Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Date range picker component with predefined ranges.</p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date Range Picker</label>
                        <input type="text" class="form-control" data-daterange id="daterange-input" placeholder="Select date range">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Single Date Picker</label>
                        <input type="text" class="form-control" data-daterange data-single-date id="single-date-input" placeholder="Select date">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Custom Format</label>
                        <input type="text" class="form-control" id="custom-date-input" placeholder="DD/MM/YYYY format">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary me-2" onclick="getDateValues()">
                            <i class="fas fa-eye me-2"></i>Get Dates
                        </button>
                        <button class="btn btn-secondary me-2" onclick="clearDates()">
                            <i class="fas fa-eraser me-2"></i>Clear Dates
                        </button>
                        <button class="btn btn-info" onclick="setDateValues()">
                            <i class="fas fa-edit me-2"></i>Set Dates
                        </button>
                    </div>
                </div>
                <div id="date-values" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table text-success me-2"></i>
                    DataTables Demo
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Advanced table with sorting, filtering, and pagination.</p>
                
                <!-- Filter Controls -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="status-filter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="role-filter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="date-filter" placeholder="Filter by date" data-daterange data-single-date>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="refreshTable()">
                            <i class="fas fa-sync me-2"></i>Refresh Table
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="demo-table">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>john.doe@example.com</td>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>2024-01-15</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>jane.smith@example.com</td>
                                <td><span class="badge bg-primary">Editor</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>2024-01-14</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(2)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Mike Johnson</td>
                                <td>mike.johnson@example.com</td>
                                <td><span class="badge bg-secondary">User</span></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2024-01-13</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(3)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(3)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Sarah Wilson</td>
                                <td>sarah.wilson@example.com</td>
                                <td><span class="badge bg-secondary">User</span></td>
                                <td><span class="badge bg-danger">Inactive</span></td>
                                <td>2024-01-12</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(4)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(4)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>David Brown</td>
                                <td>david.brown@example.com</td>
                                <td><span class="badge bg-primary">Editor</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>2024-01-11</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(5)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(5)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Combined Demo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-puzzle-piece text-purple me-2"></i>
                    Combined Demo Form
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Form combining all UI components with validation.</p>
                <form id="demo-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User *</label>
                            <select class="form-select" name="user_id" data-select2 required>
                                <option value="">Select user</option>
                                <option value="1">John Doe</option>
                                <option value="2">Jane Smith</option>
                                <option value="3">Mike Johnson</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tags</label>
                            <select class="form-select" name="tags[]" data-select2 multiple>
                                <option value="tag1">Important</option>
                                <option value="tag2">Urgent</option>
                                <option value="tag3">Review</option>
                                <option value="tag4">Follow-up</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Range *</label>
                            <input type="text" class="form-control" name="date_range" data-daterange required placeholder="Select date range">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="text" class="form-control" name="due_date" data-daterange data-single-date placeholder="Select due date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Enter description"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="submitDemoForm()">
                            <i class="fas fa-save me-2"></i>Submit Form
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetDemoForm()">
                            <i class="fas fa-undo me-2"></i>Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Styles are now included in admin.css via ui-components.css import --}}

{{-- UI Components functionality is now provided by admin/ui-components.js and related modules --}}
