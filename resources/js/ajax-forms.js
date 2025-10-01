window.showNotification = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto transition-all duration-300 transform translate-x-full`;
    
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 
                   type === 'error' ? 'bg-red-50 border-red-200' : 
                   'bg-blue-50 border-blue-200';
    
    const textColor = type === 'success' ? 'text-green-800' : 
                     type === 'error' ? 'text-red-800' : 
                     'text-blue-800';
    
    const iconColor = type === 'success' ? 'text-green-400' : 
                     type === 'error' ? 'text-red-400' : 
                     'text-blue-400';
    
    const icon = type === 'success' ? 
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
        type === 'error' ? 
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    
    notification.innerHTML = `
        <div class="rounded-lg p-4 border ${bgColor}">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        ${icon}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex rounded-md p-1.5 ${textColor} hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" onclick="this.closest('.fixed').remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
};

// Global loading state handler
window.setFormLoading = function(form, loading = true) {
    const submitButton = form.querySelector('button[type="submit"]');
    const inputs = form.querySelectorAll('input, textarea, select, button');
    
    if (loading) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        inputs.forEach(input => {
            if (input.type !== 'submit') input.disabled = true;
        });
    } else {
        submitButton.disabled = false;
        // Restore original button text
        const originalText = submitButton.dataset.originalText || 
                           (form.id.includes('edit') ? 'Update User' :
                           form.id.includes('tambah') ? 'Simpan User' :
                           form.id.includes('password') ? 'Update Password' :
                           form.id.includes('profile') ? 'Update Profile' :
                           form.id.includes('hapus') ? 'Ya, Hapus' :
                           'Simpan');
        submitButton.textContent = originalText;
        inputs.forEach(input => {
            if (input.type !== 'submit') input.disabled = false;
        });
    }
};

// Clear form errors
window.clearFormErrors = function(form) {
    const errorElements = form.querySelectorAll('.pesan-error');
    errorElements.forEach(el => el.textContent = '');
    
    const inputElements = form.querySelectorAll('input, textarea, select');
    inputElements.forEach(el => {
        el.classList.remove('border-red-500', 'border-green-500');
    });
};

// Display form errors
window.displayFormErrors = function(form, errors) {
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorElement = field.nextElementSibling;
            if (errorElement && errorElement.classList.contains('pesan-error')) {
                errorElement.textContent = errors[fieldName][0];
                errorElement.className = 'pesan-error text-red-500 text-sm mt-1';
            }
            field.classList.add('border-red-500');
            field.classList.remove('border-green-500');
        }
    });
};

// Update table row for users
window.updateUserTableRow = function(user) {
    const row = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (row) {
        // Update name and email
        const nameCell = row.querySelector('.user-name');
        const emailCell = row.querySelector('.user-email');
        const roleCell = row.querySelector('.user-role');
        
        if (nameCell) nameCell.textContent = user.name;
        if (emailCell) emailCell.textContent = user.email;
        if (roleCell) {
            roleCell.textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
            roleCell.className = `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'
            }`;
        }
        
        // Update action buttons data attributes
        const editButton = row.querySelector('.tombol-edit-native');
        const deleteButton = row.querySelector('.tombol-hapus-native');
        
        if (editButton) {
            editButton.dataset.name = user.name;
            editButton.dataset.email = user.email;
            editButton.dataset.role = user.role;
        }
        
        if (deleteButton) {
            deleteButton.dataset.nama = user.name;
        }
    }
};

// Add new user row to table
window.addUserTableRow = function(user) {
    const tbody = document.querySelector('table tbody');
    if (tbody) {
        // Check if empty state message exists and remove it
        const emptyMessage = tbody.querySelector('tr td[colspan]');
        if (emptyMessage) {
            emptyMessage.closest('tr').remove();
        }
        
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50';
        newRow.dataset.userId = user.id;
        
        const editUrl = window.location.origin + '/admin/users/' + user.id;
        const deleteUrl = window.location.origin + '/admin/users/' + user.id;
        
        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                            ${user.name.charAt(0).toUpperCase()}
                        </span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 user-name">${user.name}</div>
                        <div class="text-sm text-gray-500 user-email">${user.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="user-role inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'}">
                    ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${user.created_at}
                <div class="text-xs text-gray-400">${user.created_at_diff}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${user.email_verified_at ? 
                    '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>' :
                    '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Unverified</span>'
                }
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                    <button class="tombol-edit-native text-blue-600 hover:text-blue-900 text-sm font-medium"
                            data-action="${editUrl}"
                            data-name="${user.name}"
                            data-email="${user.email}"
                            data-role="${user.role}">
                        Edit
                    </button>
                    <button class="tombol-hapus-native text-red-600 hover:text-red-900 text-sm font-medium ml-3"
                            data-action="${deleteUrl}"
                            data-nama="${user.name}"
                            data-id="${user.id}">
                        Hapus
                    </button>
                </div>
            </td>
        `;
        
        // Add to top of table
        tbody.insertBefore(newRow, tbody.firstChild);
    }
};

// Remove user row from table
window.removeUserTableRow = function(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (row) {
        row.remove();
    }
};

// Main AJAX form handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('AJAX Forms handler loaded');
    
    // Handle all form submissions with AJAX
    document.body.addEventListener('submit', function(event) {
        const form = event.target;
        
        // Only handle forms with ajax-form class or specific form IDs
        const isAjaxForm = form.classList.contains('ajax-form') || 
                          form.id === 'form-tambah' || 
                          form.id === 'form-edit' || 
                          form.id === 'form-hapus' ||
                          form.id === 'update-profile-form' ||
                          form.id === 'update-password-form';
        
        if (!isAjaxForm) {
            return;
        }
        
        // Skip login form and other non-CRUD forms
        if (form.id === 'login-form' || form.closest('#login-form')) {
            return;
        }
        
        console.log('Handling AJAX form submission for:', form.id);
        event.preventDefault();
        
        // Prevent double submission
        if (form.dataset.submitting === 'true') {
            return;
        }
        form.dataset.submitting = 'true';
        
        // Clear previous errors
        clearFormErrors(form);
        
        // Set loading state
        setFormLoading(form, true);
        
        // Prepare form data
        const formData = new FormData(form);
        const method = form.method.toLowerCase();
        const action = form.action;
        
        // Add AJAX headers
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
        
        // Add CSRF token if not already in FormData
        const csrfToken = form.querySelector('input[name="_token"]') || 
                         document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.value || csrfToken.getAttribute('content');
        }
        
        // Make AJAX request
        fetch(action, {
            method: method.toUpperCase(),
            body: formData,
            headers: headers
        })
        .then(response => {
            return response.json().then(data => ({
                ok: response.ok,
                status: response.status,
                data: data
            }));
        })
        .then(result => {
            setFormLoading(form, false);
            form.dataset.submitting = 'false';
            
            if (result.ok) {
                // Success handling
                showNotification(result.data.message, 'success');
                
                // Handle different form types
                if (form.id === 'form-tambah') {
                    // Add new user to table
                    addUserTableRow(result.data.data);
                    // Close modal and reset form
                    if (window.closeModal) {
                        closeModal('modal-tambah');
                    }
                } else if (form.id === 'form-edit') {
                    // Update existing user in table
                    updateUserTableRow(result.data.data);
                    // Close modal
                    if (window.closeModal) {
                        closeModal('modal-edit');
                    }
                } else if (form.id === 'form-hapus') {
                    // Remove user from table
                    const formAction = form.action;
                    const userId = formAction.substring(formAction.lastIndexOf('/') + 1);
                    removeUserTableRow(userId);
                    // Close modal
                    if (window.closeModal) {
                        closeModal('modal-hapus');
                    }
                } else if (form.id === 'update-profile-form') {
                    // Update profile info in header dropdown
                    const headerName = document.querySelector('.py-1 .font-medium');
                    const headerEmail = document.querySelector('.py-1 .text-xs');
                    if (headerName && result.data.data) {
                        headerName.textContent = result.data.data.name;
                    }
                    if (headerEmail && result.data.data) {
                        headerEmail.textContent = result.data.data.email;
                    }
                    // Also update the avatar initial
                    const avatarInitial = document.querySelector('.h-8.w-8 .text-sm');
                    if (avatarInitial && result.data.data) {
                        avatarInitial.textContent = result.data.data.name.charAt(0).toUpperCase();
                    }
                } else if (form.id === 'update-password-form') {
                    // Reset password form
                    form.reset();
                    clearFormErrors(form);
                }
                
            } else {
                // Error handling
                if (result.data.errors) {
                    displayFormErrors(form, result.data.errors);
                    showNotification('Please fix the errors below.', 'error');
                } else {
                    showNotification(result.data.message || 'An error occurred.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            setFormLoading(form, false);
            form.dataset.submitting = 'false';
            showNotification('An unexpected error occurred. Please try again.', 'error');
        });
    });
});