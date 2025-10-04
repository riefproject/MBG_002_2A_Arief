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
                        <x-heroicon-o-x-mark class="h-6 w-6" />
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
// kontrol modal global pake event delegation
document.addEventListener('DOMContentLoaded', function() {
    // handle klik buka / tutup modal
    document.body.addEventListener('click', function(event) {
        const openTrigger = event.target.closest('[data-action="open-modal"]');
        if (openTrigger) {
            event.preventDefault();
            event.stopPropagation();

            const target = openTrigger.dataset.target ||
                           openTrigger.getAttribute('data-modal') ||
                           openTrigger.getAttribute('data-modal-id');

            if (target) {
                const modalId = target.startsWith('#') ? target.slice(1) : target;
                if (typeof window.openModal === 'function') {
                    window.openModal(modalId);
                }
            }
            return;
        }

        // nutup modal pas klik tombol close
        if (event.target.classList.contains('tutup-modal') || 
            event.target.closest('.tutup-modal')) {
            event.preventDefault();
            event.stopPropagation();
            
            const modal = event.target.closest('.modal-backdrop');
            if (modal && modal.id) {
                closeModal(modal.id);
            }
            return;
        }
        
        // nutup modal kalo klik area backdrop
        if (event.target.classList.contains('modal-backdrop')) {
            event.preventDefault();
            event.stopPropagation();
            
            if (event.target.id) {
                closeModal(event.target.id);
            }
            return;
        }
    });

    // tutup modal lewat tombol esc
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-backdrop:not(.hidden)');
            if (openModal && openModal.id) {
                closeModal(openModal.id);
            }
        }
    });
});

// fungsi global buka modal
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // fokus ke input pertama kalo ada
        const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
};

// fungsi global nutup modal
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';

    const form = modal.querySelector('form');
    if (form) {
        form.reset();

        const errorElements = form.querySelectorAll('.pesan-error');
        errorElements.forEach(el => el.textContent = '');

        const inputElements = form.querySelectorAll('input, textarea, select');
        inputElements.forEach(el => {
            el.classList.remove('border-red-500', 'border-green-500');
        });
    }
};
</script>
@endpush
@endonce
