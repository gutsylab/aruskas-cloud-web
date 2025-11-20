@extends('layouts.admin')

@section('title', 'File Manager - Laravel Admin')
@section('page-title', 'File Manager')

@section('breadcrumb')
    <li class="breadcrumb-item active">File Manager</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card" id="fileManagerContainer">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">File Manager</h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-primary btn-sm" id="uploadFileBtn">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="createFolderBtn">
                        <i class="fas fa-folder-plus"></i> New Folder
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Breadcrumb Navigation -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb" id="pathBreadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#" data-path="" class="path-link">
                                <i class="fas fa-home"></i> Storage
                            </a>
                        </li>
                    </ol>
                </nav>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading files...</p>
                </div>

                <!-- File List -->
                <div id="fileList" class="table-responsive" style="display: none;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="40%">Name</th>
                                <th width="15%">Type</th>
                                <th width="15%">Size</th>
                                <th width="20%">Modified</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="fileTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No files found</h5>
                    <p class="text-muted">This folder is empty. Upload some files or create a new folder.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="btn-close modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Choose File</label>
                        <input type="file" class="form-control" id="fileInput" name="file" required>
                        <div class="form-text">Maximum file size: 10MB</div>
                    </div>
                    <div class="progress" id="uploadProgress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadSubmitBtn">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                <button type="button" class="btn-close modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="createFolderForm">
                    @csrf
                    <div class="mb-3">
                        <label for="folderNameInput" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderNameInput" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="button" class="btn btn-success" id="createFolderSubmitBtn">Create</button>
            </div>
        </div>
    </div>
</div>

@endsection
