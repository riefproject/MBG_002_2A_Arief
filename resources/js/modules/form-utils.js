// tampilin notif toast ringan
export function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto transition-all duration-300 transform translate-x-full';

    const bgMap = {
        success: 'bg-green-50 border-green-200',
        error: 'bg-red-50 border-red-200',
        info: 'bg-blue-50 border-blue-200',
    };

    const textMap = {
        success: 'text-green-800',
        error: 'text-red-800',
        info: 'text-blue-800',
    };

    const iconPathMap = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    };

    const iconColorMap = {
        success: 'text-green-400',
        error: 'text-red-400',
        info: 'text-blue-400',
    };

    const variant = bgMap[type] ? type : 'info';

    notification.innerHTML = `
        <div class="rounded-lg p-4 border ${bgMap[variant]}">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconColorMap[variant]}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        ${iconPathMap[variant]}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${textMap[variant]}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex rounded-md p-1.5 ${textMap[variant]} hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" onclick="this.closest('.fixed')?.remove()">
                            <span class="sr-only">Tutup</span>
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

    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// matiin atau hidupin state loading form
export function setFormLoading(form, loading = true) {
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
        inputs.forEach((input) => {
            if (input !== submitButton && input.type !== 'submit') {
                input.disabled = true;
            }
        });
    } else {
        submitButton.disabled = false;
        if (submitButton.dataset.originalInner) {
            submitButton.innerHTML = submitButton.dataset.originalInner;
        }
        inputs.forEach((input) => {
            if (input !== submitButton) {
                input.disabled = false;
            }
        });
    }
}

// bersihin error yang nempel di form
export function clearFormErrors(form) {
    const errorElements = form.querySelectorAll('.pesan-error');
    errorElements.forEach((el) => {
        el.textContent = '';
        el.classList.remove('text-green-500');
        el.classList.add('text-red-500');
    });

    const inputElements = form.querySelectorAll('input, textarea, select');
    inputElements.forEach((el) => {
        el.classList.remove('border-red-500', 'border-green-500');
    });
}

// tampilin error form berdasarkan response
export function displayFormErrors(form, errors) {
    Object.keys(errors).forEach((fieldName) => {
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
}
