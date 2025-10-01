@props([
    'editUrl' => null,
    'deleteUrl' => null,
    'editData' => [],
    'deleteName' => '',
    'deleteId' => '',
    'size' => 'sm'
])

<div class="flex items-center space-x-2">
    @if($editUrl)
        <button type="button"
                class="tombol-edit-native inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150"
                data-action="{{ $editUrl }}"
                @foreach($editData as $key => $value)
                    data-{{ $key }}="{{ $value }}"
                @endforeach>
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
        </button>
    @endif

    @if($deleteUrl)
        <button type="button"
                class="tombol-hapus-native inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150"
                data-action="{{ $deleteUrl }}"
                data-nama="{{ $deleteName }}"
                data-id="{{ $deleteId }}">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus
        </button>
    @endif
</div>

@once
@push('scripts')
<script>
// Event delegation untuk tombol edit dengan modal form
document.body.addEventListener('click', function(event) {
    if (event.target.classList.contains('tombol-edit-native') || event.target.closest('.tombol-edit-native')) {
        event.preventDefault();
        
        const button = event.target.classList.contains('tombol-edit-native') 
            ? event.target 
            : event.target.closest('.tombol-edit-native');
        
        // Ambil data dari attributes
        const data = button.dataset;
        
        // Update modal form jika ada
        const modal = document.getElementById('modal-edit');
        if (modal) {
            const form = modal.querySelector('#form-edit');
            
            if (form) {
                form.action = data.action;
                
                // Populate form fields
                Object.keys(data).forEach(key => {
                    if (key !== 'action') {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.value = data[key];
                        }
                    }
                });
            }
            
            // Show modal
            if (window.openModal) {
                window.openModal('modal-edit');
            }
        }
    }
});

// Event delegation untuk tombol hapus dengan konfirmasi modal
document.body.addEventListener('click', function(event) {
    if (event.target.classList.contains('tombol-hapus-native') || event.target.closest('.tombol-hapus-native')) {
        event.preventDefault();
        
        const button = event.target.classList.contains('tombol-hapus-native') 
            ? event.target 
            : event.target.closest('.tombol-hapus-native');
        
        const nama = button.dataset.nama;
        const id = button.dataset.id;
        const formAction = button.dataset.action;
        
        // Update modal content
        const modal = document.getElementById('modal-hapus');
        if (modal) {
            const namaSpan = modal.querySelector('#nama-item');
            const confirmForm = modal.querySelector('#form-hapus');
            
            if (namaSpan) namaSpan.textContent = nama;
            if (confirmForm) confirmForm.action = formAction;
            
            // Show modal
            if (window.openModal) {
                window.openModal('modal-hapus');
            }
        }
    }
});
</script>
@endpush
@endonce