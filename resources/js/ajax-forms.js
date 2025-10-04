import bahanBakuTable from './modules/bahan-baku-table';
import {
    showNotification,
    setFormLoading,
    clearFormErrors,
    displayFormErrors,
} from './modules/form-utils';

window.showNotification = showNotification;
window.setFormLoading = setFormLoading;
window.clearFormErrors = clearFormErrors;
window.displayFormErrors = displayFormErrors;
window.bahanBakuTable = bahanBakuTable;

// setup handler ajax global pas dom siap
document.addEventListener('DOMContentLoaded', function() {
    // handle submit ajax buat form yg diijinin
    document.body.addEventListener('submit', function(event) {
        const form = event.target;
        
        // cek dulu formnya termasuk whitelist
        const isAjaxForm = form.classList.contains('ajax-form') || 
                          form.id === 'form-tambah' || 
                          form.id === 'form-edit' || 
                          form.id === 'form-hapus' ||
                          form.id === 'update-profile-form' ||
                          form.id === 'update-password-form';
        
        if (!isAjaxForm) {
            return;
        }
        
        // skip login dan form lain yg ga perlu ajax
        if (form.id === 'login-form' || form.closest('#login-form')) {
            return;
        }
        
        event.preventDefault();
        
        // cegah submit dobel
        if (form.dataset.submitting === 'true') {
            return;
        }
        form.dataset.submitting = 'true';
        
    clearFormErrors(form);

    const formData = new FormData(form);

    setFormLoading(form, true);
        const overrideMethod = formData.get('_method');
        const defaultMethod = (form.method || 'GET').toUpperCase();
        const submitMethod = overrideMethod ? 'POST' : defaultMethod;
        const normalizedMethod = submitMethod.toLowerCase();
        const action = form.action;
        
        // header dasar buat request ajax
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
        
        // tambahin token csrf kalo belum ada
        const csrfToken = form.querySelector('input[name="_token"]') || 
                         document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.value || csrfToken.getAttribute('content');
        }
        
        if (overrideMethod) {
            headers['X-HTTP-Method-Override'] = overrideMethod.toUpperCase();
        }

        fetch(action, {
            method: submitMethod,
            body: normalizedMethod === 'get' ? null : formData,
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
                        document.dispatchEvent(new CustomEvent('bahan-baku:created', { detail: responseData }));
                        if (window.closeModal) {
                            closeModal('modal-tambah');
                        }
                        form.reset();
                    } else if (form.id === 'form-edit') {
                        bahanBakuTable.updateRow(responseData);
                        document.dispatchEvent(new CustomEvent('bahan-baku:updated', { detail: responseData }));
                        if (window.closeModal) {
                            closeModal('modal-edit');
                        }
                    } else if (form.id === 'form-hapus') {
                        const targetId = responseData?.id || form.dataset.targetId;
                        if (targetId) {
                            bahanBakuTable.removeRow(targetId);
                            document.dispatchEvent(new CustomEvent('bahan-baku:deleted', { detail: { id: targetId } }));
                        }
                        if (window.closeModal) {
                            closeModal('modal-hapus');
                        }
                    }
                } else if (form.id === 'update-profile-form') {
                    const responseData = responsePayload.data || {};

                    const nameInput = form.querySelector('[name="name"]');
                    if (nameInput) {
                        nameInput.value = responseData.name || '';
                    }

                    const emailInput = form.querySelector('[name="email"]');
                    if (emailInput) {
                        emailInput.value = responseData.email || '';
                    }

                    const navbarName = document.getElementById('navbar-user-name');
                    if (navbarName) {
                        navbarName.textContent = responseData.name || navbarName.textContent;
                    }

                    const navbarEmail = document.getElementById('navbar-user-email');
                    if (navbarEmail) {
                        navbarEmail.textContent = responseData.email || navbarEmail.textContent;
                    }

                    const navbarInitial = document.getElementById('navbar-avatar-initial');
                    if (navbarInitial && responseData.initial) {
                        navbarInitial.textContent = responseData.initial;
                    }

                    const mobileName = document.getElementById('mobile-user-name');
                    if (mobileName) {
                        mobileName.textContent = responseData.name || mobileName.textContent;
                    }

                    const mobileEmail = document.getElementById('mobile-user-email');
                    if (mobileEmail) {
                        mobileEmail.textContent = responseData.email || mobileEmail.textContent;
                    }

                    const mobileInitial = document.getElementById('mobile-avatar-initial');
                    if (mobileInitial && responseData.initial) {
                        mobileInitial.textContent = responseData.initial;
                    }

                    const profileHeaderName = document.getElementById('profile-header-name');
                    if (profileHeaderName) {
                        profileHeaderName.textContent = responseData.name || profileHeaderName.textContent;
                    }

                    const profileHeaderEmail = document.getElementById('profile-header-email');
                    if (profileHeaderEmail) {
                        profileHeaderEmail.textContent = responseData.email || profileHeaderEmail.textContent;
                    }

                    const profileInitial = document.getElementById('profile-avatar-initial');
                    if (profileInitial && responseData.initial) {
                        profileInitial.textContent = responseData.initial;
                    }

                    const profileLastUpdated = document.getElementById('profile-last-updated');
                    if (profileLastUpdated && responseData.updated_at_diff) {
                        profileLastUpdated.textContent = responseData.updated_at_diff;
                    }
                } else if (form.id === 'update-password-form') {
                    // reset password form biar bersih
                    form.reset();
                    clearFormErrors(form);
                }

            } else {
                // error handling santai
                if (result.data.errors) {
                    displayFormErrors(form, result.data.errors);
                    showNotification('Please fix the errors below.', 'error');
                } else {
                    showNotification(result.data.message || 'An error occurred.', 'error');
                }
            }
        })
        .catch(() => {
            setFormLoading(form, false);
            form.dataset.submitting = 'false';
            showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
        });
    });
});
