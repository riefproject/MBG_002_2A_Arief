@php
    use Illuminate\Support\Str;
@endphp

@props([
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'editData' => [],
    'deleteData' => [],
    'deleteName' => '',
    'deleteId' => '',
])

<div class="flex items-center space-x-1">
    @if($viewUrl)
        <a href="{{ $viewUrl }}"
           class="inline-flex items-center px-2 py-1 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
            <x-heroicon-o-eye class="h-3 w-3 mr-1" />
            Lihat
        </a>
    @endif

    @if($editUrl)
        <button type="button"
                class="tombol-edit-native inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150"
                data-action="{{ $editUrl }}"
                @foreach($editData as $key => $value)
                    data-{{ Str::kebab($key) }}="{{ $value }}"
                @endforeach>
            <x-heroicon-o-pencil class="h-3 w-3 mr-1" />
            Edit
        </button>
    @endif

    @if($deleteUrl)
        <button type="button"
                class="tombol-hapus-native inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150"
                data-action="{{ $deleteUrl }}"
                data-nama="{{ $deleteName }}"
                data-id="{{ $deleteId }}"
                @foreach($deleteData as $key => $value)
                    data-{{ Str::kebab($key) }}="{{ $value }}"
                @endforeach>
            <x-heroicon-o-trash class="h-3 w-3 mr-1" />
            Hapus
        </button>
    @endif
</div>

@once
@push('scripts')
<script>
(function () {
    if (window.__tableActionsBound) {
        return;
    }

    window.__tableActionsBound = true;

    const humanize = (value) => {
        if (!value) {
            return '-';
        }

        return value
            .toString()
            .replace(/_/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .replace(/\b\w/g, (char) => char.toUpperCase());
    };

    const fillDatasetFields = (container, attribute, dataset, transformer = (val) => val) => {
        container.querySelectorAll(attribute).forEach((field) => {
            const key = field.dataset.field || field.dataset.deleteField;
            if (!key) {
                return;
            }

            const datasetKey = key.charAt(0).toLowerCase() + key.slice(1);
            let value = dataset[datasetKey];

            if (key.toLowerCase().includes('status')) {
                value = humanize(value || dataset.status);
            }

            if (key === 'jumlahLabel' && (!value || value === '-') && dataset.jumlah && dataset.satuan) {
                value = `${dataset.jumlah} ${dataset.satuan}`;
            }

            field.textContent = transformer(value) || '-';
        });
    };

    const toggleSubmitState = (button, enabled) => {
        if (!button) {
            return;
        }

        button.disabled = !enabled;
        button.classList.toggle('opacity-60', !enabled);
        button.classList.toggle('cursor-not-allowed', !enabled);
    };

    const openEditModal = (button) => {
        const modal = document.getElementById('modal-edit');
        if (!modal) {
            return;
        }

        const form = modal.querySelector('#form-edit');
        if (!form) {
            return;
        }

        const data = button.dataset;
        form.action = data.action || '';

        const jumlahInput = form.querySelector('[name="jumlah"]');
        if (jumlahInput) {
            const jumlahValue = typeof data.jumlah !== 'undefined' ? data.jumlah : (typeof data.jumlahValue !== 'undefined' ? data.jumlahValue : '');
            jumlahInput.value = jumlahValue;
        }

        fillDatasetFields(modal, '[data-field]', data, (value) => value || '-');

        const statusRaw = (data.status || '').toLowerCase();
        const statusLabel = (data.statusLabel || '').toLowerCase();
        const isExpired = statusRaw === 'kadaluarsa' || statusLabel === 'kadaluarsa';

        const submitButton = form.querySelector('button[type="submit"]');
        const warningBox = modal.querySelector('[data-edit-warning]');

        if (jumlahInput) {
            jumlahInput.disabled = isExpired;
        }

        toggleSubmitState(submitButton, !isExpired);

        if (warningBox) {
            warningBox.classList.toggle('hidden', !isExpired);
        }

        if (typeof window.openModal === 'function') {
            window.openModal('modal-edit');
        }

        if (jumlahInput && !jumlahInput.disabled) {
            setTimeout(() => {
                jumlahInput.focus();
                jumlahInput.select();
            }, 120);
        }
    };

    const openDeleteModal = (button) => {
        const modal = document.getElementById('modal-hapus');
        if (!modal) {
            return;
        }

        const form = modal.querySelector('#form-hapus');
        if (!form) {
            return;
        }

        const data = button.dataset;
        const namaSpan = modal.querySelector('#nama-item');
        if (namaSpan) {
            namaSpan.textContent = data.nama || '';
        }

        form.action = data.action || '';
        form.dataset.targetId = data.id || '';

        fillDatasetFields(modal, '[data-delete-field]', data, (value) => value || '-');

        const statusRaw = (data.status || '').toLowerCase();
        const statusLabel = (data.statusLabel || '').toLowerCase();
        const isoDate = data.tanggalKadaluarsaIso;

        const expiredByStatus = statusRaw === 'kadaluarsa' || statusLabel === 'kadaluarsa';

        const expiredByDate = (() => {
            if (!isoDate) {
                return false;
            }
            const today = new Date();
            const expiry = new Date(`${isoDate}T00:00:00`);
            return !Number.isNaN(expiry.getTime()) && today >= expiry;
        })();

        const explicitFlag = ['true', '1', true, 1].includes(data.canDelete);
        const canDelete = expiredByStatus || expiredByDate || explicitFlag;
        const warningBox = modal.querySelector('[data-delete-warning]');
        const submitButton = form.querySelector('button[type="submit"]');

        if (warningBox) {
            if (canDelete) {
                warningBox.classList.add('hidden');
                warningBox.textContent = '';
            } else {
                warningBox.classList.remove('hidden');
                const infoStatus = [statusRaw, statusLabel]
                    .filter(Boolean)
                    .map((value) => value.replace(/_/g, ' '))
                    .join(' / ');
                const messageParts = [
                    'Status saat ini belum kadaluarsa, silakan periksa kembali tanggal kadaluarsa.',
                ];
                if (isoDate) {
                    messageParts.push(`Tanggal kadaluarsa: ${isoDate}`);
                }
                if (infoStatus) {
                    messageParts.push(`Status terdeteksi: ${infoStatus}`);
                }
                warningBox.textContent = messageParts.join(' ');
            }
        }

        toggleSubmitState(submitButton, canDelete);

        if (typeof window.openModal === 'function') {
            window.openModal('modal-hapus');
        }
    };

    document.addEventListener('click', (event) => {
        const editButton = event.target.closest('.tombol-edit-native');
        if (editButton) {
            event.preventDefault();
            openEditModal(editButton);
            return;
        }

        const deleteButton = event.target.closest('.tombol-hapus-native');
        if (deleteButton) {
            event.preventDefault();
            openDeleteModal(deleteButton);
        }
    });
})();
</script>
@endpush
@endonce
