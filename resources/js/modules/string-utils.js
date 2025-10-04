// escape teks biar ga nyelip html liar
export const escapeHtml = (value) => {
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

// ubah string snake_case jadi lebih kebaca
export const humanizeValue = (value) => {
    if (!value) {
        return '-';
    }

    return value.toString()
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};
