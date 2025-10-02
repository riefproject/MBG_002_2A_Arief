{{-- Partial view untuk konten admin bahan baku tanpa layout --}}
<div class="py-12">
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

        <!-- Bahan Baku Table -->
        <x-table.wrapper 
            id="tabel-bahan-baku"
            :headers="['Nama', 'Kategori', 'Jumlah', 'Satuan', 'Tanggal Masuk', 'Tanggal Kadaluarsa', 'Status', 'Aksi']"
            empty-message="Belum ada bahan baku yang terdaftar">
            @foreach(App\Models\BahanBaku::latest()->get() as $bb)
            @php
                $status = \App\Models\BahanBaku::determineStatus($bb->jumlah, $bb->tanggal_kadaluarsa);
                $statusLabel = str_replace('_', ' ', $status);
                $tanggalMasukLabel = $bb->tanggal_masuk ? $bb->tanggal_masuk->format('d M Y') : '-';
                $tanggalKadaluarsaLabel = $bb->tanggal_kadaluarsa ? $bb->tanggal_kadaluarsa->format('d M Y') : '-';
            @endphp
            <tr class="hover:bg-gray-50" data-bahan-baku-id="{{ $bb->id }}">
                <td class="px-6 py-4 whitespace-nowrap">{{ $bb->nama }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb->kategori }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb->jumlah }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb->satuan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tanggalMasukLabel }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tanggalKadaluarsaLabel }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="capitalize">{{ $statusLabel }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-table.actions 
                        view-url="{{ route('admin.bahan_baku.show', $bb) }}"
                        edit-url="{{ route('admin.bahan_baku.update', $bb) }}"
                        :edit-data="[
                            'id' => $bb->id,
                            'nama' => $bb->nama,
                            'kategori' => $bb->kategori,
                            'jumlah' => $bb->jumlah,
                            'satuan' => $bb->satuan,
                            'jumlah_label' => $bb->jumlah . ' ' . $bb->satuan,
                            'status' => $status,
                            'status_label' => $statusLabel,
                            'tanggal_masuk_label' => $tanggalMasukLabel,
                            'tanggal_kadaluarsa_label' => $tanggalKadaluarsaLabel,
                        ]"
                        delete-url="{{ route('admin.bahan_baku.destroy', $bb) }}"
                        :delete-name="$bb->nama"
                        :delete-id="$bb->id"
                        :delete-data="[
                            'kategori' => $bb->kategori,
                            'jumlah' => $bb->jumlah,
                            'satuan' => $bb->satuan,
                            'jumlah_label' => $bb->jumlah . ' ' . $bb->satuan,
                            'status' => $status,
                            'status_label' => $statusLabel,
                            'tanggal_masuk_label' => $tanggalMasukLabel,
                            'tanggal_kadaluarsa_label' => $tanggalKadaluarsaLabel,
                            'can_delete' => $status === 'kadaluarsa' ? 'true' : 'false',
                        ]" />
                </td>
            </tr>
            @endforeach
        </x-table.wrapper>
    </div>
</div>

<!-- Modal Tambah Bahan Baku -->
<x-modal.base id="modal-tambah" title="Tambah Bahan Baku Baru" size="md">
    <form method="POST" action="{{ route('admin.bahan_baku.store') }}" id="form-tambah" class="ajax-form" data-entity="bahan-baku">
        @csrf
        <x-form.input 
            name="nama" 
            label="Nama Bahan Baku" 
            placeholder="Masukkan nama bahan baku"
            required />

        <x-form.input 
            name="kategori" 
            label="Kategori" 
            placeholder="Masukkan kategori"
            required />

        <x-form.input 
            name="jumlah" 
            type="number"
            label="Jumlah" 
            placeholder="Masukkan jumlah"
            min="0"
            required />

        <x-form.input 
            name="satuan" 
            label="Satuan" 
            placeholder="Masukkan satuan (misal: kg, liter, pcs)"
            required />

        <x-form.input 
            name="tanggal_masuk" 
            type="date"
            label="Tanggal Masuk" 
            placeholder="Masukkan tanggal masuk"
            required />

        <x-form.input 
            name="tanggal_kadaluarsa" 
            type="date"
            label="Tanggal Kadaluarsa" 
            placeholder="Masukkan tanggal kadaluarsa"
            required />

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    class="tutup-modal mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Simpan Bahan Baku
            </button>
        </div>
    </form>
</x-modal.base>

<!-- Modal Update Stok Bahan Baku -->
<x-modal.base id="modal-edit" title="Update Stok Bahan Baku" size="md">
    <form method="POST" action="" id="form-edit" class="ajax-form" data-entity="bahan-baku">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</p>
                    <p class="mt-1 font-medium text-gray-900" data-field="nama">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</p>
                    <p class="mt-1 text-gray-900" data-field="kategori">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Masuk</p>
                    <p class="mt-1 text-gray-900" data-field="tanggalMasukLabel">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Kadaluarsa</p>
                    <p class="mt-1 text-gray-900" data-field="tanggalKadaluarsaLabel">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status Saat Ini</p>
                    <p class="mt-1 capitalize text-gray-900" data-field="statusLabel">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Saat Ini</p>
                    <p class="mt-1 text-gray-900" data-field="jumlahLabel">-</p>
                </div>
            </div>

            <x-form.input 
                name="jumlah" 
                type="number"
                label="Jumlah Baru" 
                placeholder="Masukkan jumlah stok terbaru"
                min="0"
                required />
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    class="tutup-modal mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Update Stok
            </button>
        </div>
    </form>
</x-modal.base>

<!-- Modal Konfirmasi Hapus -->
<x-modal.base id="modal-hapus" title="Konfirmasi Hapus" size="sm">
    <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        
        <h3 class="mt-2 text-lg font-medium text-gray-900">Hapus Bahan Baku</h3>
        <p class="mt-2 text-sm text-gray-500">
            Apakah Anda yakin ingin menghapus bahan baku <strong id="nama-item"></strong>? 
            Tindakan ini tidak dapat dibatalkan.
        </p>

    <form method="POST" action="" id="form-hapus" class="mt-4 ajax-form" data-entity="bahan-baku">
        @csrf
        @method('DELETE')

        <div class="text-left bg-gray-50 border border-gray-200 rounded-md px-4 py-3 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Kategori</span>
                <span class="font-semibold text-gray-900" data-delete-field="kategori">-</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Jumlah</span>
                <span class="font-semibold text-gray-900" data-delete-field="jumlahLabel">-</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Status</span>
                <span class="font-semibold text-gray-900 capitalize" data-delete-field="statusLabel">-</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Tanggal Masuk</span>
                <span class="text-gray-900" data-delete-field="tanggalMasukLabel">-</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Kadaluarsa</span>
                <span class="text-gray-900" data-delete-field="tanggalKadaluarsaLabel">-</span>
            </div>
        </div>

        <div class="hidden mt-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md px-4 py-3" data-delete-warning></div>

        <div class="mt-5 flex items-center justify-center space-x-3">
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
