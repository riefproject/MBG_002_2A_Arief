<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ config('app.name') }}</h1>
        </div>

        <!-- Login Form -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="email" 
                           class="input-wajib-validasi p-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm mt-1 block w-full" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username" />
                    <div class="pesan-error text-red-500 text-sm mt-1">
                        @error('email'){{ $message }}@enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                    <input id="password" 
                           class="input-wajib-validasi p-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm mt-1 block w-full"
                           type="password"
                           name="password"
                           required 
                           autocomplete="current-password" />
                    <div class="pesan-error text-red-500 text-sm mt-1">
                        @error('password'){{ $message }}@enderror
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Masuk
                    </button>
                </div>
            </form>
        </div>

        <!-- Demo Users Info -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg text-sm text-blue-700 max-w-md">
            <h3 class="font-medium mb-2">Demo Users:</h3>
            <div class="space-y-1">
                <div><strong>Admin:</strong> admin@example.com / password</div>
                <div><strong>User:</strong> user@example.com / password</div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Client-side validation untuk login form
        const form = document.getElementById('login-form');
        if (form) {
            const inputs = form.querySelectorAll('.input-wajib-validasi');
            
            const validateInput = (input) => {
                const pesanError = input.nextElementSibling;
                let isValid = true;
                let errorMessage = '';
                
                // Validasi berdasarkan type input
                if (input.hasAttribute('required') && input.value.trim() === '') {
                    isValid = false;
                    errorMessage = 'Field ini tidak boleh kosong.';
                } else if (input.type === 'email' && input.value && !isValidEmail(input.value)) {
                    isValid = false;
                    errorMessage = 'Format email tidak valid.';
                }
                
                // Update tampilan
                if (isValid) {
                    input.classList.remove('border-red-500', 'border-red-300');
                    input.classList.add('border-green-500');
                    if (pesanError) pesanError.textContent = '';
                } else {
                    input.classList.remove('border-green-500');
                    input.classList.add('border-red-500');
                    if (pesanError && !pesanError.textContent) {
                        pesanError.textContent = errorMessage;
                        pesanError.className = 'text-red-500 text-sm mt-1';
                    }
                }
                
                return isValid;
            };
            
            // Helper function untuk validasi email
            const isValidEmail = (email) => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            };
            
            // Real-time validation saat user mengetik
            inputs.forEach(input => {
                input.addEventListener('input', () => validateInput(input));
                input.addEventListener('blur', () => validateInput(input));
            });
            
            // Validasi saat submit
            form.addEventListener('submit', (event) => {
                let isFormValid = true;
                
                inputs.forEach(input => {
                    if (!validateInput(input)) {
                        isFormValid = false;
                    }
                });
                
                if (!isFormValid) {
                    event.preventDefault();
                    
                    // Focus ke input pertama yang error
                    const firstErrorInput = form.querySelector('.border-red-500');
                    if (firstErrorInput) {
                        firstErrorInput.focus();
                    }
                }
            });
        }
    </script>
    @endpush
</body>
</html>