/**
 * UI Components Demo Functions
 * Functions for testing various UI components in the admin panel
 */

// UI Components object
const uiComponents = {
    init: initUIComponents,
    testSweetSuccess,
    testSweetError,
    testSweetWarning,
    testSweetInfo,
    testSweetConfirm,
    testSweetDelete,
    testSweetLoading,
    getSelectValues,
    clearSelects,
    setSelectValues,
    getDateValues,
    clearDates,
    setDateValues,
    refreshTable,
    editUser,
    deleteUser,
    submitDemoForm,
    resetDemoForm,
    checkDependencies
};

// Check if all dependencies are available
function checkDependencies() {
    const missing = [];
    
    if (typeof window.sweetAlert === 'undefined') missing.push('sweetAlert');
    if (typeof window.$ === 'undefined') missing.push('jQuery');
    
    if (missing.length > 0) {
        console.warn('UI Components: Missing dependencies:', missing.join(', '));
        return false;
    }
    
    return true;
}

// SweetAlert2 Demo Functions
function testSweetSuccess() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.success('Success!', 'Operation completed successfully.');
    } else {
        alert('SweetAlert2 is not available. Please ensure all dependencies are loaded.');
    }
}

function testSweetError() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.error('Error!', 'Something went wrong. Please try again.');
    } else {
        alert('SweetAlert2 is not available. Please ensure all dependencies are loaded.');
    }
}

function testSweetWarning() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.warning('Warning!', 'Please check your input before proceeding.');
    } else {
        alert('SweetAlert2 is not available. Please ensure all dependencies are loaded.');
    }
}

function testSweetInfo() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.info('Information', 'This is an informational message.');
    } else {
        alert('SweetAlert2 is not available. Please ensure all dependencies are loaded.');
    }
}

function testSweetConfirm() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.confirm('Are you sure?', 'This action cannot be undone.')
            .then((result) => {
                if (result.isConfirmed) {
                    sweetAlert.success('Confirmed!', 'Your action has been executed.');
                }
            });
    } else {
        if (confirm('Are you sure? This action cannot be undone.')) {
            alert('Confirmed! Your action has been executed.');
        }
    }
}

function testSweetDelete() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.confirmDelete()
            .then((result) => {
                if (result.isConfirmed) {
                    sweetAlert.success('Deleted!', 'The item has been deleted.');
                }
            });
    } else {
        if (confirm('Are you sure you want to delete this? This action cannot be undone.')) {
            alert('Deleted! The item has been deleted.');
        }
    }
}

function testSweetLoading() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.loading('Processing...');
        
        // Simulate API call
        setTimeout(() => {
            sweetAlert.close();
            sweetAlert.success('Complete!', 'Process finished successfully.');
        }, 3000);
    } else {
        alert('Loading simulation would appear here with SweetAlert2.');
    }
}

// Simple Demo Functions (without external libraries)
function getSelectValues() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    // Get values from basic select elements
    const single = $('#single-select').val() || 'No selection';
    const multiple = $('#multiple-select').val() || [];
    
    const values = {
        'Single Select': single,
        'Multiple Select': Array.isArray(multiple) ? multiple.join(', ') : 'No selection'
    };
    
    let html = '<div class="alert alert-info"><h6>Current Values:</h6>';
    for (const [key, value] of Object.entries(values)) {
        html += `<strong>${key}:</strong> ${value}<br>`;
    }
    html += '</div>';
    
    $('#select-values').html(html);
}

function clearSelects() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    $('#single-select').val('');
    $('#multiple-select').val([]);
    $('#select-values').html('<div class="alert alert-secondary">All selections cleared.</div>');
}

function setSelectValues() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    $('#single-select').val('option2');
    $('#multiple-select').val(['option1', 'option3']);
    $('#select-values').html('<div class="alert alert-success">Values have been set programmatically.</div>');
}

function getDateValues() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    const dateRange = $('#daterange-input').val() || 'No selection';
    const singleDate = $('#single-date-input').val() || 'No selection';
    
    const values = {
        'Date Range': dateRange,
        'Single Date': singleDate
    };
    
    let html = '<div class="alert alert-info"><h6>Current Date Values:</h6>';
    for (const [key, value] of Object.entries(values)) {
        html += `<strong>${key}:</strong> ${value}<br>`;
    }
    html += '</div>';
    
    $('#date-values').html(html);
}

function clearDates() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    $('#daterange-input').val('');
    $('#single-date-input').val('');
    $('#date-values').html('<div class="alert alert-secondary">All dates cleared.</div>');
}

function setDateValues() {
    if (typeof window.$ === 'undefined') {
        alert('jQuery is not available. Please ensure all dependencies are loaded.');
        return;
    }
    
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    
    $('#daterange-input').val(`${today} - ${nextWeek}`);
    $('#single-date-input').val(today);
    $('#date-values').html('<div class="alert alert-success">Dates have been set programmatically.</div>');
}

function refreshTable() {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.success('Refreshed!', 'Table would be reloaded here.');
    } else {
        alert('Refreshed! Table would be reloaded here.');
    }
}

function editUser(userId) {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.info('Edit User', `Edit functionality for user ID: ${userId}`);
    } else {
        alert(`Edit functionality for user ID: ${userId}`);
    }
}

function deleteUser(userId) {
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.confirmDelete('Delete User', 'This will permanently delete the user.')
            .then((result) => {
                if (result.isConfirmed) {
                    sweetAlert.success('Deleted!', `User ${userId} has been deleted.`);
                }
            });
    } else {
        if (confirm(`Delete User ${userId}? This will permanently delete the user.`)) {
            alert(`User ${userId} has been deleted.`);
        }
    }
}

// Combined Demo Form Functions
function submitDemoForm() {
    const form = document.getElementById('demo-form');
    const formData = new FormData(form);
    
    // Show loading
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.loading('Submitting form...');
        
        // Simulate API call
        setTimeout(() => {
            sweetAlert.close();
            sweetAlert.success('Success!', 'Form submitted successfully.');
            resetDemoForm();
        }, 2000);
    } else {
        // Fallback without SweetAlert
        alert('Form submitted successfully!');
        resetDemoForm();
    }
}

function resetDemoForm() {
    const form = document.getElementById('demo-form');
    if (form) {
        form.reset();
    }
    
    if (typeof window.sweetAlert !== 'undefined') {
        sweetAlert.info('Reset', 'Form has been reset.');
    } else {
        alert('Form has been reset.');
    }
}

// Initialize UI Components Demo
function initUIComponents() {
    // Check dependencies first
    const dependenciesReady = checkDependencies();
    
    if (!dependenciesReady) {
        console.warn('UI Components: Some dependencies are not ready yet. Retrying in 500ms...');
        setTimeout(initUIComponents, 500);
        return;
    }
    
    console.log('UI Components initialized successfully!');
}

// Global scope assignments for backward compatibility
window.testSweetSuccess = testSweetSuccess;
window.testSweetError = testSweetError;
window.testSweetWarning = testSweetWarning;
window.testSweetInfo = testSweetInfo;
window.testSweetConfirm = testSweetConfirm;
window.testSweetDelete = testSweetDelete;
window.testSweetLoading = testSweetLoading;
window.getSelectValues = getSelectValues;
window.clearSelects = clearSelects;
window.setSelectValues = setSelectValues;
window.getDateValues = getDateValues;
window.clearDates = clearDates;
window.setDateValues = setDateValues;
window.refreshTable = refreshTable;
window.editUser = editUser;
window.deleteUser = deleteUser;
window.submitDemoForm = submitDemoForm;
window.resetDemoForm = resetDemoForm;
window.initUIComponents = initUIComponents;

// Auto-initialize with retry mechanism
let initAttempts = 0;
const maxAttempts = 10;

function tryInitialize() {
    initAttempts++;
    
    if (checkDependencies()) {
        initUIComponents();
    } else if (initAttempts < maxAttempts) {
        setTimeout(tryInitialize, 200 * initAttempts); // Increasing delay
    } else {
        console.error('UI Components: Failed to initialize after', maxAttempts, 'attempts. Some dependencies may be missing.');
    }
}

// Make uiComponents globally available
window.uiComponents = uiComponents;

// Start initialization process
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tryInitialize);
} else {
    tryInitialize();
}

export default uiComponents;
