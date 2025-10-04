// baca json tapi aman kalo datanya aneh
export function parseJsonSafe(value, fallback = []) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch (error) {
        return fallback;
    }
}

// tunggu helper modal siap dipake
function waitForModalHelpers(callback, attempt = 0, maxAttempt = 20) {
    if (typeof window.openModal === 'function' && typeof window.closeModal === 'function') {
        callback();
        return;
    }

    if (attempt >= maxAttempt) {
        return;
    }

    setTimeout(() => waitForModalHelpers(callback, attempt + 1, maxAttempt), 50);
}

// buka modal tanpa takut helper belum ada
export function openModalSafely(modalId) {
    waitForModalHelpers(() => window.openModal(modalId));
}
