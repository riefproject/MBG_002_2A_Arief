@extends('layouts.app')

@section('content')
<div class="py-12" data-spa-content>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manajemen Bahan Baku</h1>
                        <p class="text-gray-600">Kelola semua kebutuhan bahan baku</p>
                    </div>
                    <button data-action="open-modal" data-target="modal-tambah"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Bahan Baku
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <x-table.wrapper 
            :headers="['id', 'Name', 'Kategori', 'Jumlah', 'Satuan', 'Tanggal Masuk', 'Tanggal Kadaluarsa', 'status', 'created at']"
            empty-message="Belum ada bahan baku yang terdaftar">
            
            @foreach(App\Models\BahanBaku::latest()->get() as $bb)
            <tr class="hover:bg-gray-50" data-user-id="{{ $bb->id }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($bb->nama, 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 user-name">{{ $bb->nama }}</div>
                        </div>
                    </div>
                </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->kategori }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->jumlah }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->satuan }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->status }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->tanggal_masuk }}
                    <div class="text-xs text-gray-400">{{ $bb->created_at->diffForHumans() }}</div>
                </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $bb->tanggal_kadaluarsa->format('d M Y') }}
                    <div class="text-xs text-gray-400">{{ $bb->created_at->diffForHumans() }}</div>
                </td>
        <td class="px-6 py-4 whitespace-nowrap">
                    <x-table.actions 
                        edit-url="{{ route('admin.users.update', $bb) }}"
                        :edit-data="[
                            'name' => $bb->nama,
                            'email' => $bb->email,
                            'role' => $bb->role
                        ]"
                        delete-url="{{ route('admin.users.destroy', $bb) }}"
                        :delete-name="$bb->nama"
                        :delete-id="$bb->id" />
                </td>
            </tr>
            @endforeach
        </x-table.wrapper>
    </div>
</div>

<!-- Modal Tambah User -->
<x-modal.base id="modal-tambah" title="Tambah User Baru" size="md">
    <div class="modal-content-compact">
    <form method="POST" action="{{ route('admin.users.store') }}" id="form-tambah" class="ajax-form">
        @csrf
        
        <x-form.input 
            name="name" 
            label="Nama Lengkap" 
            placeholder="Masukkan nama lengkap"
            required />

        <x-form.input 
            name="email" 
            type="email"
            label="Email" 
            placeholder="Masukkan email"
            required />

        <x-form.input 
            name="password" 
            type="password"
            label="Password" 
            placeholder="Masukkan password"
            required />

        <div class="mb-3">
            <label for="role" class="block font-medium text-sm text-gray-700 mb-1">
                Role <span class="text-red-500">*</span>
            </label>
            <select name="role" 
                    id="role" 
                    class="input-wajib-validasi border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
                    required>
                <option value="">Pilih Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <div class="pesan-error text-red-500 text-sm mt-1"></div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    class="tutup-modal mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Simpan User
            </button>
        </div>
    </form>
    </div>
</x-modal.base>

<!-- Modal Edit User -->
<x-modal.base id="modal-edit" title="Edit User" size="md">
    <div class="modal-content-compact">
    <form method="POST" action="" id="form-edit" class="ajax-form">
        @csrf
        @method('PUT')
        
        <x-form.input 
            name="name" 
            label="Nama Lengkap" 
            placeholder="Masukkan nama lengkap"
            required />

        <x-form.input 
            name="email" 
            type="email"
            label="Email" 
            placeholder="Masukkan email"
            required />

        <div class="mb-3">
            <label for="edit_role" class="block font-medium text-sm text-gray-700 mb-1">
                Role <span class="text-red-500">*</span>
            </label>
            <select name="role" 
                    id="edit_role" 
                    class="input-wajib-validasi border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
                    required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <div class="pesan-error text-red-500 text-sm mt-1"></div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    class="tutup-modal mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Update User
            </button>
        </div>
    </form>
    </div>
</x-modal.base>

<!-- Modal Konfirmasi Hapus -->
<x-modal.base id="modal-hapus" title="Konfirmasi Hapus" size="sm">
    <div class="text-center">
        <svg class="mx-auto h-10 w-10 text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        
        <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus User</h3>
        <p class="text-sm text-gray-500 mb-4">
            Apakah Anda yakin ingin menghapus user <strong id="nama-item"></strong>? 
            Tindakan ini tidak dapat dibatalkan.
        </p>

        <form method="POST" action="" id="form-hapus" class="mt-4 ajax-form">
            @csrf
            @method('DELETE')
            
            <div class="flex items-center justify-center space-x-3">
                <button type="button" 
                        class="tutup-modal inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Batal
                </button>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</x-modal.base>

@push('scripts')
<script>
// Modal functions (ensure they're available)
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

// Event delegation untuk membuka modal
document.body.addEventListener('click', function(event) {
    if (event.target.dataset.action === 'open-modal') {
        const targetModal = event.target.dataset.target;
        if (targetModal) {
            openModal(targetModal);
        }
    }
});

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

</script>
@endpush
@endsection