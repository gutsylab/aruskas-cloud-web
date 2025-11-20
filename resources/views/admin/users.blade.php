@extends('layouts.admin')

@section('title', 'User Management - Laravel Admin')
@section('page-title', 'User Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Users</h5>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New User
                </button>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="search-input" placeholder="Search users...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="role-filter" data-select2 data-select2-options='{"placeholder": "All Roles"}'>
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="status-filter" data-select2 data-select2-options='{"placeholder": "All Status"}'>
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="date-filter" placeholder="Filter by date" data-daterange data-single-date>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="refreshUsers()">
                            <i class="fas fa-sync me-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="users-table" data-datatable data-datatable-options='{"order": [[0, "desc"]], "columnDefs": [{"orderable": false, "targets": [0, 6]}]}'>
                        <thead class="table-dark">
                            <tr>
                                <th><input type="checkbox" class="form-check-input" id="select-all"></th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" value="1"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40x40" alt="User" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">John Doe</h6>
                                            <small class="text-muted">ID: #001</small>
                                        </div>
                                    </div>
                                </td>
                                <td>john.doe@example.com</td>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>2024-01-15</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-primary" onclick="editUser(1)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="viewUser(1)" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="resetPassword(1)" title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(1, 'John Doe')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40x40" alt="User" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">Jane Smith</h6>
                                            <small class="text-muted">ID: #002</small>
                                        </div>
                                    </div>
                                </td>
                                <td>jane.smith@example.com</td>
                                <td><span class="badge bg-primary">Editor</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>2024-01-14</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>Reset Password</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40x40" alt="User" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">Mike Johnson</h6>
                                            <small class="text-muted">ID: #003</small>
                                        </div>
                                    </div>
                                </td>
                                <td>mike.johnson@example.com</td>
                                <td><span class="badge bg-secondary">User</span></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2024-01-13</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-check me-2"></i>Approve</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40x40" alt="User" class="rounded-circle me-3" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">Sarah Wilson</h6>
                                            <small class="text-muted">ID: #004</small>
                                        </div>
                                    </div>
                                </td>
                                <td>sarah.wilson@example.com</td>
                                <td><span class="badge bg-secondary">User</span></td>
                                <td><span class="badge bg-danger">Inactive</span></td>
                                <td>2024-01-12</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-check me-2"></i>Activate</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">Showing 1 to 4 of 25 entries</small>
                    </div>
                    <nav aria-label="User pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (Hidden by default) -->
<div class="position-fixed bottom-0 start-50 translate-middle-x bg-primary text-white p-3 rounded-top shadow d-none" id="bulkActions">
    <div class="d-flex align-items-center gap-3">
        <span class="fw-bold">2 users selected</span>
        <button class="btn btn-sm btn-light">
            <i class="fas fa-edit me-1"></i>Edit
        </button>
        <button class="btn btn-sm btn-danger">
            <i class="fas fa-trash me-1"></i>Delete
        </button>
        <button class="btn btn-sm btn-outline-light" onclick="clearSelection()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
    
    .badge {
        font-size: 0.75em;
    }
</style>
@endpush

@push('scripts')
<script>
let usersTable;

$(document).ready(function() {
    // Initialize DataTable
    usersTable = dataTable.init('#users-table', {
        pageLength: 25,
        order: [[5, 'desc']], // Sort by created date
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Disable sorting on checkbox and actions
            { searchable: false, targets: [0, 6] }  // Disable search on checkbox and actions
        ]
    });

    // Custom search functionality
    $('#search-input').on('keyup', function() {
        usersTable.search(this.value).draw();
    });

    // Filter by role
    $('#role-filter').on('change', function() {
        const selectedRole = this.value;
        usersTable.column(3).search(selectedRole).draw();
    });

    // Filter by status
    $('#status-filter').on('change', function() {
        const selectedStatus = this.value;
        usersTable.column(4).search(selectedStatus).draw();
    });

    // Date filter
    $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
        const selectedDate = picker.startDate.format('YYYY-MM-DD');
        usersTable.column(5).search(selectedDate).draw();
    });

    $('#date-filter').on('cancel.daterangepicker', function() {
        usersTable.column(5).search('').draw();
    });

    // Select all functionality
    $('#select-all').on('change', function() {
        const isChecked = this.checked;
        $('.row-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });

    // Individual checkbox change
    $('.row-checkbox').on('change', function() {
        updateBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
});

// Update bulk actions visibility
function updateBulkActions() {
    const selectedCount = $('.row-checkbox:checked').length;
    const bulkActions = $('#bulkActions');
    
    if (selectedCount > 0) {
        bulkActions.removeClass('d-none');
        bulkActions.find('span').text(`${selectedCount} user${selectedCount > 1 ? 's' : ''} selected`);
    } else {
        bulkActions.addClass('d-none');
    }
}

// Clear all selections
function clearSelection() {
    $('.row-checkbox, #select-all').prop('checked', false);
    $('#select-all').prop('indeterminate', false);
    updateBulkActions();
}

// Refresh users table
function refreshUsers() {
    if (usersTable) {
        usersTable.ajax.reload();
        sweetAlert.success('Refreshed!', 'User data has been reloaded.');
    }
}

// View user details
function viewUser(userId) {
    sweetAlert.info('User Details', `Viewing details for user ID: ${userId}`, 'View Details');
}

// Edit user
function editUser(userId) {
    sweetAlert.info('Edit User', `Opening edit form for user ID: ${userId}`, 'Edit User');
    // In real app, you would open a modal or redirect to edit page
}

// Reset user password
function resetPassword(userId) {
    sweetAlert.confirm(
        'Reset Password?', 
        'This will send a password reset email to the user.',
        'Yes, Reset Password',
        'Cancel'
    ).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            sweetAlert.loading('Sending reset email...');
            
            // Simulate API call
            setTimeout(() => {
                sweetAlert.close();
                sweetAlert.success('Password Reset Sent!', 'The user will receive an email with reset instructions.');
            }, 2000);
        }
    });
}

// Delete user
function deleteUser(userId, userName) {
    sweetAlert.confirmDelete(
        'Delete User?', 
        `Are you sure you want to delete "${userName}"? This action cannot be undone.`
    ).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            sweetAlert.loading('Deleting user...');
            
            // Simulate API call
            setTimeout(() => {
                sweetAlert.close();
                sweetAlert.success('Deleted!', `${userName} has been deleted successfully.`);
                
                // In real app, you would refresh the table
                // usersTable.ajax.reload();
            }, 1500);
        }
    });
}

// Bulk delete selected users
function bulkDelete() {
    const selectedUsers = $('.row-checkbox:checked');
    const count = selectedUsers.length;
    
    if (count === 0) {
        sweetAlert.warning('No Selection', 'Please select users to delete.');
        return;
    }
    
    sweetAlert.confirmDelete(
        'Delete Multiple Users?',
        `Are you sure you want to delete ${count} selected user${count > 1 ? 's' : ''}? This action cannot be undone.`
    ).then((result) => {
        if (result.isConfirmed) {
            sweetAlert.loading('Deleting users...');
            
            // Simulate API call
            setTimeout(() => {
                sweetAlert.close();
                sweetAlert.success('Deleted!', `${count} user${count > 1 ? 's have' : ' has'} been deleted successfully.`);
                clearSelection();
                // In real app: usersTable.ajax.reload();
            }, 2000);
        }
    });
}

// Bulk export selected users
function bulkExport() {
    const selectedUsers = $('.row-checkbox:checked');
    const count = selectedUsers.length;
    
    if (count === 0) {
        sweetAlert.warning('No Selection', 'Please select users to export.');
        return;
    }
    
    sweetAlert.loading('Preparing export...');
    
    // Simulate export process
    setTimeout(() => {
        sweetAlert.close();
        sweetAlert.success('Export Ready!', `${count} user${count > 1 ? 's' : ''} exported successfully.`);
        
        // In real app, trigger download
        // window.location.href = '/admin/users/export?ids=' + getSelectedIds();
    }, 2000);
}
</script>
@endpush
