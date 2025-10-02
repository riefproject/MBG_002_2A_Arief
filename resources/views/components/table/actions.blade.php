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
(function() {
    if (window.__tableActionsBound) {
        return;
    }
    window.__tableActionsBound = true;

    const humanize = (value) => {
        if (!value) {
            return '-';
        }

        return value.toString()
            .replace(/_/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .replace(/\b\w/g, (char) => char.toUpperCase());
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
        if (jumlahInput && typeof data.jumlah !== 'undefined') {
            jumlahInput.value = data.jumlah;
        }

        const displayFields = modal.querySelectorAll('[data-field]');
        displayFields.forEach((field) => {
            const key = field.dataset.field;
            if (!key) {
                return;
            }

            const datasetKey = key.charAt(0).toLowerCase() + key.slice(1);
            let value = data[datasetKey];

            if (key.toLowerCase().includes('status')) {
                value = humanize(value || data.status);
            }

            if (key === 'jumlahLabel' && (!value || value === '-') && data.jumlah && data.satuan) {
                value = `${data.jumlah} ${data.satuan}`;
            }

            field.textContent = value || '-';
        });

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
        }

        if (jumlahInput) {
            jumlahInput.focus();
            jumlahInput.select();
        }

        if (typeof window.openModal === 'function') {
            window.openModal('modal-edit');
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

        const detailFields = modal.querySelectorAll('[data-delete-field]');
        detailFields.forEach((field) => {
            const key = field.dataset.deleteField;
            if (!key) {
                return;
            }

            const datasetKey = key.charAt(0).toLowerCase() + key.slice(1);
            let value = data[datasetKey];

            if (key.toLowerCase().includes('status')) {
                value = humanize(data.statusLabel || data.status);
            }

            if (key === 'jumlahLabel' && (!value || value === '-') && data.jumlah && data.satuan) {
                value = `${data.jumlah} ${data.satuan}`;
            }

            field.textContent = value || '-';
        });

        const warningBox = modal.querySelector('[data-delete-warning]');
        const submitButton = form.querySelector('button[type="submit"]');
        const canDelete = data.canDelete === 'true' || data.status === 'kadaluarsa';

        if (submitButton) {
            submitButton.disabled = !canDelete;
            submitButton.classList.toggle('opacity-60', !canDelete);
            submitButton.classList.toggle('cursor-not-allowed', !canDelete);
        }

        if (warningBox) {
            if (!canDelete) {
                warningBox.classList.remove('hidden');
                warningBox.textContent = 'Status saat ini bukan kadaluarsa. Bahan baku hanya bisa dihapus ketika statusnya kadaluarsa.';
            } else {
                warningBox.classList.add('hidden');
                warningBox.textContent = '';
            }
        }

        if (typeof window.openModal === 'function') {
            window.openModal('modal-hapus');
        }
    };

    document.addEventListener('click', (event) => {
        const editButton = event.target.closest('.tombol-edit-native');
        if (editButton) {
            event.preventDefault();
            openEditModal(editButton);
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