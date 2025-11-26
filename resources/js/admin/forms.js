/**
 * Admin Forms JavaScript
 * Handles form validation, AJAX submissions, and dynamic form elements
 */

window.AdminForms = {
    init: function() {
        this.initValidation();
        this.initAjaxForms();
        this.initSelect2();
        this.initDatePickers();
        this.initFileUploads();
        this.bindFormEvents();
    },

    initValidation: function() {
        // Bootstrap validation
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Custom validation rules
        this.addCustomValidators();
    },

    addCustomValidators: function() {
        // Password confirmation validator
        const passwordConfirm = document.querySelectorAll('input[name="password_confirmation"]');
        passwordConfirm.forEach(input => {
            input.addEventListener('input', function() {
                const password = document.querySelector('input[name="password"]');
                if (password && password.value !== this.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
        });

        // Email format validator
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.setCustomValidity('Please enter a valid email address');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    },

    initAjaxForms: function() {
        const ajaxForms = document.querySelectorAll('.ajax-form');
        ajaxForms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleAjaxSubmit(e));
        });
    },

    handleAjaxSubmit: function(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.innerHTML;
        
        // Disable submit button and show loading
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        }

        // Clear previous errors
        this.clearFormErrors(form);

        const formData = new FormData(form);
        const url = form.action;
        const method = form.method || 'POST';

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Utils.showToast(data.message || 'Operation completed successfully', 'success');
                
                // Handle redirect
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
                
                // Reset form if specified
                if (data.reset_form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    this.showFormErrors(form, data.errors);
                } else {
                    Utils.showToast(data.message || 'An error occurred', 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            Utils.showToast('An error occurred while processing your request', 'danger');
        })
        .finally(() => {
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    },

    showFormErrors: function(form, errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                Utils.showFieldError(field, errors[fieldName][0]);
            }
        });
    },

    clearFormErrors: function(form) {
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            Utils.clearFieldError(field);
        });
    },

    initSelect2: function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').each(function() {
                const $select = $(this);
                const config = {
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $select.data('placeholder') || 'Select an option...',
                    allowClear: $select.data('allow-clear') !== false
                };

                // AJAX configuration
                if ($select.data('ajax-url')) {
                    config.ajax = {
                        url: $select.data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        }
                    };
                }

                $select.select2(config);
            });
        }
    },

    initDatePickers: function() {
        // Initialize date pickers
        const dateInputs = document.querySelectorAll('.datepicker');
        dateInputs.forEach(input => {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(input, {
                    dateFormat: input.dataset.format || 'Y-m-d',
                    allowInput: true,
                    clickOpens: true
                });
            }
        });

        // Initialize date range pickers
        const dateRangeInputs = document.querySelectorAll('.daterangepicker');
        dateRangeInputs.forEach(input => {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(input, {
                    mode: 'range',
                    dateFormat: input.dataset.format || 'Y-m-d',
                    allowInput: true,
                    clickOpens: true
                });
            }
        });
    },

    initFileUploads: function() {
        const fileInputs = document.querySelectorAll('.file-upload');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFileUpload(e));
        });

        // Drag and drop functionality
        const dropZones = document.querySelectorAll('.drop-zone');
        dropZones.forEach(zone => {
            this.initDropZone(zone);
        });
    },

    handleFileUpload: function(event) {
        const input = event.target;
        const files = input.files;
        const preview = input.parentNode.querySelector('.file-preview');
        
        if (preview && files.length > 0) {
            preview.innerHTML = '';
            
            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item d-flex align-items-center mb-2';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.cssText = 'width: 50px; height: 50px; object-fit: cover; border-radius: 4px;';
                    fileItem.appendChild(img);
                }
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'ms-2';
                fileInfo.innerHTML = `
                    <div class="fw-medium">${file.name}</div>
                    <small class="text-muted">${this.formatFileSize(file.size)}</small>
                `;
                fileItem.appendChild(fileInfo);
                
                preview.appendChild(fileItem);
            });
        }
    },

    initDropZone: function(dropZone) {
        const input = dropZone.querySelector('input[type="file"]');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-over');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (input && files.length > 0) {
                input.files = files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    },

    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    bindFormEvents: function() {
        // Auto-save functionality
        const autoSaveForms = document.querySelectorAll('.auto-save');
        autoSaveForms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', Utils.debounce(() => {
                    this.autoSave(form);
                }, 1000));
            });
        });

        // Dynamic form fields
        $(document).on('click', '.add-field', function() {
            AdminForms.addDynamicField(this);
        });

        $(document).on('click', '.remove-field', function() {
            AdminForms.removeDynamicField(this);
        });
    },

    autoSave: function(form) {
        const formData = new FormData(form);
        const url = form.dataset.autoSaveUrl;
        
        if (url) {
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show a subtle indication that data was saved
                    const indicator = form.querySelector('.auto-save-indicator');
                    if (indicator) {
                        indicator.textContent = 'Saved';
                        setTimeout(() => {
                            indicator.textContent = '';
                        }, 2000);
                    }
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
            });
        }
    },

    addDynamicField: function(button) {
        const template = button.parentNode.querySelector('.field-template');
        const container = button.parentNode.querySelector('.dynamic-fields');
        
        if (template && container) {
            const newField = template.cloneNode(true);
            newField.classList.remove('field-template', 'd-none');
            newField.classList.add('dynamic-field');
            
            // Update field names with unique indexes
            const index = container.children.length;
            newField.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.name.replace(/\[\d*\]/, `[${index}]`);
                input.name = name;
                input.value = '';
            });
            
            container.appendChild(newField);
        }
    },

    removeDynamicField: function(button) {
        const field = button.closest('.dynamic-field');
        if (field) {
            field.remove();
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.AdminForms.init();
});

export default window.AdminForms;
