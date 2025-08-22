/**
 * File Manager JavaScript
 * Handles file management functionality including upload, delete, folder creation, etc.
 */

class FileManager {
    constructor() {        
        this.currentPath = '';
        this.init();
    }

    init() {
        this.loadFiles();
        this.bindEvents();
    }

    bindEvents() {
        
        // Upload file button
        const uploadBtn = document.getElementById('uploadFileBtn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', () => {
                this.showModal('uploadModal');
            });
        } else {
        }

        // Create folder button
        const createFolderBtn = document.getElementById('createFolderBtn');
        if (createFolderBtn) {
            createFolderBtn.addEventListener('click', () => {
                this.showModal('createFolderModal');
            });
        } else {
        }

        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.loadFiles();
            });
        } else {
        }

        // Upload form submit
        const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
        if (uploadSubmitBtn) {
            uploadSubmitBtn.addEventListener('click', () => {
                this.uploadFile();
            });
        } else {
        }

        // Create folder form submit
        const createFolderSubmitBtn = document.getElementById('createFolderSubmitBtn');
        if (createFolderSubmitBtn) {
            createFolderSubmitBtn.addEventListener('click', () => {
                this.createFolder();
            });
        } else {
        }

        // Path navigation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('path-link')) {
                e.preventDefault();
                this.navigateToPath(e.target.dataset.path);
            }
        });

        // Modal close buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-close') || e.target.closest('.modal-close')) {
                const modal = e.target.closest('.modal');
                if (modal) {
                    this.hideModal(modal.id);
                }
            }
        });

        // Close modal when clicking backdrop
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.hideModal(e.target.id);
            }
        });
    }

    async loadFiles() {
        this.showLoading();
        
        try {
            const url = `/admin/file-manager/api/files?path=${encodeURIComponent(this.currentPath)}`;
            
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });        
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.renderFiles(result.data);
                this.updateBreadcrumb();
            } else {
                this.showError(result.message || 'Failed to load files');
            }
        } catch (error) {
            console.error('FileManager: Error loading files:', error);
            this.showError('Network error: ' + error.message);
        }
    }

    renderFiles(data) {
        const tbody = document.getElementById('fileTableBody');
        tbody.innerHTML = '';
        
        const allItems = [...data.directories, ...data.files];
        
        if (allItems.length === 0) {
            this.showEmptyState();
            return;
        }
        
        this.hideLoading();
        this.hideEmptyState();
        
        allItems.forEach(item => {
            const row = this.createFileRow(item);
            tbody.appendChild(row);
        });
    }

    createFileRow(item) {
        const row = document.createElement('tr');
        row.className = 'file-row';
        
        const icon = this.getFileIcon(item);
        const nameClass = item.type === 'directory' ? 'folder-name' : 'file-name';
        const nameClick = item.type === 'directory' ? `onclick="fileManager.navigateToPath('${item.path}')"` : '';
        
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="file-icon me-2">${icon}</div>
                    <span class="${nameClass}" ${nameClick}>${item.name}</span>
                </div>
            </td>
            <td>
                <span class="badge bg-${item.type === 'directory' ? 'primary' : 'secondary'}">
                    ${item.type === 'directory' ? 'Folder' : (item.extension || 'File')}
                </span>
            </td>
            <td>${item.size}</td>
            <td><small class="text-muted">${item.modified}</small></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${this.createActionButtons(item)}
                </div>
            </td>
        `;
        
        return row;
    }

    createActionButtons(item) {
        let buttons = '';
        
        if (item.type === 'file') {
            buttons += `
                <button class="btn btn-outline-primary action-btn" onclick="fileManager.downloadFile('${item.path}')" title="Download">
                    <i class="fas fa-download"></i>
                </button>
                <button class="btn btn-outline-info action-btn" onclick="window.open('${item.url}', '_blank')" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-outline-success action-btn" onclick="fileManager.copyFileLink('${item.url}')" title="Copy Link">
                    <i class="fas fa-link"></i>
                </button>
            `;
        }
        
        buttons += `
            <button class="btn btn-outline-danger action-btn" onclick="fileManager.deleteItem('${item.path}', '${item.type}')" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        return buttons;
    }

    getFileIcon(item) {
        if (item.type === 'directory') {
            return '<i class="fas fa-folder text-warning"></i>';
        }
        
        const ext = item.extension ? item.extension.toLowerCase() : '';
        const iconMap = {
            'pdf': '<i class="fas fa-file-pdf text-danger"></i>',
            'doc': '<i class="fas fa-file-word text-primary"></i>',
            'docx': '<i class="fas fa-file-word text-primary"></i>',
            'xls': '<i class="fas fa-file-excel text-success"></i>',
            'xlsx': '<i class="fas fa-file-excel text-success"></i>',
            'ppt': '<i class="fas fa-file-powerpoint text-warning"></i>',
            'pptx': '<i class="fas fa-file-powerpoint text-warning"></i>',
            'jpg': '<i class="fas fa-file-image text-info"></i>',
            'jpeg': '<i class="fas fa-file-image text-info"></i>',
            'png': '<i class="fas fa-file-image text-info"></i>',
            'gif': '<i class="fas fa-file-image text-info"></i>',
            'mp4': '<i class="fas fa-file-video text-dark"></i>',
            'avi': '<i class="fas fa-file-video text-dark"></i>',
            'mp3': '<i class="fas fa-file-audio text-purple"></i>',
            'wav': '<i class="fas fa-file-audio text-purple"></i>',
            'zip': '<i class="fas fa-file-archive text-secondary"></i>',
            'rar': '<i class="fas fa-file-archive text-secondary"></i>',
            'txt': '<i class="fas fa-file-alt text-muted"></i>',
            'php': '<i class="fas fa-file-code text-primary"></i>',
            'js': '<i class="fas fa-file-code text-warning"></i>',
            'css': '<i class="fas fa-file-code text-info"></i>',
            'html': '<i class="fas fa-file-code text-danger"></i>'
        };
        
        return iconMap[ext] || '<i class="fas fa-file text-muted"></i>';
    }

    navigateToPath(path) {
        this.currentPath = path;
        this.loadFiles();
    }

    updateBreadcrumb() {
        const breadcrumb = document.getElementById('pathBreadcrumb');
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item">
                <a href="#" data-path="" class="path-link">
                    <i class="fas fa-home"></i> Storage
                </a>
            </li>
        `;
        
        if (this.currentPath) {
            const pathParts = this.currentPath.split('/');
            let currentPath = '';
            
            pathParts.forEach((part, index) => {
                currentPath += (index > 0 ? '/' : '') + part;
                const isLast = index === pathParts.length - 1;
                
                const li = document.createElement('li');
                li.className = 'breadcrumb-item' + (isLast ? ' active' : '');
                
                if (isLast) {
                    li.textContent = part;
                } else {
                    li.innerHTML = `<a href="#" data-path="${currentPath}" class="path-link">${part}</a>`;
                }
                
                breadcrumb.appendChild(li);
            });
        }
    }

    async uploadFile() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'No file selected',
                text: 'Please select a file to upload'
            });
            return;
        }
    
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('path', this.currentPath);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
        
        const submitBtn = document.getElementById('uploadSubmitBtn');
        const progress = document.getElementById('uploadProgress');
        
        try {
            submitBtn.disabled = true;
            progress.style.display = 'block';
            
            const response = await fetch('/admin/file-manager/api/upload', {
                method: 'POST',
                body: formData
            });
            
            
            const result = await response.json();            
            
            if (result.success) {
                this.hideModal('uploadModal');
                this.showSuccess(result.message);
                this.loadFiles();
                fileInput.value = '';
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('FileManager: Upload error:', error);
            this.showError('Upload failed: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            progress.style.display = 'none';
        }
    }

    async createFolder() {
        const folderNameInput = document.getElementById('folderNameInput');
        const folderName = folderNameInput.value.trim();
        
        if (!folderName) {
            Swal.fire({
                icon: 'warning',
                title: 'No folder name',
                text: 'Please enter a folder name'
            });
            return;
        }
        
        const submitBtn = document.getElementById('createFolderSubmitBtn');
        
        try {
            submitBtn.disabled = true;
            
            const response = await fetch('/admin/file-manager/api/create-folder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: folderName,
                    path: this.currentPath
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.hideModal('createFolderModal');
                this.showSuccess(result.message);
                this.loadFiles();
                folderNameInput.value = '';
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('Failed to create folder: ' + error.message);
        } finally {
            submitBtn.disabled = false;
        }
    }

    async deleteItem(path, type) {
        try {
            const result = await Swal.fire({
                title: `Delete ${type}?`,
                text: `Are you sure you want to delete this ${type}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });

            if (!result.isConfirmed) {
                return;
            }

            const response = await fetch('/admin/file-manager/api/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    path: path,
                    type: type
                })
            });
            
            const responseData = await response.json();
            
            if (responseData.success) {
                this.showSuccess(responseData.message);
                this.loadFiles();
            } else {
                this.showError(responseData.message);
            }
        } catch (error) {
            this.showError('Delete failed: ' + error.message);
        }
    }

    downloadFile(path) {
        const downloadUrl = `/admin/file-manager/api/download?path=${encodeURIComponent(path)}`;
        window.open(downloadUrl, '_blank');
    }

    async copyFileLink(fileUrl) {
        try {
            // Use the Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(fileUrl);
                this.showSuccess('File link copied to clipboard!');
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = fileUrl;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    this.showSuccess('File link copied to clipboard!');
                } catch (err) {
                    this.showError('Failed to copy link. Please copy manually: ' + fileUrl);
                } finally {
                    document.body.removeChild(textArea);
                }
            }
        } catch (error) {
            // Show the link in a modal as a last resort
            Swal.fire({
                title: 'Copy File Link',
                html: `
                    <p>Copy the link below:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" value="${fileUrl}" id="linkToCopy" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="
                            document.getElementById('linkToCopy').select();
                            document.execCommand('copy');
                            Swal.fire({icon: 'success', title: 'Copied!', timer: 1500, showConfirmButton: false});
                        ">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false
            });
        }
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Create backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = modalId + '-backdrop';
            document.body.appendChild(backdrop);
        }
    }

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Remove backdrop
            const backdrop = document.getElementById(modalId + '-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }

    showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('fileList').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('fileList').style.display = 'block';
    }

    showEmptyState() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('fileList').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
    }

    hideEmptyState() {
        document.getElementById('emptyState').style.display = 'none';
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message
        });
    }
}

// Expose as AdminFileManager for consistency with other modules
window.AdminFileManager = {
    instance: null,
    
    init() {        
        const container = document.querySelector('#fileManagerContainer');
        
        if (container) {
            this.instance = new FileManager();
        } else {
        }
    }
};

// Also create global fileManager for backward compatibility
window.fileManager = null;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.AdminFileManager.init();
        if (window.AdminFileManager.instance) {
            window.fileManager = window.AdminFileManager.instance;
        }
    });
} else {
    window.AdminFileManager.init();
    if (window.AdminFileManager.instance) {
        window.fileManager = window.AdminFileManager.instance;
    }
}
