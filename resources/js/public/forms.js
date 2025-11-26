/**
 * Public Forms JavaScript
 * Handles public form interactions and validation
 */

window.PublicForms = {
    init: function() {
        this.initContactForms();
        this.initNewsletterForms();
        this.initValidation();
        this.bindEvents();
    },

    initContactForms: function() {
        const contactForms = document.querySelectorAll('.contact-form, .form-contact');
        
        contactForms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleContactSubmit(e));
        });
    },

    initNewsletterForms: function() {
        const newsletterForms = document.querySelectorAll('.newsletter-form, .form-newsletter');
        
        newsletterForms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        });
    },

    initValidation: function() {
        // Real-time validation
        const formInputs = document.querySelectorAll('.form-control, .form-select');
        
        formInputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    },

    bindEvents: function() {
        // File upload preview
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFilePreview(e));
        });

        // Dynamic field addition (for forms with repeating fields)
        const addFieldBtns = document.querySelectorAll('.add-field-btn');
        addFieldBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.addFormField(e));
        });

        // Character counter for textareas
        const textareas = document.querySelectorAll('textarea[data-max-length]');
        textareas.forEach(textarea => {
            this.initCharacterCounter(textarea);
        });
    },

    handleContactSubmit: function(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Validate form
        if (!this.validateForm(form)) {
            return;
        }

        // Show loading state
        this.setLoadingState(submitBtn, 'Sending Message...');

        // Prepare form data
        const formData = new FormData(form);
        
        // Submit form
        Utils.ajax(form.action || '/contact', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            this.showSuccessMessage(form, response.message || 'Thank you for your message! We\'ll get back to you soon.');
            form.reset();
        })
        .catch(error => {
            console.error('Contact form error:', error);
            this.showErrorMessage(form, 'Sorry, there was an error sending your message. Please try again.');
        })
        .finally(() => {
            this.resetLoadingState(submitBtn, originalText);
        });
    },

    handleNewsletterSubmit: function(event) {
        event.preventDefault();
        
        const form = event.target;
        const emailInput = form.querySelector('input[type="email"]');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Validate email
        if (!this.validateEmail(emailInput.value)) {
            this.showFieldError(emailInput, 'Please enter a valid email address');
            return;
        }

        // Show loading state
        this.setLoadingState(submitBtn, 'Subscribing...');

        // Prepare form data
        const formData = new FormData(form);
        
        // Submit form
        Utils.ajax(form.action || '/newsletter/subscribe', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            this.showSuccessMessage(form, response.message || 'Thank you for subscribing to our newsletter!');
            form.reset();
        })
        .catch(error => {
            console.error('Newsletter form error:', error);
            this.showErrorMessage(form, 'Sorry, there was an error processing your subscription. Please try again.');
        })
        .finally(() => {
            this.resetLoadingState(submitBtn, originalText);
        });
    },

    validateForm: function(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    },

    validateField: function(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Check if required field is empty
        if (field.hasAttribute('required') && !value) {
            errorMessage = 'This field is required';
            isValid = false;
        }
        // Validate email
        else if (field.type === 'email' && value && !this.validateEmail(value)) {
            errorMessage = 'Please enter a valid email address';
            isValid = false;
        }
        // Validate phone
        else if (field.type === 'tel' && value && !this.validatePhone(value)) {
            errorMessage = 'Please enter a valid phone number';
            isValid = false;
        }
        // Validate minimum length
        else if (field.hasAttribute('minlength') && value.length < parseInt(field.getAttribute('minlength'))) {
            errorMessage = `Minimum ${field.getAttribute('minlength')} characters required`;
            isValid = false;
        }
        // Validate maximum length
        else if (field.hasAttribute('maxlength') && value.length > parseInt(field.getAttribute('maxlength'))) {
            errorMessage = `Maximum ${field.getAttribute('maxlength')} characters allowed`;
            isValid = false;
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }

        return isValid;
    },

    validateEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    validatePhone: function(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    },

    showFieldError: function(field, message) {
        field.classList.add('is-invalid');
        
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    },

    clearFieldError: function(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    },

    showSuccessMessage: function(form, message) {
        const alert = this.createAlert(message, 'success');
        form.parentNode.insertBefore(alert, form);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    },

    showErrorMessage: function(form, message) {
        const alert = this.createAlert(message, 'danger');
        form.parentNode.insertBefore(alert, form);
        
        // Auto-remove after 7 seconds
        setTimeout(() => {
            alert.remove();
        }, 7000);
    },

    createAlert: function(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        return alert;
    },

    setLoadingState: function(button, text) {
        button.disabled = true;
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
    },

    resetLoadingState: function(button, originalText) {
        button.disabled = false;
        button.innerHTML = originalText;
    },

    handleFilePreview: function(event) {
        const input = event.target;
        const files = Array.from(input.files);
        const previewContainer = input.parentNode.querySelector('.file-preview') || this.createFilePreviewContainer(input);
        
        previewContainer.innerHTML = '';

        files.forEach(file => {
            const fileItem = this.createFilePreviewItem(file);
            previewContainer.appendChild(fileItem);
        });
    },

    createFilePreviewContainer: function(input) {
        const container = document.createElement('div');
        container.className = 'file-preview mt-2';
        input.parentNode.appendChild(container);
        return container;
    },

    createFilePreviewItem: function(file) {
        const item = document.createElement('div');
        item.className = 'file-preview-item d-flex align-items-center p-2 border rounded mb-2';
        
        let preview = '';
        if (file.type.startsWith('image/')) {
            const imageUrl = URL.createObjectURL(file);
            preview = `<img src="${imageUrl}" alt="Preview" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" class="me-2">`;
        } else {
            preview = `<i class="fas fa-file me-2"></i>`;
        }
        
        item.innerHTML = `
            ${preview}
            <div class="flex-grow-1">
                <div class="fw-medium">${file.name}</div>
                <small class="text-muted">${this.formatFileSize(file.size)}</small>
            </div>
        `;
        
        return item;
    },

    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    addFormField: function(event) {
        const button = event.target;
        const template = button.parentNode.querySelector('.field-template');
        const container = button.parentNode.querySelector('.dynamic-fields');
        
        if (template && container) {
            const newField = template.cloneNode(true);
            newField.classList.remove('field-template', 'd-none');
            
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

    initCharacterCounter: function(textarea) {
        const maxLength = parseInt(textarea.dataset.maxLength);
        const counter = document.createElement('div');
        counter.className = 'character-counter text-muted small text-end mt-1';
        textarea.parentNode.appendChild(counter);
        
        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength}`;
            
            if (remaining < 50) {
                counter.classList.add('text-warning');
            } else {
                counter.classList.remove('text-warning');
            }
            
            if (remaining < 0) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-warning');
            } else {
                counter.classList.remove('text-danger');
            }
        };
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.PublicForms.init();
});

export default window.PublicForms;
