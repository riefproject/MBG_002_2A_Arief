@props([
    'id' => '',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl
    'closable' => true
])

@php
$sizeClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
];
@endphp

<!-- Modal -->
<div id="{{ $id }}" 
     class="modal-backdrop fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center py-4">
    <div class="relative mx-auto p-6 border w-11/12 {{ $sizeClasses[$size] }} shadow-lg rounded-lg bg-white max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        @if($title || $closable)
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                @if($title)
                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                @endif
                
                @if($closable)
                    <button type="button" 
                            class="tutup-modal text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <!-- Modal Body -->
        <div class="mt-4">
            {{ $slot }}
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
// Universal modal controller dengan event delegation
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation untuk menutup modal
    document.body.addEventListener('click', function(event) {
        // Tutup modal ketika klik tombol close atau backdrop
        if (event.target.classList.contains('tutup-modal') || 
            event.target.closest('.tutup-modal') ||
            event.target.classList.contains('modal-backdrop')) {
            
            const modal = event.target.closest('[id]');
            if (modal && modal.id) {
                closeModal(modal.id);
            }
        }
    });

    // Tutup modal dengan ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-backdrop:not(.hidden)');
            if (openModal && openModal.id) {
                closeModal(openModal.id);
            }
        }
    });
});

// Global function untuk membuka modal
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Focus ke input pertama jika ada
        const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
};

// Global function untuk menutup modal
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        
        // Reset form jika ada
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            
            // Clear validation errors
            const errorElements = form.querySelectorAll('.pesan-error');
            errorElements.forEach(el => el.textContent = '');
            
            const inputElements = form.querySelectorAll('input, textarea, select');
            inputElements.forEach(el => {
                el.classList.remove('border-red-500', 'border-green-500');
            });
        }
    }
};
</script>
@endpush
@endonce