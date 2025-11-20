/**
 * AJAX Demo Functions for Laravel Admin Panel
 * Demo functions for testing various AJAX request types
 */

// AJAX GET Test
async function testAjaxGet() {
    const resultDiv = document.getElementById('get-result');
    
    try {
        toggleLoading(true, '#get-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxGet(`${baseUrl}/api/demo/users`);
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>GET Request Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        showNotification('GET request completed successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('GET request failed!', 'error');
    }
}

// AJAX POST Test
async function testAjaxPost() {
    const resultDiv = document.getElementById('post-result');
    const name = document.getElementById('post-name').value;
    const email = document.getElementById('post-email').value;
    
    if (!name || !email) {
        showNotification('Please fill in both name and email fields!', 'warning');
        return;
    }
    
    try {
        toggleLoading(true, '#post-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxPost(`${baseUrl}/api/demo/users`, {
            name: name,
            email: email
        });
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>POST Request Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        // Clear inputs
        document.getElementById('post-name').value = '';
        document.getElementById('post-email').value = '';
        
        showNotification('User created successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('POST request failed!', 'error');
    }
}

// AJAX PUT Test
async function testAjaxPut() {
    const resultDiv = document.getElementById('put-result');
    const id = document.getElementById('put-id').value;
    const name = document.getElementById('put-name').value;
    
    if (!id || !name) {
        showNotification('Please fill in both ID and name fields!', 'warning');
        return;
    }
    
    try {
        toggleLoading(true, '#put-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxPut(`${baseUrl}/api/demo/users/${id}`, {
            name: name,
            email: `updated_${name.toLowerCase()}@example.com`
        });
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>PUT Request Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        showNotification('User updated successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('PUT request failed!', 'error');
    }
}

// AJAX PATCH Test
async function testAjaxPatch() {
    const resultDiv = document.getElementById('patch-result');
    const id = document.getElementById('patch-id').value;
    const status = document.getElementById('patch-status').value;
    
    if (!id || !status) {
        showNotification('Please fill in both ID and status fields!', 'warning');
        return;
    }
    
    try {
        toggleLoading(true, '#patch-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxPatch(`${baseUrl}/api/demo/users/${id}/status`, {
            status: status
        });
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>PATCH Request Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        showNotification('User status updated successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('PATCH request failed!', 'error');
    }
}

// AJAX DELETE Test
async function testAjaxDelete() {
    const resultDiv = document.getElementById('delete-result');
    const id = document.getElementById('delete-id').value;
    
    if (!id) {
        showNotification('Please enter an ID to delete!', 'warning');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }
    
    try {
        toggleLoading(true, '#delete-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxDelete(`${baseUrl}/api/demo/users/${id}`);
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>DELETE Request Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        showNotification('User deleted successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('DELETE request failed!', 'error');
    }
}

// AJAX Form Test
async function testAjaxForm() {
    const resultDiv = document.getElementById('form-result');
    const form = document.getElementById('upload-form');
    const formData = new FormData(form);
    
    if (!formData.get('title')) {
        showNotification('Please enter a title!', 'warning');
        return;
    }
    
    try {
        toggleLoading(true, '#form-result');
        
        const baseUrl = getBaseUrl();
        const response = await ajaxForm(`${baseUrl}/api/demo/upload`, formData, 'POST');
        
        resultDiv.innerHTML = `
            <div class="result-container">
                <h6 class="text-success mb-2">
                    <i class="fas fa-check-circle me-2"></i>Form Upload Successful
                </h6>
                <pre>${JSON.stringify(response, null, 2)}</pre>
            </div>
        `;
        
        // Reset form
        form.reset();
        
        showNotification('Form submitted successfully!', 'success');
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${error.message}
            </div>
        `;
        showNotification('Form submission failed!', 'error');
    }
}

// Global scope assignments for backward compatibility
window.testAjaxGet = testAjaxGet;
window.testAjaxPost = testAjaxPost;
window.testAjaxPut = testAjaxPut;
window.testAjaxPatch = testAjaxPatch;
window.testAjaxDelete = testAjaxDelete;
window.testAjaxForm = testAjaxForm;
