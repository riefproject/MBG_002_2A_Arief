@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Bahan Baku</h2>
                <p class="mt-1 text-sm text-gray-500">Informasi lengkap bahan baku dan aksi lanjutan.</p>
            </div>

            <dl class="divide-y divide-gray-200 text-sm text-gray-600">
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Nama</dt>
                    <dd class="text-gray-900" id="bb-nama">{{ $bahanBaku->nama }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Kategori</dt>
                    <dd class="text-gray-900" id="bb-kategori">{{ $bahanBaku->kategori }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Jumlah</dt>
                    <dd class="text-gray-900" id="bb-jumlah">{{ $bahanBakuData['jumlah_label'] ?? ($bahanBaku->jumlah . ' ' . $bahanBaku->satuan) }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Tanggal Masuk</dt>
                    <dd class="text-gray-900" id="bb-tanggal-masuk">{{ $bahanBakuData['tanggal_masuk'] ?? optional($bahanBaku->tanggal_masuk)->format('d M Y') ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Tanggal Kadaluarsa</dt>
                    <dd class="text-gray-900" id="bb-tanggal-kadaluarsa">{{ $bahanBakuData['tanggal_kadaluarsa'] ?? optional($bahanBaku->tanggal_kadaluarsa)->format('d M Y') ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Status</dt>
                    <dd class="text-gray-900 capitalize" id="bb-status">{{ $bahanBakuData['status_label'] ?? str_replace('_', ' ', $bahanBaku->status) }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium text-gray-700">Dibuat pada</dt>
                    <dd class="text-gray-900">{{ optional($bahanBaku->created_at)->format('d M Y H:i') ?? '-' }}</dd>
                </div>
            </dl>

            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('admin.bahan_baku.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Kembali
                </a>
                @if(!empty($bahanBakuData))
                    <x-table.actions
                        :view-url="null"
                        edit-url="{{ $bahanBakuData['edit_url'] }}"
                        :edit-data="[
                            'id' => $bahanBakuData['id'],
                            'nama' => $bahanBakuData['nama'],
                            'kategori' => $bahanBakuData['kategori'],
                            'jumlah' => $bahanBakuData['jumlah'],
                            'satuan' => $bahanBakuData['satuan'],
                            'jumlah_label' => $bahanBakuData['jumlah_label'],
                            'status' => $bahanBakuData['status'],
                            'status_label' => $bahanBakuData['status_label'],
                            'tanggal_masuk_label' => $bahanBakuData['tanggal_masuk'] ?? '-',
                            'tanggal_kadaluarsa_label' => $bahanBakuData['tanggal_kadaluarsa'] ?? '-',
                        ]"
                        delete-url="{{ $bahanBakuData['delete_url'] }}"
                        :delete-name="$bahanBakuData['nama']"
                        :delete-id="$bahanBakuData['id']"
                        :delete-data="[
                            'kategori' => $bahanBakuData['kategori'],
                            'jumlah' => $bahanBakuData['jumlah'],
                            'satuan' => $bahanBakuData['satuan'],
                            'jumlah_label' => $bahanBakuData['jumlah_label'],
                            'status' => $bahanBakuData['status'],
                            'status_label' => $bahanBakuData['status_label'],
                            'tanggal_masuk_label' => $bahanBakuData['tanggal_masuk'] ?? '-',
                            'tanggal_kadaluarsa_label' => $bahanBakuData['tanggal_kadaluarsa'] ?? '-',
                            'tanggal_kadaluarsa_iso' => $bahanBakuData['tanggal_kadaluarsa_iso'],
                            'can_delete' => !empty($bahanBakuData['can_delete']) ? 'true' : 'false',
                        ]"
                    />
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.bahan_baku.partials.modals', ['showCreate' => false])
@endsection
@push('scripts')
<script>
// update detail kalau stok bahan diubah/diupdate
document.addEventListener('bahan-baku:updated', function(event) {
    var data = event.detail || {};
    if (!data || !data.id || data.id !== {{ $bahanBaku->id }}) {
        return;
    }

    var helpers = {
        text: function(id, value) {
            var el = document.getElementById(id);
            if (el && value !== undefined && value !== null) {
                el.textContent = value;
            }
        }
    };

    helpers.text('bb-nama', data.nama);
    helpers.text('bb-kategori', data.kategori);
    helpers.text('bb-jumlah', data.jumlah_label);
    helpers.text('bb-tanggal-masuk', data.tanggal_masuk_label || data.tanggal_masuk || '-');
    helpers.text('bb-tanggal-kadaluarsa', data.tanggal_kadaluarsa_label || data.tanggal_kadaluarsa || '-');
    helpers.text('bb-status', data.status_label || data.status);
});
</script>
@endpush

