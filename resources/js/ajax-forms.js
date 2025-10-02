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
    
    // Auto hide setelah 5 detik
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
};

const escapeHtml = (value) => {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

const humanizeValue = (value) => {
    if (!value) {
        return '-';
    }

    return value.toString()
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

// Global loading state handler
window.setFormLoading = function(form, loading = true) {
    const submitButton = form.querySelector('button[type="submit"]');
    const inputs = form.querySelectorAll('input, textarea, select, button');
    
    if (!submitButton) {
        return;
    }

    if (loading) {
        submitButton.disabled = true;
        submitButton.dataset.originalInner = submitButton.innerHTML;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        inputs.forEach(input => {
            if (!['submit', 'button'].includes(input.type) || input === submitButton) {
                input.disabled = true;
            }
        });
    } else {
        submitButton.disabled = false;
        // Restore original button text
        if (submitButton.dataset.originalInner) {
            submitButton.innerHTML = submitButton.dataset.originalInner;
        }
        inputs.forEach(input => {
            if (input !== submitButton) {
                input.disabled = false;
            }
        });
    }
};

// Clear form errors
window.clearFormErrors = function(form) {
    const errorElements = form.querySelectorAll('.pesan-error');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('text-green-500');
        el.classList.add('text-red-500');
    });
    
    const inputElements = form.querySelectorAll('input, textarea, select');
    inputElements.forEach(el => {
        el.classList.remove('border-red-500', 'border-green-500');
    });
};

// Display form errors
window.displayFormErrors = function(form, errors) {
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field) {
            return;
        }

        const errorElement = form.querySelector(`[data-error-for="${field.id}"]`) || field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('pesan-error')) {
            errorElement.textContent = errors[fieldName][0];
            errorElement.classList.remove('text-green-500');
            errorElement.classList.add('text-red-500');
        }

        field.classList.add('border-red-500');
        field.classList.remove('border-green-500');
    });
};



const bahanBakuTable = {
    tableSelector: '#tabel-bahan-baku',

    getTable() {
        return document.querySelector(this.tableSelector);
    },

    getTbody() {
        const table = this.getTable();
        return table ? table.querySelector('tbody') : null;
    },

    hydrateRow(row, data) {
        if (!row || !data) {
            return;
        }

        const formatDate = (value, fallback = '-') => {
            if (!value) {
                return fallback;
            }

            if (value instanceof Date) {
                return value.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }

            return value;
        };

        this.renderRow(row, data);
    },

    createRow(data) {
        const tbody = this.getTbody();
        if (!tbody) {
            return null;
        }

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.dataset.bahanBakuId = data.id;

        this.renderRow(row, data);

        tbody.insertBefore(row, tbody.firstChild);
        document.dispatchEvent(new Event('bahan-baku:table-updated'));

        return row;
    },

    renderRow(row, data) {
        if (!row || !data) {
            return;
        }

        const tanggalMasuk = escapeHtml(data.tanggal_masuk_label || data.tanggal_masuk || '-');
        const tanggalKadaluarsa = escapeHtml(data.tanggal_kadaluarsa_label || data.tanggal_kadaluarsa || '-');
        const statusRaw = data.status_label || data.status;
        const statusLabel = escapeHtml(humanizeValue(statusRaw));
        const jumlahLabel = escapeHtml(data.jumlah_label || `${data.jumlah} ${data.satuan}`);
        const nama = escapeHtml(data.nama);
        const kategori = escapeHtml(data.kategori);
        const jumlah = escapeHtml(data.jumlah);
        const satuan = escapeHtml(data.satuan);
        const statusValue = escapeHtml(data.status);
        const viewUrl = escapeHtml(data.view_url || '');
        const editUrl = escapeHtml(data.edit_url || '');
        const deleteUrl = escapeHtml(data.delete_url || '');
        const id = escapeHtml(data.id);
        const canDelete = data.can_delete ? 'true' : 'false';

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">${nama}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${kategori}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${jumlah}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${satuan}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tanggalMasuk}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tanggalKadaluarsa}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="capitalize">${statusLabel}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-1">
                    ${viewUrl ? `
                        <a href="${viewUrl}" 
                           class="inline-flex items-center px-2 py-1 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </a>
                    ` : ''}
                    <button type="button"
                            class="tombol-edit-native inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150"
                            data-action="${editUrl}"
                            data-id="${id}"
                            data-nama="${nama}"
                            data-kategori="${kategori}"
                            data-jumlah="${jumlah}"
                            data-satuan="${satuan}"
                            data-jumlah-label="${jumlahLabel}"
                            data-status="${statusValue}"
                            data-status-label="${statusLabel}"
                            data-tanggal-masuk-label="${tanggalMasuk}"
                            data-tanggal-kadaluarsa-label="${tanggalKadaluarsa}">
                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652l-1.688 1.687m-2.651-2.651L8.25 15.75M19.5 19.5h-15" />
                        </svg>
                        Edit
                    </button>
                    <button type="button"
                            class="tombol-hapus-native inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150"
                            data-action="${deleteUrl}"
                            data-nama="${nama}"
                            data-id="${id}"
                            data-kategori="${kategori}"
                            data-jumlah="${jumlah}"
                            data-satuan="${satuan}"
                            data-jumlah-label="${jumlahLabel}"
                            data-status="${statusValue}"
                            data-status-label="${statusLabel}"
                            data-tanggal-masuk-label="${tanggalMasuk}"
                            data-tanggal-kadaluarsa-label="${tanggalKadaluarsa}"
                            data-can-delete="${canDelete}">
                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </td>
        `;
    },

    updateRow(data) {
        const row = document.querySelector(`tr[data-bahan-baku-id="${data.id}"]`);
        if (!row) {
            return this.createRow(data);
        }

        this.renderRow(row, data);
        document.dispatchEvent(new Event('bahan-baku:table-updated'));

        return row;
    },

    removeRow(id) {
        const row = document.querySelector(`tr[data-bahan-baku-id="${id}"]`);
        if (row) {
            row.remove();
            document.dispatchEvent(new Event('bahan-baku:table-updated'));
        }
    }
};

// Main AJAX form handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('AJAX Forms handler loaded');
    
    // Handle semua form submissions with AJAX
    document.body.addEventListener('submit', function(event) {
        const form = event.target;
        
        // Hanya tangani form dengan kelas ajax-form atau ID form tertentu
        const isAjaxForm = form.classList.contains('ajax-form') || 
                          form.id === 'form-tambah' || 
                          form.id === 'form-edit' || 
                          form.id === 'form-hapus' ||
                          form.id === 'update-profile-form' ||
                          form.id === 'update-password-form';
        
        if (!isAjaxForm) {
            return;
        }
        
        // Skip login dan non CRUD forms
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
        
        clearFormErrors(form);
        setFormLoading(form, true);
        
        const formData = new FormData(form);
        const overrideMethod = formData.get('_method');
        const method = (overrideMethod || form.method).toLowerCase();
        const action = form.action;
        
        // AJAX headers
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
        
        // Tambahkan token CSRF jika belum ada di FormData
        const csrfToken = form.querySelector('input[name="_token"]') || 
                         document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.value || csrfToken.getAttribute('content');
        }
        
        fetch(action, {
            method: overrideMethod ? overrideMethod.toUpperCase() : form.method.toUpperCase(),
            body: method === 'get' ? null : formData,
            headers: headers
        })
        .then(response => {
            return response.json().then(data => ({
                ok: response.ok,
                status: response.status,
                data
            }));
        })
        .then(result => {
            setFormLoading(form, false);
            form.dataset.submitting = 'false';
            
            if (result.ok) {
                const responsePayload = result.data;
                showNotification(responsePayload.message, 'success');
            
                if (form.dataset.entity === 'bahan-baku') {
                    const responseData = responsePayload.data;
                    if (form.id === 'form-tambah') {
                        bahanBakuTable.createRow(responseData);
                        if (window.closeModal) {
                            closeModal('modal-tambah');
                        }
                        form.reset();
                    } else if (form.id === 'form-edit') {
                        bahanBakuTable.updateRow(responseData);
                        if (window.closeModal) {
                            closeModal('modal-edit');
                        }
                    } else if (form.id === 'form-hapus') {
                        const targetId = responseData?.id || form.dataset.targetId;
                        if (targetId) {
                            bahanBakuTable.removeRow(targetId);
                        }
                        if (window.closeModal) {
                            closeModal('modal-hapus');
                        }
                    }
                } else if (form.id === 'update-profile-form') {
                    // Update profile info ke header dropdown
                    const headerName = document.querySelector('.py-1 .font-medium');
                    const headerEmail = document.querySelector('.py-1 .text-xs');
                    if (headerName && result.data.data) {
                        headerName.textContent = result.data.data.name;
                    }
                    if (headerEmail && result.data.data) {
                        headerEmail.textContent = result.data.data.email;
                    }
                    // Also update avatar initial
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
            showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
        });
    });
});