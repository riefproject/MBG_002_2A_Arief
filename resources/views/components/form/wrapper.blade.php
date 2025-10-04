@props([
    'id' => '',
    'method' => 'POST',
    'action' => '',
    'title' => '',
    'submitText' => 'Simpan',
    'cancelUrl' => null,
    'useValidation' => true,
    'class' => ''
])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        @if($title)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ $title }}</h2>
            </div>
        @endif

        <form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" 
              action="{{ $action }}" 
              id="{{ $id }}"
              class="{{ $useValidation ? 'form-dengan-validasi' : '' }} {{ $class }}">
            @csrf
            @if(!in_array(strtoupper($method), ['GET', 'POST']))
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                @if($cancelUrl)
                    <a href="{{ $cancelUrl }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Batal
                    </a>
                @endif
                
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $submitText }}
                </button>
            </div>
        </form>
    </div>
</div>

@if($useValidation)
@push('scripts')
<script>
// auto validasi simple buat form 'form-dengan-validasi'
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('{{ $id }}');
    if (form && form.classList.contains('form-dengan-validasi')) {
        const inputs = form.querySelectorAll('.input-wajib-validasi');
        
        const validateInput = (input) => {
            const pesanError = input.nextElementSibling;
            let isValid = true;
            let errorMessage = '';
            
            // cek validasi sesuai tipe input
            if (input.hasAttribute('required') && input.value.trim() === '') {
                isValid = false;
                errorMessage = 'Field ini tidak boleh kosong.';
            } else if (input.type === 'email' && input.value && !isValidEmail(input.value)) {
                isValid = false;
                errorMessage = 'Format email tidak valid.';
            } else if (input.type === 'password' && input.value && input.value.length < 6) {
                isValid = false;
                errorMessage = 'Password minimal 6 karakter.';
            }
            
            // update tampilan sesuai hasil
            if (isValid) {
                input.classList.remove('border-red-500', 'border-red-300');
                input.classList.add('border-green-500');
                if (pesanError) pesanError.textContent = '';
            } else {
                input.classList.remove('border-green-500');
                input.classList.add('border-red-500');
                if (pesanError && pesanError.classList.contains('pesan-error')) {
                    pesanError.textContent = errorMessage;
                    pesanError.className = 'pesan-error text-red-500 text-sm mt-1';
                }
            }
            
            return isValid;
        };
        
        // helper cek format email
        const isValidEmail = (email) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        };
        
        // validasi realtime pas user ngetik
        inputs.forEach(input => {
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
        });
        
        // validasi ulang pas submit
        form.addEventListener('submit', (event) => {
            let isFormValid = true;
            
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isFormValid = false;
                }
            });
            
            if (!isFormValid) {
                event.preventDefault();
                
                // fokus ke input pertama yg error
                const firstErrorInput = form.querySelector('.border-red-500');
                if (firstErrorInput) {
                    firstErrorInput.focus();
                    firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // munculin notif biar user tau
                if (window.showNotification) {
                    window.showNotification('Harap perbaiki semua error sebelum submit.', 'error');
                }
            }
        });
    }
});
</script>
@endpush
@endif
