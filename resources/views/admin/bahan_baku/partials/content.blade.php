<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Bahan Baku</h1>
                    <p class="text-gray-600">Kelola stok dan status bahan baku</p>
                </div>
                <div class="sm:hidden">
                    @include('admin.bahan_baku.partials.create-button')
                </div>
            </div>
        </div>

        <x-table.wrapper 
            id="tabel-bahan-baku"
            :headers="['Nama', 'Kategori', 'Jumlah', 'Satuan', 'Tanggal Masuk', 'Tanggal Kadaluarsa', 'Status', 'Aksi']"
            empty-message="Belum ada bahan baku yang terdaftar"
        >
            <x-slot name="createButton">
                <div class="hidden sm:block">
                    @include('admin.bahan_baku.partials.create-button')
                </div>
            </x-slot>

            @forelse($bahanBakus as $bb)
                <tr class="hover:bg-gray-50" data-bahan-baku-id="{{ $bb['id'] }}">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $bb['nama'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb['kategori'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb['jumlah'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb['satuan'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb['tanggal_masuk'] ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bb['tanggal_kadaluarsa'] ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="capitalize">{{ $bb['status_label'] }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-table.actions 
                            view-url="{{ $bb['view_url'] }}"
                            edit-url="{{ $bb['edit_url'] }}"
                            :edit-data="[
                                'id' => $bb['id'],
                                'nama' => $bb['nama'],
                                'kategori' => $bb['kategori'],
                                'jumlah' => $bb['jumlah'],
                                'satuan' => $bb['satuan'],
                                'jumlah_label' => $bb['jumlah_label'],
                                'status' => $bb['status'],
                                'status_label' => $bb['status_label'],
                                'tanggal_masuk_label' => $bb['tanggal_masuk'] ?? '-',
                                'tanggal_kadaluarsa_label' => $bb['tanggal_kadaluarsa'] ?? '-',
                            ]"
                            delete-url="{{ $bb['delete_url'] }}"
                            :delete-name="$bb['nama']"
                            :delete-id="$bb['id']"
                            :delete-data="[
                                'kategori' => $bb['kategori'],
                                'jumlah' => $bb['jumlah'],
                                'satuan' => $bb['satuan'],
                                'jumlah_label' => $bb['jumlah_label'],
                                'status' => $bb['status'],
                                'status_label' => $bb['status_label'],
                                'tanggal_masuk_label' => $bb['tanggal_masuk'] ?? '-',
                                'tanggal_kadaluarsa_label' => $bb['tanggal_kadaluarsa'] ?? '-',
                                'tanggal_kadaluarsa_iso' => $bb['tanggal_kadaluarsa_iso'],
                                'can_delete' => $bb['can_delete'] ? 'true' : 'false',
                            ]"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        Belum ada bahan baku yang terdaftar.
                    </td>
                </tr>
            @endforelse
        </x-table.wrapper>
    </div>
</div>

@include('admin.bahan_baku.partials.modals', ['showCreate' => true])
