@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Profile Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center space-x-6">
                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-2xl font-medium text-gray-700" id="profile-avatar-initial">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900" id="profile-header-name">{{ Auth::user()->name }}</h1>
                        <p class="text-gray-600" id="profile-header-email">{{ Auth::user()->email }}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ Auth::user()->role === 'gudang' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }} mt-1">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Profile Form -->
        <x-form.wrapper 
        id="update-profile-form"
        method="PUT"
        action="{{ route('profile.update') }}"
        title="Update Profile Information"
        submit-text="Update Profile"
        :use-validation="true"
        class="ajax-form">            
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input 
                    name="name" 
                    label="Nama Lengkap" 
                    :value="Auth::user()->name"
                    placeholder="Masukkan nama lengkap"
                    required />

                <x-form.input 
                    name="email" 
                    type="email"
                    label="Email Address" 
                    :value="Auth::user()->email"
                    placeholder="Masukkan email"
                    required />
            </div>
        </x-form.wrapper>

        <!-- Change Password Form -->
        <x-form.wrapper 
        id="update-password-form"
        method="PUT"
        action="{{ route('profile.password') }}"
        title="Update Password"
        submit-text="Update Password"
        :use-validation="true"
        class="ajax-form">
            
            <div class="space-y-6">
                <x-form.input 
                    name="current_password" 
                    type="password"
                    label="Current Password" 
                    placeholder="Masukkan password saat ini"
                    required />

                <x-form.input 
                    name="password" 
                    type="password"
                    label="New Password" 
                    placeholder="Masukkan password baru"
                    help="Password minimal 6 karakter"
                    required />

                <x-form.input 
                    name="password_confirmation" 
                    type="password"
                    label="Confirm New Password" 
                    placeholder="Konfirmasi password baru"
                    required />
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-400 mt-0.5" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Security Notice</h3>
                        <p class="mt-1 text-sm text-yellow-700">
                            Setelah mengubah password, Anda akan diminta untuk login ulang di semua device.
                        </p>
                    </div>
                </div>
            </div>
        </x-form.wrapper>
    </div>
</div>

@push('scripts')
<script>
// validasi santai buat cek konfirmasi password
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('update-password-form');
    if (form) {
        const passwordInput = form.querySelector('[name="password"]');
        const confirmPasswordInput = form.querySelector('[name="password_confirmation"]');
        
        const validatePasswordConfirmation = () => {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const errorElement = confirmPasswordInput.nextElementSibling;
            
            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.classList.add('border-red-500');
                confirmPasswordInput.classList.remove('border-green-500');
                if (errorElement && errorElement.classList.contains('pesan-error')) {
                    errorElement.textContent = 'Konfirmasi password tidak cocok.';
                }
                return false;
            } else if (confirmPassword) {
                confirmPasswordInput.classList.remove('border-red-500');
                confirmPasswordInput.classList.add('border-green-500');
                if (errorElement && errorElement.classList.contains('pesan-error')) {
                    errorElement.textContent = '';
                }
                return true;
            }
            return true;
        };
        
        confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
        passwordInput.addEventListener('input', validatePasswordConfirmation);
        
        form.addEventListener('submit', function(event) {
            if (!validatePasswordConfirmation()) {
                event.preventDefault();
                confirmPasswordInput.focus();
                if (window.showNotification) {
                    window.showNotification('Konfirmasi password tidak cocok.', 'error');
                }
            }
        });
    }
});
</script>
@endpush
@endsection
